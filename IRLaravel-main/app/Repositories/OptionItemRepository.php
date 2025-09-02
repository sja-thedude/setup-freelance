<?php

namespace App\Repositories;

use App\Models\Option;
use App\Models\OptionItem;
use App\Models\OptionItemReference;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class OptionItemRepository.
 *
 * @package namespace App\Repositories;
 */
class OptionItemRepository extends AppBaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OptionItem::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $input
     * @param $workspaceId
     * @param $connectorsList
     * @return bool|null
     */
    public function updateOrCreateOptionItemReferences($input, $workspaceId, $connectorsList, Option $option) {
        if(empty($connectorsList) || empty($option)) {
            return null;
        }

        $items = $option->items;

        foreach($items as $item) {
            foreach($connectorsList as $connectorItem) {
                $optionItemReference = $item->optionItemReferences()
                    ->where('provider', $connectorItem->provider)
                    ->where('local_id', $item->id)
                    ->first();

                if(empty($optionItemReference)) {
                    $optionItemReference = new OptionItemReference();
                    $optionItemReference->workspace_id = $workspaceId;
                    $optionItemReference->local_id = $item->id;
                    $optionItemReference->provider = $connectorItem->provider;
                }

                if(!empty($input['orderItemReferences'][$item->id][$connectorItem->id])) {
                    $optionItemReference->remote_id = !empty($input['orderItemReferences'][$item->id][$connectorItem->id]['remote_id'])
                        ? $input['orderItemReferences'][$item->id][$connectorItem->id]['remote_id']
                        : '';
                    $optionItemReference->save();
                }
            }
        }

        return true;
    }

    /**
     * @param $localIds
     * @return \Illuminate\Support\Collection
     */
    public function getOptionItemReferencesByWorkspaceAndLocalId($workspaceId, $localId) {
        return OptionItemReference::where('workspace_id', $workspaceId)
            ->where('local_id', $localId)
            ->get();
    }

    /**
     * @param $provider
     * @param $localIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getOptionItemReferencesByWorkspaceAndLocalIds($workspaceId, $localIds) {
        return OptionItemReference::where('workspace_id', $workspaceId)
            ->whereIn('local_id', $localIds)
            ->get();
    }

    /**
     * @param $provider
     * @param $localIds
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getOptionItemReferencesByWorkspaceAndProviderAndLocalIds($workspaceId, $provider, $localIds) {
        return OptionItemReference::where('workspace_id', $workspaceId)
            ->where('provider', $provider)
            ->whereIn('local_id', $localIds)
            ->get();
    }

    public function getMaxOrderPositionByWorkspaceId($workspaceId, $optieId) {
        DB::enableQueryLog();

        $result = DB::table('optie_items')
            ->select(DB::raw('MAX(optie_items.order) AS max_order'))
            ->leftJoin('opties', 'optie_items.opties_id', '=', 'opties.id')
            ->where('opties.workspace_id', $workspaceId)
            ->where('opties.id', $optieId)
            ->first();

        if(!empty($result->max_order)) {
            return (int) $result->max_order;
        }

        return 0;
    }
}
