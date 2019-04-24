Assets
php artisan vendor:publish --provider="JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider" --tag="vue-components"

Config
JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider::class,


Trait
use App\Traits\DatatableTrait;

use DatatableTrait;

Controller
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

$data = Project::dataTableQuery($column, $dir, $length, $searchValue);

return new DataTableCollectionResource($data);