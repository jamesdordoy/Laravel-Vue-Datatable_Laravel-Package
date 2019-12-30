<?php

namespace JamesDordoy\LaravelVueDatatable\Traits;

use DB;
use Illuminate\Support\Str;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use JamesDordoy\LaravelVueDatatable\Exceptions\ColumnNotFoundException;
use JamesDordoy\LaravelVueDatatable\Exceptions\ColumnNotOrderableException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipForeignKeyNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotInstantiatableException;

trait LaravelVueDatatableTrait
{
    /**
     * Method to get a Vue Datatable Query
     */
    public function scopeEloquentQuery($query, $orderBy = 'id', $orderByDir = 'asc', $searchValue = '', $relationships = [])
    {
        //Get select data
        $query = $this->selectData($query);

        $query = $this->eloquentOrderBy($query, $orderBy, $orderByDir);

        if (isset($this->dataTableRelationships) && count($this->dataTableRelationships)) {
            $query = $this->addRelationships($query, $relationships);
        }
        
        if (! empty($searchValue)) {
            $query = $this->eloquentFilter($query, $searchValue);
        }        

        return $query;
    }

    private function getColumnKeys() {
        //Select columns defined in the model
        $columnKeys = array_keys($this->dataTableColumns);

        //Prefix keys with table name
        foreach ($columnKeys as $index => $key) {
            $columnKeys[$index] = $this->getTable() . ".$key";
        }

        return $columnKeys;
    }


    private function createRelationshipModel($path)
    {
        try {
            return new $path;
        } catch (\Throwable $e) {
            throw new RelationshipModelNotInstantiatableException(
                "Could not create model for $tableName: " . $options['model']
            );
        }
    }

    private function selectData($query)
    {
        //Select this tables columns defined in the model
        $columnKeys = $this->getColumnKeys();

        $defaultOrderBy = config('laravel-vue-datatables.models.order_term');

        //Attach local foreign keys for joining
        if (isset($this->dataTableRelationships['belongsTo'])) {
            foreach ($this->dataTableRelationships['belongsTo'] as $tableName => $options) {
                $columnKeys[count($columnKeys) + 1] = $this->getTable() . "." . $options['foreign_key'];
            }
        }
        
        //Attach related foreign keys
        if (isset($this->dataTableRelationships['hasMany'])) {            
            foreach ($this->dataTableRelationships['hasMany'] as $tableName => $options) {
                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                if (! isset($options['foreign_key'])) {
                    throw new RelationshipForeignKeyNotSetException(
                        "Foreign Key not set on relationship: $tableName"
                    );
                }

                $model = $this->createRelationshipModel($options['model']);
                $columnKeys[count($columnKeys) + 1] = $model->getTable() . "." . $options['foreign_key'] . " as _datatable_" . $model->getTable() . "_" . $options['foreign_key'];
            }
        }

        if (isset($this->dataTableRelationships['belongsToMany'])) {
            foreach ($this->dataTableRelationships['belongsToMany'] as $tableName => $options) {

                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                $model = $this->createRelationshipModel($options['model']);
                $columnKeys[count($columnKeys) + 1] = $model->getTable() .".". $model->getKeyName() ." as _datatable_". $model->getTable() ."_". $model->getKeyName();
            }
        }
        
        if (isset($this->dataTableRelationships['belongsTo'])) {
            foreach ($this->dataTableRelationships['belongsTo'] as $tableName => $options) {
                //Exceptions
                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                if (! isset($options['foreign_key'])) {
                    throw new RelationshipForeignKeyNotSetException(
                        "Foreign Key not set on relationship: $tableName"
                    );
                }

                if (! isset($options['columns'])) {
                    throw new RelationshipColumnsNotFoundException(
                        "Columns array not set on relationship: $tableName"
                    );
                }

                $model = $this->createRelationshipModel($options['model']);

                //Join the table so it can be orderBy
                $query = $query->leftJoin(
                    $model->getTable(),
                    $this->getTable() . "." . $options['foreign_key'],
                    '=',
                    $model->getTable() . "." . $model->getKeyName()
                );

                foreach ($options['columns'] as $columnName => $atts) {
                    if ($atts[$defaultOrderBy]) {
                        array_push($columnKeys, $model->getTable() . ".$columnName as _datatable_" . $model->getTable() . "_" . $columnName);
                    }
                }
            }
        }

        if (isset($this->dataTableRelationships['hasMany'])) {
            foreach ($this->dataTableRelationships['hasMany'] as $tableName => $options) {

                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                if (! isset($options['columns'])) {
                    throw new RelationshipColumnsNotFoundException(
                        "Columns array not set on relationship: $tableName"
                    );
                }

                if (! isset($options['foreign_key'])) {
                    throw new RelationshipForeignKeyNotSetException(
                        "Foreign Key not set on relationship: $tableName"
                    );
                }

                $model = $this->createRelationshipModel($options['model']);

                $tableName = $model->getTable();

                //Join the table so it can be orderBy
                $query = $query->leftJoin(
                    "$tableName",
                    "$tableName." . $options['foreign_key'],
                    '=',
                    $this->getTable() . "." . $this->getKeyName(),
                );

                foreach ($options['columns'] as $columnName => $atts) {
                    if ($atts[$defaultOrderBy]) {
                        array_push($columnKeys, "$tableName.$columnName as _datatable_" . $tableName . "_" . $columnName);
                    }
                }
            }
        }

        if (isset($this->dataTableRelationships['belongsToMany'])) {
            foreach ($this->dataTableRelationships['belongsToMany'] as $tableName => $options) {

                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                $model = $this->createRelationshipModel($options['model']);

                $tableName = $model->getTable();

                //Join the table so it can be orderBy
                $query = $query->leftJoin(
                    $options['pivot']['table_name'],
                    $this->getTable() . "." . $this->getKeyName(),
                    '=',
                    $options['pivot']['table_name'] . "." . "user_id"
                );

                $query = $query->leftJoin(
                    $tableName,
                    $options['pivot']['table_name'] . "." . $options['pivot']['foreign_key'],
                    '=',
                    $tableName . "." . $model->getKeyName()
                );
            }
        }
        
        $query = $query->select($columnKeys)->groupBy($this->getTable() . "." . $this->getKeyName());

        return $query;
    }

    private function eloquentOrderBy($query, $column, $orderByDir)
    {
        //If a orderBy has been provided
        if (isset($column) && ! empty($column)) {
            $defaultOrderBy = config('laravel-vue-datatables.models.order_term');
            $tableAndColumn = count(explode(".", $column)) > 1 ? $column : $this->getTable() . ".$column";
            return $query->orderBy($tableAndColumn, $orderByDir);
        } else {
            $defaultOrderBy = config('laravel-vue-datatables.default_order_by');
            $defaultOrderBy = is_null($defaultOrderBy) ? 'id' : $defaultOrderBy;
            return $query->orderBy($this->getTable() . ".$defaultOrderBy", $orderByDir);
        }
    }

    private function addRelationships($query, $relationships)
    {
        //Get relationship names
        $belongsTo = isset($this->dataTableRelationships['belongsTo']) ?
            array_keys($this->dataTableRelationships['belongsTo']) : [];

        $hasMany = isset($this->dataTableRelationships['hasMany']) ?
            array_keys($this->dataTableRelationships['hasMany']) : [];

        $belongsToMany = isset($this->dataTableRelationships['belongsToMany']) ?
            array_keys($this->dataTableRelationships['belongsToMany']) : [];

        //Check if any belongsToMany Relationships need ordering 
        if (isset($this->dataTableRelationships['belongsToMany'])) {
            foreach ($this->dataTableRelationships['belongsToMany'] as $tableName => $relationship) {

                foreach ($belongsToMany as $name) {
                    if ($tableName === $name) {
                        if (isset($relationship['subOrder'])) {

                            $subOrder = $relationship['subOrder'];

                            $belongsToMany[$name] = function($q) use ($subOrder) {
                                $q->orderBy($subOrder["order_by"], $subOrder["order_dir"]);
                            };
                        }
                    }
                }
            }
        }

        //Remove relationships not required for query
        $with = array_merge($belongsTo, $hasMany, $belongsToMany);
        $relationships = array_intersect($with , $relationships);

        if (count($relationships)) {
            return $query->with($relationships);
        }
    }

    private function eloquentFilter($query, $searchValue)
    {
        $searchTerm = config('laravel-vue-datatables.models.search_term');

        if (isset($searchValue) && ! empty($searchValue)) {
            //Filter Local Columns
            if (isset($this->dataTableColumns)) {
                $query->where(function($query) use ($searchValue, $searchTerm) {
                    foreach ($this->dataTableColumns as $key => $column) {
                        if (isset($column[$searchTerm])) {
                            if ($key === key($this->dataTableColumns)) {
                                $query->where($this->getTable() . ".$key", 'like', "%$searchValue%");
                            } else {
                                $query->orWhere($this->getTable() . ".$key", 'like', "%$searchValue%");
                            }
                        }
                    }
                });
            }   
            //Filter Relationships
            if (isset($this->dataTableRelationships['belongsTo'])) {

                $searchTerm = config('laravel-vue-datatables.models.search_term');

                foreach ($this->dataTableRelationships['belongsTo'] as $tableName => $options) {  

                    if (! isset($options['model'])) {     
                        throw new RelationshipModelNotSetException(
                            "Model not set on relationship: $tableName"
                        );
                    }

                    if (! isset($options['columns'])) {
                        throw new RelationshipColumnsNotFoundException(
                            "Columns array not set on relationship: $tableName"
                        );
                    }

                    $model = $this->createRelationshipModel($options['model']);

                    $query = $query->orWhereHas($tableName, function ($q) use ($searchValue, $model, $options, $searchTerm) {
                        
                        if (isset($options['columns'])) {
                            
                            $tableName = $model->getTable();

                            foreach ($options['columns'] as $columnName => $col) {
                                if ($col[$searchTerm]) {
                                    if ($columnName === key($options['columns'])) {
                                        $q->where("$tableName.$columnName", "like",  "%$searchValue%");
                                    } else {
                                        $q->orWhere("$tableName.$columnName", "like",  "%$searchValue%");
                                    }
                                }
                            }
                        } 
                    });
                }
            }

            if (isset($this->dataTableRelationships['hasMany'])) {

                $searchTerm = config('laravel-vue-datatables.models.search_term');

                foreach ($this->dataTableRelationships['hasMany'] as $tableName => $options) {

                    if (! isset($options['model'])) {     
                        throw new RelationshipModelNotSetException(
                            "Model not set on relationship: $tableName"
                        );
                    }

                    if (! isset($options['columns'])) {
                        throw new RelationshipColumnsNotFoundException(
                            "Columns array not set on relationship: $tableName"
                        );
                    }

                    $model = $this->createRelationshipModel($options['model']);

                    $query = $query->orWhereHas($tableName, function ($q) use ($searchValue, $model, $options, $searchTerm) {
                    
                        $tableName = $model->getTable();

                        foreach ($options['columns'] as $columnName => $col) {
                            if ($col[$searchTerm]) {
                                if ($columnName === key($options['columns'])) {
                                    $q->where("$tableName.$columnName", "like",  "%$searchValue%");
                                } else {
                                    $q->orWhere("$tableName.$columnName", "like",  "%$searchValue%");
                                }
                            }
                        }  
                    });
                }
            }

            if (isset($this->dataTableRelationships['belongsToMany'])) {

                $searchTerm = config('laravel-vue-datatables.models.search_term');

                foreach ($this->dataTableRelationships['belongsToMany'] as $tableName => $options) {

                    if (! isset($options['model'])) {     
                        throw new RelationshipModelNotSetException(
                            "Model not set on relationship: $tableName"
                        );
                    }

                    if (! isset($options['columns'])) {
                        throw new RelationshipColumnsNotFoundException(
                            "Columns array not set on relationship: $tableName"
                        );
                    }

                    $model = $this->createRelationshipModel($options['model']);

                    $query = $query->orWhereHas($tableName, function ($q) use ($searchValue, $model, $options, $searchTerm) {
                        //Get the real table name
                        $tableName = $model->getTable();

                        foreach ($options['columns'] as $columnName => $col) {
                            //Check if column is searchable
                            if ($col[$searchTerm]) {
                                //Check if first key
                                if ($columnName === key($options['columns'])) {
                                    $q->where("$tableName.$columnName", "like",  "%$searchValue%");
                                } else {
                                    $q->orWhere("$tableName.$columnName", "like",  "%$searchValue%");
                                }
                            }
                        }  
                    });
                }
            }
        }

        return $query;
    }
}
