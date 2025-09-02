<?php

namespace App\Http\Controllers\API;

use App\Facades\Helper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use URL;
use DB;

/**
 * Class CommonAPIController
 * @package App\Http\Controllers\API
 */
class CommonAPIController extends AppBaseController
{
    /**
     * Get all timezones
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTimezones(Request $request)
    {
        $items = Helper::getTimezones();
        $flatItems = Arr::flatten($items);
        $totalItems = count($flatItems);
        $perPage = ($request->has('limit')) ? (int)$request->get('limit') : $totalItems;
        $currentPage = ($request->has('page')) ? (int)$request->get('page') : 1;
        $options = [
            'path' => URL::current(),
        ];

        $result = new LengthAwarePaginator($items, $totalItems, $perPage, $currentPage, $options);

        return $this->sendResponse($result, 'Timezones are retrieved successfully');
    }

    /**
     * Check system online or not
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isOnline(Request $request)
    {
        // Check DB connection
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse(null, 'Server is okay');
    }
}
