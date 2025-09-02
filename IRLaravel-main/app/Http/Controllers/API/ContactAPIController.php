<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateContactAPIRequest;
use App\Http\Requests\API\UpdateContactAPIRequest;
use App\Models\Contact;
use App\Repositories\ContactRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ContactController
 * @package App\Http\Controllers\API
 */
class ContactAPIController extends AppBaseController
{
    /**
     * @var ContactRepository $contactRepository
     */
    private $contactRepository;

    public function __construct(ContactRepository $contactRepo)
    {
        parent::__construct();

        $this->contactRepository = $contactRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->contactRepository->pushCriteria(new RequestCriteria($request));
            $this->contactRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        // $limit = limit or per_page
        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $contacts = $this->contactRepository->paginate($limit);

        return $this->sendResponse($contacts->toArray(), trans('contact.message_retrieved_list_successfully'));
    }

    /**
     * @param CreateContactAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateContactAPIRequest $request)
    {
        $input = $request->all();

        $contacts = $this->contactRepository->create($input);

        return $this->sendResponse($contacts->toArray(), trans('contact.message_saved_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Contact $contact */
        $contact = $this->contactRepository->findWithoutFail($id);

        if (empty($contact)) {
            return $this->sendError('Contact not found');
        }

        return $this->sendResponse($contact->toArray(), trans('contact.message_retrieved_successfully'));
    }

    /**
     * @param UpdateContactAPIRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateContactAPIRequest $request, $id)
    {
        $input = $request->all();

        /** @var Contact $contact */
        $contact = $this->contactRepository->findWithoutFail($id);

        if (empty($contact)) {
            return $this->sendError('Contact not found');
        }

        $contact = $this->contactRepository->update($input, $id);

        return $this->sendResponse($contact->toArray(), trans('contact.message_updated_successfully'));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var Contact $contact */
        $contact = $this->contactRepository->findWithoutFail($id);

        if (empty($contact)) {
            return $this->sendError('Contact not found');
        }

        $contact->delete();

        return $this->sendResponse($id, trans('contact.message_deleted_successfully'));
    }

    /**
     * Send contact to admin
     *
     * @param CreateContactAPIRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toAdmin(CreateContactAPIRequest $request)
    {
        $input = $request->all();

        $contacts = $this->contactRepository->create($input);

        return $this->sendResponse($contacts->toArray(), trans('contact.message_sent_successfully'));
    }
}
