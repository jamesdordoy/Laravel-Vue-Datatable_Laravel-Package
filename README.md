
# Laravel Vue Datatable
A Vue.js datatable component for Laravel that works with Bootstrap.

## Requirements

* [Vue.js](https://vuejs.org/) 2.x
* [Laravel](http://laravel.com/docs/) 5.x
* [Bootstrap](http://getbootstrap.com/) 4

This package makes use of the Laravel Vue Pagination component created by [gilbitron](https://github.com/gilbitron/laravel-vue-pagination). If you need a pagination component for other areas of the website and you are using a Laravel API i highly suggest using this flexable component.

## Demo

See 

## Example
![Image description](https://www.jamesdordoy.co.uk/images/datatable.png?a=a)

## Package Installation
```
$ composer require jamesdordoy/laravelvuedatatable
```

## Add Service Provider
```
JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider::class,
```

## Publish the Config
```
php artisan vendor:publish --provider="JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider"
```

### Options

```json
{
    "models": {
        "search_term": "The term used in each model to declare if it is searchable by the datatable"
    },
    "default_order_by": "the default order by column on page loads"
}
```


## Use the Trait
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

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

## Use the Controller Resource
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
        $dir = $request->input('dir', 'asc');
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

## Register the Plugin

```javascript
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);
```

## Basic Example 

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

## API

### Datatable Props

| Name | Type | Default | Description  
| --- | --- | --- | --- |
| `url ` | String | "/" | The JSON url |
| `columns` | Array | [ '10', '25', '50' ] | The table columns |
| `per-page` | Array | [ '10', '25', '50' ] | Amount to be displayed |
| `classes` | Object | See Below | Table classes |
| `pagination` | Object | {}  | (optional) props for gilbitron/laravel-vue-pagination |

### Default Classes
```json
{
    "table-container": {
        "table-responsive": true,
    },
    "table": {
        "table": true,
        "table-striped": true,
        "table-dark": true,
    },
    "t-head": {

    },
    "t-body": {
        
    },
    "td": {

    },
    "th": {
        
    },
}
```

### Column Props
| Name | Type | Default | Description  
| --- | --- | --- | --- |
| `label ` | String | "" | The JSON url |
| `name` | String | "" | The table columns |
| `filterable` | Boolean | false | Is Column filterable |
| `component` | Component | null | a dynamic component that can be injected |
| `classes` | Object | {} | Component classes to parse |


## Using dynamic components
You can also inject your own components into the table such as buttons. Your buttons, links etc can also listen for events.

### Example button component (ExampleButton.vue)

```html
<template>
    <button :class="classes" @click="click(data)" title="Update">
        <span>
            <i class="fa fa-eye" aria-hidden="true"></i>
        </span>
        &nbsp;
        {{ name }}
    </button>
</template>
```

```javascript
export default {
    props: {
        data: {},
        name: {},
        click: {},
        classes: {},
    }
}
```

### Datatable Columns (UserDataTable.vue)
```javascript

import ExampleButton './ExampleButton.vue';

export default {
	data() {
	    return {
	        url: 'http://vue-datatable.test/ajax',
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
                }
	            {
	                label: '',
	                name: 'View',
	                filterable: false,
	                component: ExampleButton,
	                click: this.alertMe,
	                classes: { 
	                    'btn': true,
	                    'btn-primary': true,
	                    'btn-sm': true,
	                } 
	            },
	        ]
	    }
	},
	components: {
	    // eslint-disable-next-line
	    ExampleButton,
	},
	methods: {
        alertMe(data) {
            alert("hey");
        }
    },
}
```

## Overriding the Filters &amp; Pagination Components:
If the included pagination or filters do not meet your requirements or if the styling is incorrect, they can be over-written using scoped slots.

### DataTable

```html
<data-table
    :url="url"
    :per-page="perPage"
    :columns="columns">

    <span slot="pagination" slot-scope="{ links, meta }">
        <paginator 
            @next="updateUrl"
            @prev="updateUrl"
            :meta="meta"
            :links="links">
        </paginator>
    </span>
</data-table>
```

Once the URL has been updated by your customer paginator or filters, the table will re-render. Alterativly, if updating the URL is troublesome, different table filters can be manipulated by your filters using the v-model directive:

### Example filter (DataTableFilter.vue)
This example filter will control the length of the table manipulating the tableData.length property using v-model.

```html
<template>
	<select
	    v-model="tableData.length"
	    class="form-control">
	    <option
	        :key="index"
	        :value="records"
	        v-for="(records, index) in perPage">
	        {{ records }}
	    </option>
	</select>
</template>
```

```javascript
export default {
    props: [
        "tableData", "perPage"
    ]
}
```

### DataTable

```html
<data-table
    :url="url"
    :per-page="perPage"
    :columns="columns">

    <span slot="filters" slot-scope="{ tableData, perPage }">
        <data-table-filters
            :table-data="tableData"
            :per-page="perPage">
        </data-table-filters>
    </span>
</data-table>
```
