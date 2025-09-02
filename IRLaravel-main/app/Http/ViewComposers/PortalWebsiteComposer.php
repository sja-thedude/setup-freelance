<?php
namespace App\Http\ViewComposers;

use App\Models\Workspace;
use App\Repositories\WorkspaceRepository;
use Illuminate\View\View;

class PortalWebsiteComposer
{
    /**
     * @var WorkspaceRepository
     */
    protected $workspaceRepository;

    public function __construct(
        WorkspaceRepository $workspaceRepository
    ){
        $this->workspaceRepository = $workspaceRepository;
    }

    public function compose(View $view)
    {
        $restaurants = $this->workspaceRepository->getRestaurantsByDistance([true], NULL, ['orderType' => Workspace::CREATED_AT], 5);
        $view->with(compact('restaurants'));
    }
}