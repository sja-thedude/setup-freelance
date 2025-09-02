<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Requests\CreateContactRequest;
use App\Http\Requests\CreatePortalContactRequest;
use App\Models\Email;
use App\Models\Workspace;
use App\Repositories\ContactRepository;
use Illuminate\Http\Request;
use Flash;
use Mail;

class ContactController extends BaseController
{
    /** @var  ContactRepository */
    private $contactRepository;

    public function __construct(ContactRepository $contactRepo)
    {
        parent::__construct();
        $this->contactRepository = $contactRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $host = $request->getHost();
        $workspaceSlug = \App\Helpers\Helper::getSubDomainOfRequest($host);
        $workspace = session('workspace_'.$workspaceSlug);
        $categories = $workspace->getListCategories();
        $userId = !auth()->guest() ? auth()->user()->id : NULL;
        $workspaceId = $workspace->id;
        // get setting order type
        $is_takeout = false;
        $is_delivery = false;
        $orderTypes = \App\Models\SettingOpenHour::where(['workspace_id' => $workspaceId, 'active' => 1])->get()->toArray();

        foreach ($orderTypes as $orderType) {
            if ($orderType['type'] == 0) {
                $is_takeout = true;
            }

            if ($orderType['type'] == 1) {
                $is_delivery = true;
            }
        }

        return view($this->guard . '.contact.index', compact(
            'workspaceId',
            'workspace',
            'categories',
            'userId',
            'is_takeout',
            'is_delivery'
        ));
    }

    /**
     * @param CreateContactRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateContactRequest $request)
    {
        $workspaceId = $request->workspaceId;
        $input = $request->all();
        $input['workspace_id'] = $workspaceId;
        $fullName = explode(' ', $input['name']);

        if (count($fullName) > 1) {
            $input['first_name'] = count($fullName) > 1 ? $fullName[0] : null;
            $input['last_name'] = count($fullName) > 1 ? $fullName[1] : null;
        } else {
            $input['first_name'] = $input['name'];
        }

        $contact = $this->contactRepository->create($input);

        Flash::success(trans('contact.message_saved_successfully'));

        return redirect(route($this->guard . '.contact.show', [$contact->id]));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $workspaceId = $request->workspaceId;
        $workspace = Workspace::find($workspaceId);
        $categories = $workspace->getListCategories();
        $contact = $this->contactRepository->findWithoutFail($id);
        $userId = !auth()->guest() ? auth()->user()->id : NULL;

        if (empty($contact)) {
            Flash::error('Contact not found');

            return redirect(route($this->guard . '.contact.index'));
        }

        return view($this->guard . '.contact.index')->with(compact(
            'contact',
            'workspace',
            'categories',
            'userId'
        ));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function portalContact()
    {
        $headerTitleKey = 'frontend_contact';
        $headerTitle = trans('frontend.contact');

        return view($this->guard . '.contact.portal_index')->with(compact(
            'headerTitle',
            'headerTitleKey'
        ));
    }

    /**
     * @param CreatePortalContactRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function portalStore(CreatePortalContactRequest $request)
    {
        $rawContent = view('layouts.emails.portal_contact', ['contact' => $request->all()])->render();
        $to = in_array(config('app.env'), ['stag', 'prod']) ? 'info@itsready.be' : env('DEVELOPER_EMAIL', 'info@itsready.be');
        Email::create([
            'to' => $to,
            'subject' => trans('frontend.contact_subject'),
            'content' => $rawContent,
            'locale' => \App::getLocale(),
            'location' => json_encode([
                'id' => ContactController::class,
            ])
        ]);
        Mail::send('layouts.emails.portal_contact', [
            'contact' => $request->all()
        ], function ($m) use ($to) {
            // Get mail sender from workspace config
            $fromMail = config('mail.from.address');
            $fromName = config('mail.from.name');

            $m->from($fromMail, $fromName);
            $m->to($to, 'ItsReady')
                ->subject(trans('frontend.contact_subject'));
        });
        Flash::success(trans('frontend.contact_send_success'));

        return redirect(route($this->guard.'.contact.portalContact')."#send-success");
    }
}
