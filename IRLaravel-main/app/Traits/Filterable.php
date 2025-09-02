<?php

namespace App\Traits;

use Carbon\Carbon;

trait Filterable
{
    /**
     * Apply the scope Filter to a given Eloquent query builder.
     *
     * @param $query
     * @param array|string|null $filtersJson
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyFilter($query, $filtersJson = null)
    {
        if ($filtersJson) {

            $opDef = ['==' => '=', 'gt' => '>', 'ge' => '>=', 'lt' => '<', 'le' => '<=', 'eq' => '=', 'ne' => '!=', 'like' => 'like', 'ilike' => 'like', 'llike' => 'like', 'rlike' => 'like', 'in' => 'in', 'notin' => 'notin', 'bw' => 'between'];
            $filters = is_array($filtersJson) ? $filtersJson : json_decode($filtersJson, true);
            $opType = ['Date', 'Time', 'Day', 'Month', 'Year'];
            //
            if ($filters) {
                foreach ($filters as $filter) {
                    $where = [];
                    $where['property'] = isset($filter['property']) ? $filter['property'] : 'id';
                    $where['operator'] = isset($filter['operator']) ? $opDef[$filter['operator']] : $opDef['eq'];
                    $where['value'] = isset($filter['value']) ? $filter['value'] : (is_null($filter['value']) ? $filter['value'] : '');
                    $where['boolean'] = empty($filter['boolean']) ? 'and' : ($filter['boolean'] == 'or' ? 'or' : 'and');
                    $where['type'] = (empty($filter['type']) || !in_array($filter['type'], $opType)) ? '' : $filter['type'];

                    // Check relationship
                    if (!empty($filter['rela'])) {
                        $where['rela'] = $filter['rela'];
                        $query = $query->whereHas($where['rela'], function ($query) use ($where) {

                            return $this->processFilter($query, $where);
                        });
                    } else {
                        if (!empty($filter['extra']) && $filter['extra'] == 'or') {
                            if (!empty($filter['children'])) {
                                $children = $filter['children'];
                                $query = $query->orWhere(function ($query) use ($children, $opDef) {
                                    foreach ($children as $fil) {
                                        $where = [];
                                        $where['property'] = $fil['property'];
                                        $where['operator'] = $opDef[$fil['operator']];
                                        $where['value'] = $fil['value'];
                                        $where['boolean'] = empty($fil['boolean']) ? 'and' : ($fil['boolean'] == 'or' ? 'or' : 'and');
                                        $query = $this->processFilter($query, $where);
                                    }
                                });
                            }

                        } else {
                            $query = $this->processFilter($query, $where);
                        }
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Apply the scope Sort to a given Eloquent query builder.
     * If sort have relationship then foregin_key is $foreignTable_id
     *
     * @param $query
     * @param array|string|null $sortJson
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplySort($query, $sortJson = null)
    {
        if ($sortJson) {
            $sorts = is_array($sortJson) ? $sortJson : json_decode($sortJson, true);
            foreach ($sorts as $sort) {
                if (!empty($sort)) {
                    $property = $sort['property'];
                    $direction = !empty($sort['direction']) ? $sort['direction'] : 'DESC';

                    if (!empty($sort['rela'])) {
                        $relation = $sort['rela'];
                        // Get table local
                        $localTable = $this->getTable();
                        $localKey = $this->getKeyName();
                        // get table foreign
                        $foreignModel = $this->{$relation}()->getModel();
                        $foreignTable = $foreignModel->getTable();
                        $foreignKey = $foreignModel->getKeyname();

                        $property = $relation . '.' . $property;
                        $query = $query->join(
                            $foreignTable . ' as ' . $relation,
                            $localTable . '.' . $localKey, '=', $relation . '.' . $localTable . '_' . $foreignKey
                        )
                            ->orderBy($property, $direction)
                            ->addSelect($localTable . '.*');;
                    } else {
                        $query = $query->orderBy($property, $direction);
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Process Filter
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $where
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function processFilter($query, $where)
    {
        if ((strpos($where['property'], '_id') && $where['value'] == 0) || $where['value'] == '') {
            return $query;
        }
        if ($where['operator'] == 'between') {
            $where['value'] = (is_array($where['value'])) ? $where['value'] : explode('', $where['value']);

            return $query->whereBetween($where['property'], $where['value']);
        } elseif ($where['operator'] == 'in') {
            $where['value'] = (is_array($where['value'])) ? $where['value'] : explode('', $where['value']);

            return $query->whereIn($where['property'], $where['value']);
        } elseif ($where['operator'] == 'notin') {
            $where['value'] = (is_array($where['value'])) ? $where['value'] : explode('', $where['value']);
            return $query->whereNotIn($where['property'], $where['value']);
        } else {
            if ($where['operator'] == 'llike') {
                $where['value'] = '%' . strtolower($where['value']);
            } elseif ($where['operator'] == 'rlike') {
                $where['value'] = strtolower($where['value']) . '%';
            } else {
                $where['value'] = ($where['operator'] == 'like') ? '%' . strtolower($where['value']) . '%' : strtolower($where['value']);
            }
            switch ($where['type']) {
                case 'Date':
                    $where['value'] = Carbon::createFromFormat(config('common.date_format'), $where['value']);
                    return $query->whereDate($where['property'], $where['operator'], $where['value']->toDateString(), $where['boolean']);
                    break;
                default:
                    return $query->where($where['property'], $where['operator'], $where['value'], $where['boolean']);
                    break;
            }
        }

    }
}