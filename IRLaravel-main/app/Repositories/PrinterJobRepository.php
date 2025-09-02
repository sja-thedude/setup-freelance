<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\PrinterJob;
use App\Models\PrinterGroupWorkspace;

class PrinterJobRepository extends AppBaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'workspace_id',
        'printer_id',
        'status',
        'mac_address',
        'job_type',
        'foreign_model',
        'foreign_id',
        'foreign_ids',
        'content',
        'meta_data',
        'retries',
        'logs',
        'printed_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     */
    public function model()
    {
        return PrinterJob::class;
    }

    /**
     * @param int $workspaceId
     * @param string $printerMAC
     * @return object $job null if not exist
     */
    public function needPrint($workspaceId, $printerMAC) {
        $jobResult = null;
        $macUppercase = strtoupper($printerMAC);
        $workspaceIds = $this->getGroupWorkspaceIds($workspaceId);
        $job = $this->makeModel()
            ->whereIn('workspace_id', $workspaceIds)
            ->whereIn('status', [PrinterJob::STATUS_PENDING, PrinterJob::STATUS_PRINTING])
            ->whereIn('mac_address', [$printerMAC, $macUppercase])
            ->orderBy('status', 'DESC')
            ->orderBy('id', 'ASC')
            ->first();

        if(empty($job)) {
            return $jobResult;
        }

        $job->status = PrinterJob::STATUS_PRINTING;
        $job->save();

        return $job;
    }

    /**
     * @param $workspaceId
     * @param $printerMAC
     * @param PrinterJob|null $job
     * @return array  [$image, $last]
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function processPrint($workspaceId, $printerMAC, $job = null, $printParts = true) {
        $last = true;
        $jobResult = [];
        $macUppercase = strtoupper($printerMAC);

        if(!is_object($job)) {
            $workspaceIds = $this->getGroupWorkspaceIds($workspaceId);
            $job = $this->makeModel()
                ->whereIn('workspace_id', $workspaceIds)
                ->where('status', PrinterJob::STATUS_PRINTING)
                ->whereIn('mac_address', [$printerMAC, $macUppercase])
                ->first();
        }

        if(empty($job)) {
            return $jobResult;
        }

        $contents = json_decode($job->meta_data, true);

        if($printParts === false) {
            return $contents;
        }

        // Use parts to return content to printer
        $last = false;
        $content = null;

        if(is_array($contents)) {
            $contentstotal = count($contents);

            foreach ($contents as $contentkey => $currentContent) {
                if (empty($currentContent['printed'])) {
                    $lastmetas = true;
                    if (isset($currentContent['metas']) && !empty($currentContent['metas'])) {
                        $lastmetas = false;
                        $metatotal = count($currentContent['metas']);
                        foreach ($currentContent['metas'] as $metakey => $meta) {
                            if (empty($meta['printed'])) {
                                $content = $meta;

                                if (($metatotal - 1) == $metakey) {
                                    $lastmetas = true;
                                }

                                break(1);
                            }
                        }
                    }

                    if (empty($content)) {
                        $content = $currentContent;
                    }

                    $last = $lastmetas && (($contentstotal - 1) == $contentkey);

                    break(1);
                }
            }
        }

        return compact('content', 'last');
    }

    /**
     * @param int $workspaceId
     * @param string $printerMAC
     * @param string $statusCode
     * @param bool $printParts
     * @return object or null
     */
    public function confirmPrinted($workspaceId, $printerMAC, $statusCode, $printParts = true) {
        $jobResult = null;
        $macUppercase = strtoupper($printerMAC);
        $workspaceIds = $this->getGroupWorkspaceIds($workspaceId);
        /** @var PrinterJob $job */
        $job = $this->makeModel()
            ->whereIn('workspace_id', $workspaceIds)
            ->where('status', PrinterJob::STATUS_PRINTING)
            ->whereIn('mac_address', [$printerMAC, $macUppercase])
            ->first();

        if(empty($job)) {
            return $jobResult;
        }

        if(in_array($statusCode, ['200 OK', '2xx', '20x', '201', '221'])) {
            // success
            $job->confirm($printParts);
        } else {
            // error
            $job->error($statusCode);
        }

        if($job->status == PrinterJob::STATUS_DONE) {
            $this->printChecked($job);
        }

        return $jobResult;
    }

    private function printChecked($job) {
        if($job->foreign_model == Order::class) {
            $attr = 'printed_' . config('print.job_type_decode.'.$job->job_type);
            
            if(!empty($job->foreign_id)) {
                $order = Order::find($job->foreign_id);

                if(!empty($order)) {
                    $order->$attr = true;
                    $order->save();
                }
            }
            if(!empty($job->foreign_ids)) {
                $orderIds = explode('_', $job->foreign_ids);

                foreach ($orderIds as $orderId) {
                    $order = Order::find($orderId);

                    if($order) {
                        $order->$attr = true;
                        $order->save();
                    }
                }
            }
        }
    }

    public function getList($request) {
        $workspaceId = $request->get('workspace_id', null);
        $model = $this->makeModel();
        //Sort
        $sortBy = (!empty($request->sort_by)) ? $request->sort_by : 'id';
        $orderBy = (!empty($request->order_by)) ? $request->order_by : 'desc';

        if(!is_null($workspaceId)) {
            $model = $model->where('workspace_id', $workspaceId);
        }

        if (!empty($request->sort_by) && $request->sort_by == 'workspace_id') {
            $model = $model->with(['workspace' => function ($query) use ($request) {
                $query->orderBy('name', $request->order_by);
            }]);
        } else {
            $model = $model->orderBy($sortBy, $orderBy);
        }

        return $model->paginate();
    }

    public function cancel($id) {
        return $this->makeModel()
            ->where('id', $id)
            ->update(['status' => PrinterJob::STATUS_ERROR]);
    }

    public function getGroupWorkspaceIds($workspaceId) {
        $workspaceIds = [$workspaceId];
        $printerGroupWorkspace = PrinterGroupWorkspace::where('workspace_id', $workspaceId)
            ->whereHas('printerGroup', function ($query) {
                $query->where('active', true);
            })
            ->first();

        if(!empty($printerGroupWorkspace)) {
            $workspaceIds = PrinterGroupWorkspace::where('printer_group_id', $printerGroupWorkspace->printer_group_id)
                ->pluck('workspace_id')
                ->all();
        }

        return $workspaceIds;
    }
}
