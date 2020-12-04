<?php

namespace Backpack\DownloadOperation;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;

trait BulkDownloadOperation
{
    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupBulkDownloadDefaults()
    {
        $this->crud->allowAccess('download');

        $this->crud->operation('list', function () {
            $this->crud->enableBulkActions();
            $this->crud->addButton('bottom', 'bulk_download', 'view', 'backpack.downloadoperation::buttons.bulk_download');
        });
    }
}
