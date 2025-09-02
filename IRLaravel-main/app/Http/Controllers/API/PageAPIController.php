<?php

namespace App\Http\Controllers\API;

use App\Modules\ContentManager\Models\Articles;
use App\Repositories\PageRepository;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

class PageAPIController extends AppBaseController
{
    private $pageRepository;
    public function __construct(PageRepository $pageRepository)
    {
        parent::__construct();

        $this->pageRepository = $pageRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $this->pageRepository->pushCriteria(new RequestCriteria($request));
            $this->pageRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        $perPage = (int)$request->get('per_page');
        $limit = (int)$request->get('limit', $perPage);
        $article = $this->pageRepository->paginate($limit);

        return $this->sendResponse($article->toArray(), 'Banners are retrieved successfully');
    }

    public function bySlug($slug)
    {
        try {
            // Assuming there is a relationship method named 'translations' in your Articles model
            $page = Articles::where('post_mime_type', $slug)->first();

            if (!$page) {
                return $this->sendError('Page not found');
            }

            $result = $page->getFullInfo();

            return $this->sendResponse($result, 'Translations retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
