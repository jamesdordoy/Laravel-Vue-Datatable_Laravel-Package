<?php

namespace JamesDordoy\LaravelVueDatatable\Traits;

trait LaravelVueDatatableTrait
{
    /**
     * Method to get a Vue Datatable Query
     */
    public function scopeDataTableQuery($query, $column = 'id', $orderBy = 'asc', $searchValue = '')
    {
        $columns = $this->dataTableColumns;

        if (isset($column) && ! empty($column) && array_key_exists($column, $columns)) {
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
}
