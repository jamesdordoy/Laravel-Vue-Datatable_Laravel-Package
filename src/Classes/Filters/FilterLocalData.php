<?php

namespace JamesDordoy\LaravelVueDatatable\Classes\Filters;

class FilterLocalData
{
    public function __invoke($query, $searchValue, $model, $localColumns)
    {
        $searchTerm = config('laravel-vue-datatables.models.search_term');
        $likeTerm = config('laravel-vue-datatables.like_term');

        if (isset($localColumns)) {
            return $query->where(function($query) use ($searchValue, $searchTerm, $model, $localColumns, $likeTerm) {
                foreach ($localColumns as $key => $column) {
                    if (isset($column[$searchTerm])) {
                        if ($key === key($localColumns)) {
                            $query->where($model->getTable() . ".$key", $likeTerm, "%$searchValue%");
                        } else {
                            $query->orWhere($model->getTable() . ".$key", $likeTerm, "%$searchValue%");
                        }
                    }
                }
            });
        }
        
        return $query;
    }
}