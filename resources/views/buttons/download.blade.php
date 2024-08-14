@if ($crud->hasAccess('download'))
	<a href="{{ url($crud->route.'/'.$entry->getKey().'/download') }}" bp-button="download" class="btn btn-sm btn-link"><i class="la la-download"></i><span> {{ trans('backpack.downloadoperation::downloadoperation.download') }}</span></a>
@endif