<?php

namespace Backpack\DownloadOperation;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;
use Spatie\Browsershot\Browsershot;

trait DownloadOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDownloadRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/download', [
            'as'        => $routeName.'.download',
            'uses'      => $controller.'@download',
            'operation' => 'download',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupDownloadDefaults()
    {
        $this->crud->allowAccess('download');

        $this->crud->operation('download', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();

            // if no default setting have been registered using config file
            // or therwise, then use some generics
            if (!$this->crud->get('download.view')) {
                $this->crud->set('download.view', 'crud::show');
                $this->crud->set('show.contentClass', 'col-md-12');
            }
            if (!$this->crud->get('download.format')) {
                $this->crud->set('download.format', 'A4');
            }
            if (!$this->crud->get('download.headers')) {
                $this->crud->set('download.headers', ['Content-Type' => 'application/pdf']);
            }
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButton('line', 'download', 'view', 'backpack.downloadoperation::buttons.download');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function download()
    {
        $this->crud->hasAccessOrFail('download');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        // get the info for that entry
        $data['crud'] = $this->crud;
        $data['entry'] = $this->crud->getModel()->findOrFail($id);
        $data['title'] = $this->crud->getOperationSetting('title') ?? ucfirst($this->crud->entity_name).' '.$data['entry']->getKey();
        $data['filename'] = $data['title'] . '.pdf';
        $data['format'] = $this->crud->get('download.format');
        $data['view'] = $this->crud->get('download.view');
        $data['headers'] = $this->crud->get('download.headers');

        return $this->downloadFile($data);
    }

    /**
     * Download the file.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function downloadFile($data)
    {
        return response()->streamDownload(function () use ($data) {
            echo Browsershot::html(view($data['view'], $data))
                ->format($data['format'])
                ->pdf();
        }, $data['filename'], $data['headers']);
    }
}
