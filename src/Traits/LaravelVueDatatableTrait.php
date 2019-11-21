<?php

namespace JamesDordoy\LaravelVueDatatable\Traits;

use DB;

trait LaravelVueDatatableTrait
{
    /**
     * Method to get a Vue Datatable Query
     */
    public function scopeEloquentQuery($query, $column = 'id', $orderBy = 'asc', $searchValue = '')
    {
        $columns = $this->dataTableColumns;

        if (isset($column) && ! empty($column)) {
            $query = $query->orderBy($column, $orderBy);
        } else {
            $defaultOrderBy = config('laravel-vue-datatables.default_order_by');
            if (is_null($defaultOrderBy)) {
                $defaultOrderBy = 'id';
            }
            $query = $query->orderBy($defaultOrderBy, $orderBy);
        }

        if ($searchValue) {
            $query->where(function($query) use ($searchValue, $columns) {
                $searchTerm = config('laravel-vue-datatables.models.search_term');
                $first = true;
                if (count($columns)) {
                    foreach ($columns as $key => $column) {
                        if ($first) {
                            if (isset($column[$searchTerm])) {
                                $query->where($key, 'like', '%' . $searchValue . '%');
                            }
                            $first = false;
                        } else {
                            if ($column[$searchTerm]) {
                                $query->orWhere($key, 'like', '%' . $searchValue . '%');
                            }
                        }
                    }
                }
            });
        }

        return $query;
    }

    public function scopeQueryBuilderQuery($query, $column = 'id', $orderBy = 'asc', $searchValue = '')
    {
        $orderByString = '';

        if (count(explode(".", $column)) > 1) {
            $orderByString = $column;
        } else {
            $orderByString = $this->getTable() . ".$column";
        }

        $columnKeys = array_keys($this->dataTableColumns);
        
        $query = DB::table($this->getTable())
            ->select($columnKeys)
            ->orderBy($orderByString, $orderBy);

        if ($searchValue) {

            $columns = $this->dataTableColumns;
            $searchTerm = config('laravel-vue-datatables.models.search_term');
            $first = true;

            foreach ($columns as $key => $col) {
                if (isset($col[$searchTerm])) {
                    if ($first && isset($column[$searchTerm])) {
                        $query = $query->where($this->getTable() . ".$key", 'LIKE', "%{$searchValue}%");
                        $first = false;
                    } else {
                        $query = $query->orWhere($this->getTable() . ".$key", 'LIKE', "%{$searchValue}%");
                    }
                }
            } 
        }

        return $query;
    }
}
