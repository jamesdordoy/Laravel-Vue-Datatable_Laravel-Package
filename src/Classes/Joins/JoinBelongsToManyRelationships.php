<?php

namespace JamesDordoy\LaravelVueDatatable\Classes\Joins;

use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipForeignKeyNotSetException;

class JoinBelongsToManyRelationships
{
    public function __invoke($query, $localModel, $relationships, $relationshipModelFactory)
    {
        if (isset($relationships['belongsToMany'])) {
            foreach ($relationships['belongsToMany'] as $tableName => $options) {

                if (! isset($options['model'])) {     
                    throw new RelationshipModelNotSetException(
                        "Model not set on relationship: $tableName"
                    );
                }

                $model = $relationshipModelFactory($options['model'], $tableName);

                $tableName = $model->getTable();

                //Join the table so it can be orderBy
                $query = $query->leftJoin(
                    $options['pivot']['table_name'],
                    $localModel->getTable() . "." . $localModel->getKeyName(),
                    '=',
                    $options['pivot']['table_name'] . "." . "user_id"
                );

                $query = $query->leftJoin(
                    $tableName,
                    $options['pivot']['table_name'] . "." . $options['pivot']['foreign_key'],
                    '=',
                    $tableName . "." . $localModel->getKeyName()
                );
            }
        }

        return $query;
    }
}