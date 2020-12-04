@if ($crud->hasAccess('download') && $crud->get('list.bulkActions'))
  <a href="javascript:void(0)" onclick="bulkDownloadEntries(this)" class="btn btn-sm btn-secondary bulk-button"><i class="la la-download"></i> {{ trans('backpack.downloadoperation::downloadoperation.download') }}</a>
@endif

@push('after_scripts')
<script>
  if (typeof bulkDownloadEntries != 'function') {
    function bulkDownloadEntries(button) {

        if (typeof crud.checkedItems === 'undefined' || crud.checkedItems.length == 0)
        {
          new PNotify({
                title: "{{ trans('backpack::crud.bulk_no_entries_selected_title') }}",
                text: "{{ trans('backpack::crud.bulk_no_entries_selected_message') }}",
                type: "warning"
            });

          return;
        }

        var message = "{{ trans('backpack.downloadoperation::downloadoperation.are_you_sure') }}";
        message = message.replace(":number", crud.checkedItems.length);


        // show confirm message
        swal({
          title: "{!! trans('backpack::base.warning') !!}",
          text: message,
          icon: "warning",
          buttons: {
            cancel: {
            text: "{!! trans('backpack::crud.cancel') !!}",
            value: null,
            visible: true,
            className: "bg-secondary",
            closeModal: true,
          },
            download: {
            text: "{{ trans('backpack.downloadoperation::downloadoperation.download') }}",
            value: true,
            visible: true,
            className: "bg-primary",
          }
          },
        }).then((value) => {
          if (!value) {
            return;
          }

          var items = crud.checkedItems;
          var items_count = items.length;
          var current_item = 1;

          // function to trigger the ajax call
          var ajax_request = function(item) {
              var deferred = $.Deferred();
              var operation_route = "{{ url($crud->route) }}/"+item+"/download";

              new Noty({
                type: "info",
                text: "<strong>{{ trans('backpack.downloadoperation::downloadoperation.downloading') }} "+current_item+"/"+items_count+"!</strong><br>{{ trans('backpack.downloadoperation::downloadoperation.please_wait') }}"
              }).show();

              $.ajax({
                url: operation_route,
                type: 'GET',
                xhrFields: {
                  responseType: 'blob' // to avoid binary data being mangled on charset conversion
                },
                success: function(blob, status, xhr) {
                  // trigger download in the browser 
                  downloadResponse(blob, status, xhr);
                  // mark the ajax call as completed
                  deferred.resolve(item);
                },
                error: function(error) {
                  new Noty({
                      type: "danger",
                      text: "{{ trans('backpack.downloadoperation::downloadoperation.downloading_failed') }}"
                    }).show();

                  // mark the ajax call as failed
                  deferred.reject(item);
                }
              });

              current_item++;

              return deferred.promise();
            };

          var looper = $.Deferred().resolve();

          // go through each item and call the ajax function
          $.when.apply($, $.map(items, function(item, i) {
            looper = looper.then(function() {
              // trigger ajax call with item data
              return ajax_request(item);
            });

            return looper;
          })).then(function() {
            // run this after all ajax calls have completed
            console.log('Done!');
          });

        });

      }
  }

  if (typeof downloadResponse != 'function') {
    function downloadResponse(blob, status, xhr) {
      var filename = "";
      var disposition = xhr.getResponseHeader('Content-Disposition');
      if (disposition && disposition.indexOf('attachment') !== -1) {
          var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
          var matches = filenameRegex.exec(disposition);
          if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
      }

      if (typeof window.navigator.msSaveBlob !== 'undefined') {
          // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
          window.navigator.msSaveBlob(blob, filename);
      } else {
          var URL = window.URL || window.webkitURL;
          var downloadUrl = URL.createObjectURL(blob);

          if (filename) {
              // use HTML5 a[download] attribute to specify filename
              var a = document.createElement("a");
              // safari doesn't support this yet
              if (typeof a.download === 'undefined') {
                  window.location.href = downloadUrl;
              } else {
                  a.href = downloadUrl;
                  a.download = filename;
                  document.body.appendChild(a);
                  a.click();
              }
          } else {
              window.location.href = downloadUrl;
          }

          setTimeout(function () { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup
      }
    }
  }
</script>
@endpush