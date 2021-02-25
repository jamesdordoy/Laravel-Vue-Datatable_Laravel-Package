<?php

namespace JamesDordoy\LaravelVueDatatable\Classes\Filters;

use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException;

/**
 * Class FilterBelongsToRelationships
 * @package JamesDordoy\LaravelVueDatatable\Classes\Filters
 */
class FilterBelongsToRelationships
{
    /**
     * @param $query
     * @param $searchValue
     * @param $relationshipModelFactory
     * @param $model
     * @param $relationships
     * @return mixed
     * @throws RelationshipColumnsNotFoundException
     * @throws RelationshipModelNotSetException
     */
    public function __invoke($query, $searchValue, $relationshipModelFactory, $model, $relationships)
    {
        if (!isset($relationships['belongsTo']) || !is_array($relationships['belongsTo'])) {
            return $query;
        }

        $searchTerm = config('laravel-vue-datatables.models.search_term');

        foreach ($relationships['belongsTo'] as $tableName => $options) {

            if (!isset($options['model'])) {
                throw new RelationshipModelNotSetException(
                    "Model not set on relationship: $tableName"
                );
            }

            if (!isset($options['columns'])) {
                throw new RelationshipColumnsNotFoundException(
                    "Columns array not set on relationship: $tableName"
                );
            }

            $model = $relationshipModelFactory($options['model'], $tableName);

            $query->orWhereHas($tableName, function ($query) use ($searchValue, $model, $options, $searchTerm) {

                if (isset($options['columns'])) {

                    $tableName = $model->getTable();

                    foreach ($options['columns'] as $columnName => $col) {
                        if ($col[$searchTerm]) {
                            $query->{($columnName === key($options['columns']) ? 'where' : 'orWhere')}("$tableName.$columnName", "like", "%$searchValue%");
                        }
                    }
                }
            });

        }

        return $query;
    }
}
