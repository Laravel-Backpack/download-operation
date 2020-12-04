@if ($crud->hasAccess('download'))
	<a href="{{ url($crud->route.'/'.$entry->getKey().'/download') }} " class="btn btn-sm btn-link"><i class="la la-download"></i> {{ trans('backpack.downloadoperation::downloadoperation.download') }}</a>
@endif