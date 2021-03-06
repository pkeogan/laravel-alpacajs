@include('alpacajs::imports')

@includeWhen($button, 'alpacajs::button')

<div class="modal fade" id="{{$id}}-modal" role="dialog" aria-labelledby="modal">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="box box-{{$modal['style']}}" id="{{$id}}-box">
          @if($modal['header'])
          <div class="box-header with-border">
            <h3 class="box-title">{{ $modal['header']['text']  }}</h3>
            <div class="box-tools pull-right">
                    @if($modal['header']['close'])<button class="btn btn-box-tool"class="close" data-dismiss="modal" aria-label="Close" data-toggle="tooltip" title="Close"><i class="fa fa-times"></i></button>@endif
            </div>
          </div>
          @endif
          <div class="box-body">
            <div id="{{ $id }}"></div>
          </div>
          <div class="box-footer">
                @if($modal['buttons']['cancel'])<button type="button" class="btn btn-{{ $modal['buttons']['cancel']['style'] }} pull-left" id="{{$id}}_cancel" data-dismiss="modal">{{ $modal['buttons']['cancel']['text'] }}</button>@endif
                @if($modal['buttons']['clear'])<button type="button" class="btn btn-{{ $modal['buttons']['clear']['style'] }} pull-left" id="{{$id}}_clear">{{ $modal['buttons']['clear']['text'] }}</button>@endif
                @if($modal['buttons']['reset'])<button type="button" class="btn btn-{{ $modal['buttons']['reset']['style'] }} pull-left" id="{{$id}}_reset">{{ $modal['buttons']['reset']['text'] }}</button>@endif
                @if($modal['buttons']['update'])<button id="{{$id}}_update" type="button" class="btn btn-{{ $modal['buttons']['update']['style'] }} pull-right" >{{ $modal['buttons']['update']['text'] }}</button>@endif
                @if($modal['buttons']['create'])<button id="{{$id}}_create" type="button" class="btn btn-{{ $modal['buttons']['create']['style'] }} pull-right" >{{ $modal['buttons']['create']['text'] }}</button>@endif
          </div>
        </div>
    </div>
  </div>
</div>


@push('scriptsdocumentready')

var lastAlpacaModelID;

@if(! isset($postRenderSource))
  $("#{{ $id }}").alpaca({
    @if(isset($schema))"schema": {!! $schema !!}, @endif
    @if(isset($options))"options": {!! $options !!}, @endif
    @if(isset($postRender2))"postRender": function(control){ {!! $postRender !!} }, @endif
    @if(isset($schemaSource))"schemaSource": "{{ $schemaSource }}", @endif
    @if(isset($optionsSource))"optionsSource": "{{ $optionsSource }}", @endif
    @if(isset($postRenderSource))"postRenderSource": function(control){ {{ $postRenderSource }} }, @endif
  });
@else

$(document).alpacaCreateWithPostRenderSource( '{{$id}}', '{{$schemaSource}}', '{{$optionsSource}}', '{{$postRenderSource}}' );

@endif

  @if($openModalAndLoad)

    jQuery.fn.extend({
        editModel: function (id) {
          lastAlpacaModelID = id;
          var dataSource = "{{ $dataRoute }}/" + id;
          var schema = $("#{{$id}}").alpaca().schema;
          var options = $("#{{$id}}").alpaca().options;
          $.ajax({
            type: "GET",
            url: dataSource,
            contentType: "application/json",
            processData: false,
            success: function(data) {
                $("#{{$id}}").alpaca().destroy();
                $("#{{$id}}").alpaca({"schema":schema,"options":options,"dataSource":dataSource @if(isset($postRender)),"postRender": eval({!! $postRender !!}) @endif});
                $("#{{$id}}-modal").modal('show');  
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
				$(document).handleError(XMLHttpRequest);
			},
         });
        }
    });
  @endif


  @if($modal['buttons']['cancel']) 
  $("#{{$id}}_cancel").click(function() {
      $('#{{ $id }}').alpaca().clear();
  });
  @endif

  @if($modal['buttons']['clear'])
  $("#{{$id}}_clear").click(function() {
      $('#{{ $id }}').alpaca().setValue(lastAlpacaJson);
  });
  @endif

  @if($modal['buttons']['reset'])
  $("#{{$id}}_reset").click(function() {
        $('#{{ $id }}').alpaca().setValue(lastAlpacaJson);
  });
  @endif

  @if($modal['buttons']['update'])

  $("#{{$id}}_update").click(function() {
    $("#{{$id}}-box").append('<div data-overlay-loader class="overlay"><i class="fas fa-spinner fa-pulse"></i></div>');
    var data = $('#{{ $id }}').alpaca().getValue();
    $.ajax({
      type: "POST",
      url: "{{ $updateRoute }}/" + lastAlpacaModelID ,
      data: JSON.stringify(data),
      contentType: "application/json",
      processData: false,
      success: function(data) {
@if(isset($calendar['refresh'])) $('#calendar').fullCalendar('refetchEvents'); @endif
          $("table[id*=datatable]").DataTable().ajax.reload(null, false);
          $('.modal').modal('hide');
          $("[data-overlay-loader]").remove();
           swal({
                type: 'success',
                title: 'Success',
                showConfirmButton: false,
                timer: 1500
              });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
       	$(document).handleError(XMLHttpRequest);
      },
    });
  });
  @endif

  @if($modal['buttons']['create'])
  jQuery.fn.extend({
        createModel: function () {
           $("#{{$id}}-modal").modal('toggle');
        }
    });


  $("#{{$id}}_create").click(function() {
    $("#{{$id}}-box").append('<div data-overlay-loader class="overlay"><i class="fas fa-spinner fa-pulse"></i></div>');
    var data = $('#{{ $id }}').alpaca().getValue();
    $.ajax({
      type: "POST",
      url: "{{ $storeRoute }}",
      data: JSON.stringify(data),
      contentType: "application/json",
      processData: false,
      success: function(data) {
     @if(isset($calendar['refresh'])) $('#calendar').fullCalendar('refetchEvents'); @endif

          $("table[id*=datatable]").DataTable().ajax.reload(null, false);
          $('.modal').modal('hide'); 
          $("[data-overlay-loader]").remove();
            swal({
                type: 'success',
                title: 'Success',
                showConfirmButton: false,
                timer: 1500
              });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
		$(document).handleError(XMLHttpRequest);
      },
    });
  });
  @endif

@endpush