## Example
![Image description](https://www.jamesdordoy.co.uk/images/datatables.png)

## Publishing Assets
php artisan vendor:publish --provider="JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider" --tag="vue-components"

## Add Config
JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider::class,

## Add Trait
```php
<?php

namespace App;

use App\Traits\DatatableTrait;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use DatatableTrait;

    protected $dataTableColumns = [
        'id' => [
            'search' => false,
        ],
        'name' => [
            'search' => true,
        ],
        'link' => [
            'search' => true,
        ]
    ];
}
```

## Use Controller Resource
```php
<?php

namespace App\Http\Controllers\Back;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class ProjectController extends Controller
{
    public function ajax(Request $request)
    {   
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir');
        $searchValue = $request->input('search');

        $data = Project::dataTableQuery($column, $dir, $length, $searchValue);

        return new DataTableCollectionResource($data);
    }
}
```

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

