<?php

namespace App\Models;

class PrinterJob extends AppModel
{
    const STATUS_PENDING = 1;
    const STATUS_PRINTING = 2;
    const STATUS_DONE = 3;
    const STATUS_ERROR = 4;

    const JOB_TYPE_KASSABON = 0;
    const JOB_TYPE_WERKBON = 1;
    const JOB_TYPE_STICKER = 2;

    const FOREIGN_MODEL_STATISTIC_PER_PRODUCT = 'statistic_per_product';
    const FOREIGN_MODEL_STATISTIC_DISCOUNT = 'statistic_discount';
    const FOREIGN_MODEL_STATISTIC_PER_PAYMENT_METHOD = 'statistic_per_payment_method';

    public $table = 'printer_jobs';
    
    public $timestamps = true;

    public $fillable = [
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
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'mac_address' => 'string',
        'job_type' => 'integer',
        'foreign_model' => 'string',
        'foreign_ids' => 'string',
        'content' => 'string',
        'meta_data' => 'string',
        'retries' => 'integer',
        'logs' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function settingPrint()
    {
        return $this->belongsTo(\App\Models\SettingPrint::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(\App\Models\Workspace::class);
    }

    /**
     * Confirm print
     * @param bool $printParts
     */
    public function confirm($printParts = true) {
        if($printParts === false) {
            $this->setAttribute('status', PrinterJob::STATUS_DONE);
            $this->setAttribute('printed_at', now());
        } else {
            $contents = json_decode($this->meta_data, true);
            $contentsPrinted = false;
            $contentstotal = count($contents);

            foreach ($contents as $contentkey => $content) {
                if (empty($content['printed'])) {
                    $metasPrinted = true;

                    if (isset($content['metas']) && !empty($content['metas'])) {
                        $metasPrinted = false;
                        $metatotal = count($content['metas']);

                        foreach ($content['metas'] as $metakey => $meta) {
                            if (empty($meta['printed'])) {
                                $contents[$contentkey]['metas'][$metakey]['printed'] = 1;

                                if (($metatotal - 1) == $metakey) {
                                    $metasPrinted = true;
                                }

                                break(1);
                            }
                        }
                    }

                    if ($metasPrinted) {
                        $contents[$contentkey]['printed'] = 1;

                        if (($contentstotal - 1) == $contentkey) {
                            $contentsPrinted = true;
                        }
                    }

                    break(1);
                }
            }

            $this->setAttribute('meta_data', json_encode($contents));
            $this->setAttribute('printed_at', now());

            if ($contentsPrinted) {
                $this->setAttribute('status', PrinterJob::STATUS_DONE);
            } else {
                $this->setAttribute('status', PrinterJob::STATUS_PENDING);
            }
        }

        $this->save();
        //$this->checkFileToDelete($this->refresh());
    }

    /**
     * Check file to delete
     */
    public function checkFileToDelete($job) {
        if($job->status == PrinterJob::STATUS_DONE) {
            if(!empty($job->content)) {
                $pendingJob = PrinterJob::where('status', '!=', PrinterJob::STATUS_DONE)
                    ->where('content', $job->content)
                    ->first();

                if(empty($pendingJob)) {
                    if(!empty($job->content)) {
                        $imagePath = implode('/', [config('filesystems.disks.public.root'), $job->content]);
                        $this->deleteFile($imagePath);
                    }

                    if(!empty($job->meta_data)) {
                        $contents = json_decode($job->meta_data, true);

                        foreach($contents as $key => $content) {
                            if($content['type'] == 'image') {
                                if(!empty($content['path'])) {
                                    $imagePath = implode('/', [config('filesystems.disks.public.root'), $content['path']]);
                                    $this->deleteFile($imagePath);
                                }

                                if(!empty($content['metas'])) {
                                    foreach($content['metas'] as $meta) {
                                        if(!empty($meta['path'])) {
                                            $imagePath = implode('/', [config('filesystems.disks.public.root'), $meta['path']]);
                                            $this->deleteFile($imagePath);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function deleteFile($imagePath) {
        if (\File::exists($imagePath)) {
            \File::delete($imagePath);
        }
    }

    /**
     * Error while printing make sure print is put on PENDING
     */
    public function error($statusCode) {
        if($this->getAttribute('retries') < config('print.retries')) {
            $this->setAttribute('retries', $this->getAttribute('retries') + 1);
            $this->setAttribute('status', PrinterJob::STATUS_PENDING);
        } else {
            $this->setAttribute('status', PrinterJob::STATUS_ERROR);
        }

        $logs = !empty($this->getAttribute('logs')) ? json_decode($this->getAttribute('logs'), true) : [];
        $this->setAttribute('logs', json_encode(array_merge($logs, [date('Y-m-d H:i:s') => $statusCode])));
        $this->save();
    }

    public static function statusOptions($value = null) {
        $options = array(
            static::STATUS_PENDING => trans('printjob.to_do'),
            static::STATUS_PRINTING => trans('printjob.printing'),
            static::STATUS_DONE => trans('printjob.done'),
            static::STATUS_ERROR => trans('printjob.error')
        );

        return static::enum($value, $options);
    }

    public static function typeOptions($value = null) {
        $options = array(
            static::JOB_TYPE_KASSABON => trans('printjob.kassabon'),
            static::JOB_TYPE_WERKBON => trans('printjob.werkbon'),
             static::JOB_TYPE_STICKER => trans('printjob.sticker'),
        );

        return static::enum($value, $options);
    }
}
