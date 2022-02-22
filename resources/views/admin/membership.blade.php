@extends('admin.layouts')
@section('extra-css')
@endsection
@section('body-section')
<div class="content">
    <!-- BEGIN: Top Bar -->
    <div class="top-bar">
        <!-- BEGIN: Breadcrumb -->
        <div class="-intro-x breadcrumb mr-auto hidden sm:flex"> <a href="" class="">Application</a> <i data-feather="chevron-right" class="breadcrumb__icon"></i> <a href="" class="breadcrumb--active">Dashboard</a> </div>
        <!-- END: Breadcrumb -->
        @include('admin.header')
    </div>
    <!-- END: Top Bar -->
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Membership List
        </h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <button id="add_new" class="button text-white bg-theme-1 shadow-md mr-2">Add New Membership</button>
        </div>
    </div>
    <!-- BEGIN: Datatable -->
    <div class="intro-y datatable-wrapper box p-5 mt-5">
        <table class="table table-report table-report--bordered display datatable w-full">
            <thead>
                <tr>
                    <th class="border-b-2 whitespace-no-wrap">#</th>
                    <th class="border-b-2 whitespace-no-wrap">Name</th>
                    <th class="border-b-2 whitespace-no-wrap">From Value</th>
                    <th class="border-b-2 whitespace-no-wrap">To Value</th>
                    <th class="border-b-2 whitespace-no-wrap">Discount</th>
                    <th class="border-b-2 text-center whitespace-no-wrap">Status</th>
                    <th class="border-b-2 text-center whitespace-no-wrap">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($membership as $key => $row)
                <tr>
                    <td class="text-center border-b">{{$key + 1}}</td>
                    <td><a>{{$row->name}}</a></td>
                    <td><a>{{$row->from_value}}</a></td>
                    <td><a>{{$row->to_value}}</a></td>
                    <td><a>{{$row->discount}} (%)</a></td>
                    <td class="w-40">
                        @if($row->status == 0)
                        <div class="flex items-center justify-center text-theme-9"> <i data-feather="check-square" class="w-4 h-4 mr-2"></i> Active </div>
                        @else 
                        <div class="flex items-center justify-center text-theme-6"> <i data-feather="check-square" class="w-4 h-4 mr-2"></i> Inactive </div>
                        @endif
                    </td>
                    <td class="table-report__action w-56">
                        <div class="flex justify-center items-center">
                            <a onclick="Edit({{$row->id}})" class="flex items-center mr-3" href="javascript:;"> <i data-feather="check-square" class="w-4 h-4 mr-1"></i> Edit </a>
                            @if($row->status == 1)
                            <a onclick="Delete({{$row->id}},0)" class="flex items-center justify-center text-theme-9" href="javascript:;"> <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> Active </a>
                            @else 
                            <a onclick="Delete({{$row->id}},1)" class="flex items-center justify-center text-theme-6" href="javascript:;"> <i data-feather="trash-2" class="w-4 h-4 mr-1"></i> InActive </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- END: Datatable -->
</div>
<!-- END: Content -->


<!-- BEGIN: Header & Footer Modal -->
<div class="modal" id="popup_modal">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200">
            <h2 id="modal-title" class="font-medium text-base mr-auto">
                Add membership
            </h2>
        </div>
        <form class="validate-form" id="form" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="id" id="id">
        <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
            <div class="col-span-12 sm:col-span-12">
                <label>Membership Name</label>
                <input autocomplete="off" type="text" class="input w-full border mt-2 flex-1" name="name" id="name">
            </div>
            <div class="col-span-12 sm:col-span-12">
                <label>From Value</label>
                <input autocomplete="off" type="number" class="input w-full border mt-2 flex-1" name="from_value" id="from_value">
            </div>
            <div class="col-span-12 sm:col-span-12">
                <label>To Value</label>
                <input autocomplete="off" type="number" class="input w-full border mt-2 flex-1" name="to_value" id="to_value">
            </div>
            <div class="col-span-12 sm:col-span-12">
                <label>Discount (%)</label>
                <input autocomplete="off" type="number" class="input w-full border mt-2 flex-1" name="discount" id="discount">
            </div>
        </div>
        </form>
        <div class="px-5 py-3 text-right border-t border-gray-200">
            <button type="button" data-dismiss="modal" class="button w-20 border text-gray-700 mr-1">Cancel</button>
            <button onclick="Save()" id="saveButton" type="button" class="button w-20 bg-theme-1 text-white">Save</button>
        </div>
    </div>
</div>
<!-- END: Header & Footer Modal -->
@endsection
@section('extra-js')

<script type="text/javascript">
$('.membership').addClass('side-menu--active');
$('.settings_master').addClass('side-menu--active side-menu--open');
$('.settings').addClass('side-menu__sub-open');

var action_type;
$('#add_new').click(function(){
    $('#popup_modal').modal('show');
    $("#form")[0].reset();
    action_type = 1;
    $(".label-error").remove();
    $('.input').removeClass('error');
    $('#saveButton').text('Save');
    $('#modal-title').text('Add membership');
});

function Save(){
    $(".label-error").remove();
  $('.input').removeClass('error');
  var formData = new FormData($('#form')[0]);
  if(action_type == 1){
    $.ajax({
        url : '/admin/save-membership',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {                
            $("#form")[0].reset();
            $('#popup_modal').modal('hide');
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Save',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
              $('.datatable-wrapper').load(location.href+' .datatable-wrapper');
            });
        },error: function (data) {
            var errorData = data.responseJSON.errors;
            $.each(errorData, function(i, obj) {
                $("#"+i).after('<label class="label-error error" for="'+i+'">'+obj[0]+'</label>');
                $('#'+i).addClass('error');
            });
        }
    });
  }else{
    $.ajax({
        url : '/admin/update-membership',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {                
            $("#form")[0].reset();
            $('#popup_modal').modal('hide');
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Update',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
              $('.datatable-wrapper').load(location.href+' .datatable-wrapper');
            });
        },error: function (data) {
            var errorData = data.responseJSON.errors;
            $.each(errorData, function(i, obj) {
                $("#"+i).after('<label class="label-error error" for="'+i+'">'+obj[0]+'</label>');
                $('#'+i).addClass('error');
            });
        }
    });
  }
}

function Edit(id){
  $.ajax({
    url : '/admin/edit-membership/'+id,
    type: "GET",
    dataType: "JSON",
    success: function(data)
    {
      $('#modal-title').text('Update membership');
      $('#save').text('Save Change');
      $('input[name=name]').val(data.name);
      $('input[name=from_value]').val(data.from_value);
      $('input[name=to_value]').val(data.to_value);
      $('input[name=discount]').val(data.discount);
      $('input[name=id]').val(id);
      action_type = 2;
      $(".label-error").remove();
      $('.input').removeClass('error');
      $('#popup_modal').modal('show');
    }
  });
}

function Delete(id,status){
    var r = confirm("Are you sure");
    if (r == true) {
      $.ajax({
        url : '/admin/delete-membership/'+id+'/'+status,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Delete',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
              $('.datatable-wrapper').load(location.href+' .datatable-wrapper');
            });
        }
      });
    } 
}
</script>
@endsection