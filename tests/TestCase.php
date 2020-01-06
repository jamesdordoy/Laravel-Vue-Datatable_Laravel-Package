<?php

use PHPUnit\Framework\TestCase;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class MyTest extends Orchestra\Testbench\TestCase
{ 
    public function test_is_working()
    {
        $data = collect(["hi"]);

        $resource = new DataTableCollectionResource($data);

        var_dump($resource);
    }

    protected function getPackageProviders($app)
    {
        return ['JamesDordoy\LaravelVueDatatable\Providers\LaravelVueDatatableServiceProvider'];
    }
}