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
            $query = $query->orderBy('id', $orderBy);
        }

        if ($searchValue) {
            $query->where(function($query) use ($searchValue, $columns) {

                $first = true;

                foreach ($columns as $key => $column) {
                    if ($first) {
                        if ($column['search']) {
                            $query->where($key, 'like', '%' . $searchValue . '%');
                        }
                        $first = false;
                    } else {
                        if ($column['search']) {
                            $query->orWhere($key, 'like', '%' . $searchValue . '%');
                        }
                    }
                }
            });
        }

        return $query->paginate($limit);
    }
}
