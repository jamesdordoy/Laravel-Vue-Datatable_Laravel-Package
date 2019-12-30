<?php

namespace JamesDordoy\LaravelVueDatatable\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DataTableCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //Remove keys required for joining
        $this->collection->each(function ($item, $key) {

            //Check that the item is a laravel model
            if (is_a($item, Model::class)) {
                $keyList = [];

                foreach ($item->getAttributes() as $key => $attribute) {
                    
                    if (substr($key, 0, 11) !== "_datatable_") {
                        $keyList[$key] = $attribute;
                    } 
                }

                $item->setRawAttributes($keyList);
            }
        });

        return [
            'data' => $this->collection,
            'payload' => $request->all()
        ];
    }
}
