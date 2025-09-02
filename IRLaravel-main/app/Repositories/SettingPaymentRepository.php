<?php

namespace App\Repositories;

use App\Models\SettingPayment;
use App\Models\SettingPaymentReference;

class SettingPaymentRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'type',
        'api_token',
        'takeout',
        'delivery',
        'in_house',
        'self_ordering',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return SettingPayment::class;
    }

    public function getPaymentMethods($workspaceId) {
        return $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();
    }

    /**
     * @param $input
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function updateOrCreatePayments($input) {
        if(empty($input['payments'])) {
            return false;
        }
        
        foreach($input['payments'] as $item) {
            $item['workspace_id'] = $input['workspace_id'];
            
            $item['takeout'] = !empty($item['takeout']) ? SettingPayment::VALUE_TRUE : SettingPayment::VALUE_FALSE;
            $item['delivery'] = !empty($item['delivery']) ? SettingPayment::VALUE_TRUE : SettingPayment::VALUE_FALSE;
            $item['in_house'] = !empty($item['in_house']) ? SettingPayment::VALUE_TRUE : SettingPayment::VALUE_FALSE;
            $item['self_ordering'] = !empty($item['self_ordering']) ? SettingPayment::VALUE_TRUE : SettingPayment::VALUE_FALSE;

            $payments = $this->makeModel()->updateOrCreate([
                'workspace_id' => $input['workspace_id'],
                'type' => (int) $item['type']
            ], $item);
        }

        return true;
    }

    /**
     * @param $input
     * @param $workspaceId
     * @param $connectorsList
     * @return bool|null
     */
    public function updateOrCreatePaymentReferences($input, $workspaceId, $connectorsList) {
        if(empty($connectorsList)) {
            return null;
        }

        $payments = $this->getPaymentMethods((int) $workspaceId);

        foreach($payments as $payment) {
            foreach($connectorsList as $connectorItem) {
                // Get order reference..
                $paymentReference = $payment->paymentReferences()
                    ->where('provider', $connectorItem->provider)
                    ->where('local_id', $payment->id)
                    ->first();

                if(empty($paymentReference)) {
                    $paymentReference = new SettingPaymentReference();
                    $paymentReference->workspace_id = $workspaceId;
                    $paymentReference->local_id = $payment->id;
                    $paymentReference->provider = $connectorItem->provider;
                }

                if(!empty($input['paymentReferences'][$payment->type][$connectorItem->id])) {
                    $paymentReference->remote_id = !empty($input['paymentReferences'][$payment->type][$connectorItem->id]['remote_id'])
                        ? $input['paymentReferences'][$payment->type][$connectorItem->id]['remote_id']
                        : '';
                    $paymentReference->remote_name = !empty($input['paymentReferences'][$payment->type][$connectorItem->id]['remote_name'])
                        ? $input['paymentReferences'][$payment->type][$connectorItem->id]['remote_name']
                        : '';
                    $paymentReference->save();
                }
            }
        }

        return true;
    }

    /**
     * @param $localIds
     * @return \Illuminate\Support\Collection
     */
    public function getSettingPaymentReferencesByWorkspace($workspaceId) {
        return SettingPaymentReference::where('workspace_id', $workspaceId)
            ->get();
    }

    /**
     * @param $workspaceId
     * @return bool
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function initSettingPaymentForWorkspace($workspaceId) {
        $checkExist = $this->makeModel()
            ->where('workspace_id', $workspaceId)
            ->get();

        if($checkExist->isEmpty()) {
            $now = now();
            $data = [
                [
                    'workspace_id' => $workspaceId,
                    'type' => 0,
                    'takeout' => 0,
                    'delivery' => 0,
                    'in_house' => 0,
                    'self_ordering' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'workspace_id' => $workspaceId,
                    'type' => 1,
                    'takeout' => 0,
                    'delivery' => 0,
                    'in_house' => 0,
                    'self_ordering' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'workspace_id' => $workspaceId,
                    'type' => 2,
                    'takeout' => 1,
                    'delivery' => 1,
                    'in_house' => 0,
                    'self_ordering' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ];

            if(!empty($data)) {
                SettingPayment::insert($data);
            }
        }

        return true;
    }
}
