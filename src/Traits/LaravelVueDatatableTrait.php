<?php

namespace JamesDordoy\LaravelVueDatatable\Traits;

trait LaravelVueDatatableTrait
{
    /**
     * Method to get a Vue Datatable Query
     */
    public function scopeDataTableQuery($query, $column = 0, $orderBy = 'asc', $limit = 15, $searchValue = '')
    {
        $columns = $this->dataTableColumns;

        if (isset(array_keys($columns)[$column])) {
            $query = $query->orderBy(array_keys($columns)[$column], $orderBy);
        } else {

            $defaultOrderBy = config('laravel-vue-datatables.default_order_by');

            if (is_null($defaultOrderBy)) {
                $defaultOrderBy = 'id';
            }

            $query = $query->orderBy($defaultOrderBy, $orderBy);
        }

        if ($searchValue) {
            $query->where(function($query) use ($searchValue, $columns) {

                $first = true;

                if (count($columns)) {
                    foreach ($columns as $key => $column) {
                        if ($first) {
    
                            $searchTerm = config('laravel-vue-datatables.models.search_term');

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

        return $query->paginate($limit);
    }
}
