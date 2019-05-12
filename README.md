
# Laravel Vue Datatable
A Vue.js datatable component for Laravel that works with Bootstrap.

## Requirements

* [Vue.js](https://vuejs.org/) 2.x
* [Laravel](http://laravel.com/docs/) 5.x
* [Bootstrap](http://getbootstrap.com/) 4

This package makes use of the Laravel Vue Pagination component created by [gilbitron](https://github.com/gilbitron/laravel-vue-pagination). If you need a pagination component for other areas of your website and you are using a Laravel API i highly suggest using this flexible component.

## Demo

See [https://jamesdordoy.github.io/vue-datatable/](https://jamesdordoy.github.io/vue-datatable/)

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

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class User extends Authenticatable
{
    use Notifiable, LaravelVueDatatableTrait;

    protected $dataTableColumns = [
        'id' => [
            'searchable' => false,
        ],
        'name' => [
            'searchable' => true,
        ],
        'email' => [
            'searchable' => true,
        ]
    ];
}
```

## Use the Controller Resource
```php
<?php

namespace App\Http\Controllers\Back;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class UserController extends Controller
{
    public function index(Request $request)
    {   
        $length = $request->input('length');
        $column = $request->input('column'); //Index
        $dir = $request->input('dir', 'asc');
        $searchValue = $request->input('search');

        $data = User::dataTableQuery($column, $dir, $length, $searchValue);

        return new DataTableCollectionResource($data);
    }
}
```

## Component Installation

```bash
npm install laravel-vue-datatable
```

### Register the Plugin

```javascript
import DataTable from 'laravel-vue-datatable';
Vue.use(DataTable);
```

### Basic Example 

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
| `columns` | Array | [] | The table columns |
| `per-page` | Array | [ '10', '25', '50' ] | (optional) Amount to be displayed |
| `classes` | Object | See Below | (optional) Table classes |
| `pagination` | Object | {}  | (optional) props for [gilbitron/laravel-vue-pagination](https://github.com/gilbitron/laravel-vue-pagination#props) |

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
| `name` | String | "" | The table column header name |
| `width` | Number | 0 | The table column width |
| `filterable` | Boolean | false | Is the column filterable |
| `component` | Component | null | A dynamic component that can be injected |
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
    :columns="columns"
    :per-page="perPage">

    <span slot="pagination" slot-scope="{ links, meta }">
        <paginator 
            :meta="meta"
            :links="links"
            @next="updateUrl"
            @prev="updateUrl">
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
        class="form-control"
        v-model="tableData.length">
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
    :columns="columns"
    :per-page="perPage">

    <span slot="filters" slot-scope="{ tableData, perPage }">
        <data-table-filters
            :per-page="perPage"
            :table-data="tableData">
        </data-table-filters>
    </span>
</data-table>
```
