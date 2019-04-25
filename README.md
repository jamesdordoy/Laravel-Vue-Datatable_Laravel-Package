##Publishing Assets
php artisan vendor:publish --provider="JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider" --tag="vue-components"

## Add Config
JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider::class,

## Add Trait
use App\Traits\DatatableTrait;

use DatatableTrait;

## Use Controller Resource
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

$data = Project::dataTableQuery($column, $dir, $length, $searchValue);

return new DataTableCollectionResource($data);

## app.js
require('./packages/jamesdordoy/laravelvuedatatable/app.js');

## Component

```html

<template>
    <div class="bg-black pb-4">
        <data-table
            url="/api/projects"
            :per-page="perPage"
            :columns="columns">
        </data-table>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                perPage: ['15', '50', '100'],
                columns: [
                    { label: 'ID', name: 'id' },
                    { label: 'Name', name: 'name' },
                    { label: 'Owner', name: 'owner' },
                    { label: 'Complete', name: 'completed' },
                    { label: 'Private', name: 'private' },
                ]
            }
        },
    }
</script>
```