<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\CreateLoyaltyRequest;
use App\Http\Requests\UpdateLoyaltyRequest;
use App\Repositories\LoyaltyRepository;
use App\Repositories\RewardRepository;
use Illuminate\Http\Request;
use Response;

class LoyaltyController extends BaseController
{
    /**
     * @var LoyaltyRepository $loyaltyRepository
     */
    protected $loyaltyRepository;

    /**
     * @var RewardRepository $rewardRepository
     */
    protected $rewardRepository;

    /**
     * LoyaltyController constructor.
     * @param LoyaltyRepository $loyaltyRepo
     * @param RewardRepository $rewardRepo
     */
    public function __construct(LoyaltyRepository $loyaltyRepo, RewardRepository $rewardRepo)
    {
        parent::__construct();

        $this->loyaltyRepository = $loyaltyRepo;
        $this->rewardRepository = $rewardRepo;
    }

    /**
     * @overwrite
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function callAction($method, $parameters)
    {
        $host = $parameters[0]->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        /** @var \App\Models\Workspace $workspace */
        $workspace = session('workspace_'.$workspaceSlug);
        $workspaceExtras = $workspace->workspaceExtras;
        $allowLoyalties = $workspaceExtras
                ->where('type', \App\Models\WorkspaceExtra::CUSTOMER_CARD)
                ->where('active', true)
                ->count() > 0;

        // Redirect to home page if Admin didn't give permission
        if (!$allowLoyalties) {
            return redirect('/');
        }

        return parent::callAction($method, $parameters);
    }

    /**
     * Display a listing of the Loyalty.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \Auth::user();
        // JWT token
        $token = \JWTAuth::fromUser($user);
        /** @var \App\Models\Workspace $workspace */
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug)->refresh();

        // Get my loyalty in a restaurant
        $loyalty = $this->loyaltyRepository
            ->firstOrCreate([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
            ]);

        // Get all rewards from a restaurant
        /** @var \Illuminate\Support\Collection $rewards */
        $rewards = $this->rewardRepository
            ->where('workspace_id', $workspace->id)
            ->where('expire_date', '>', \Carbon\Carbon::now())
            ->orderBy('score', 'ASC')
            ->get();

        $rewardIds = $rewards->pluck('id')->toArray();

        // Check if usage or not
        $usedRewards = $this->loyaltyRepository->checkUsage($loyalty, $rewardIds);

        $rewards->transform(function ($reward) use ($usedRewards) {
            $reward->is_used = array_get($usedRewards, $reward->id, false);

            return $reward;
        });

        // Get max reward by score
        $rewardMax = $this->rewardRepository
            ->orderBy('score', 'DESC')
            ->firstOrNew([
                'workspace_id' => $workspace->id
            ]);

        if ($request->ajax()) {
            $wrapLoyaltyHtml = view('web.loyalties.partials.wrap_page_card')->with([
                'loyalty' => $loyalty,
                'workspace' => $workspace,
                'rewards' => $rewards,
                'rewardMax' => $rewardMax,
                'token' => $token,
            ])->render();

            return $this->sendResponse(['wrapLoyaltyHtml' => $wrapLoyaltyHtml], trans('workspace.successfully'));
        }

        return view('web.loyalties.index')
            ->with([
                'loyalty' => $loyalty,
                'workspace' => $workspace,
                'rewards' => $rewards,
                'rewardMax' => $rewardMax,
                'token' => $token,
            ]);
    }
}
