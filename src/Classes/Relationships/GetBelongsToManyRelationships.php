<?php

namespace JamesDordoy\LaravelVueDatatable\Classes\Relationships;

use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipModelNotSetException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipColumnsNotFoundException;
use JamesDordoy\LaravelVueDatatable\Exceptions\RelationshipForeignKeyNotSetException;

class GetBelongsToManyRelationships
{
    public function __invoke($declaredRelationship, $relationships, $with = [])
    {
        if (isset($declaredRelationship['belongsToMany'])) {

            $belongsToMany = array_keys($declaredRelationship['belongsToMany']);

            foreach ($belongsToMany as $key => $item) {
                if (! is_numeric($key) && in_array($key, $relationships)){
                    $with[$key] = $item;
                }       
            }

            foreach ($declaredRelationship['belongsToMany'] as $tableName => $relationship) {
    
                foreach ($belongsToMany as $name) {
                    
                    if ($tableName === $name) {
                        if (isset($relationship['subOrder'])) {

                            $subOrder = $relationship['subOrder'];

                            $with[$name] = function($q) use ($subOrder) {
                                $q->orderBy($subOrder["order_by"], $subOrder["order_dir"]);
                            };
                        }
                    }
                }
            }

            return $with;
        }
        
        return $with;
    }
}