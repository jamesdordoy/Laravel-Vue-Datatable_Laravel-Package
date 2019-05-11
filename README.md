
# Laravel Vue Datatable
A Vue.js datatable component for Laravel that works with Bootstrap.

## Requirements

* [Vue.js](https://vuejs.org/) 2.x
* [Laravel](http://laravel.com/docs/) 5.x
* [Bootstrap](http://getbootstrap.com/) 4

## Demo

See 

## Example
![Image description](https://www.jamesdordoy.co.uk/images/datatable.png)

## Package Installation
composer require jamesdordoy/laravelvuedatatable

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

## Component Installation

```bash
npm install laravel-vue-datatable
```

## Usage

Register the plugin:

```javascript
import DataTable from '../../node_modules/laravel-vue-datatable/src/index.js';
Vue.use(DataTable);
```

Use the component:

```html
<data-table
    url="http://vue-datatable.test/ajax"
    :per-page="perPage"
    :columns="columns">
</data-table>
```

```javascript
export default {
    name: 'app',
    data() {
        return {
            perPage: ['10', '25', '50'],
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    filterable: true,
                },
                {
                    label: 'Name',
                    name: 'name',
                    filterable: true,
                },
                {
                    label: 'Email',
                    name: 'email',
                    filterable: true,
                },
                {
                    label: '',
                    name: 'View',
                    filterable: false,
                },
            ]
        }
    },
}
```
