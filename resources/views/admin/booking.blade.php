@extends('admin.layouts')
@section('extra-css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
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
    <h2 class="intro-y text-lg font-medium mt-10">
        All Booking
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-no-wrap items-center mt-2">
            <!-- <button class="button text-white bg-theme-1 shadow-md mr-2">Add New Product</button> -->
            <div class="mt-3">
                <div class="sm:grid grid-cols-5 gap-6">
                    <div class="relative mt-2">
                        <label>From Date</label>
                        <input type="date" class="input w-full border mt-2" name="from_date" id="from_date">
                    </div>
                    <div class="relative mt-2">
                        <label>To Date</label>
                        <input type="date" class="input w-full border mt-2" name="to_date" id="to_date">
                    </div>
                    <div class="relative mt-2">
                        <label>Status</label>
                        <select id="status" name="status" class="input w-full border mt-2">
                        <option value="status">SELECT</option>
                        <option value="0">Booking Accepted</option>
                        <option value="1">Booking Processing</option>
                        <option value="2">Booking Completed</option>
                        </select>
                    </div>
                    <div class="relative mt-2">
                        <button id="search" type="button" class="button w-24 bg-theme-1 text-white">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="datatable" class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-no-wrap">#</th>
                        <th class="whitespace-no-wrap">Date</th>
                        <th class="whitespace-no-wrap">Customer Details</th>
                        <th class="whitespace-no-wrap">Payment Type</th>
                        <th class="whitespace-no-wrap">Total</th>
                        <th class="whitespace-no-wrap">Payment Status</th>
                        <th class="whitespace-no-wrap">Status</th>
                        <th class="whitespace-no-wrap">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
    </div>
</div>
<!-- END: Content -->

<!-- BEGIN: Header & Footer Modal -->
<div class="modal" id="pickup_modal">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200">
            <h2 id="modal-title" class="font-medium text-base mr-auto">
                Update Driver
            </h2>
        </div>
        <form class="validate-form" id="pickup_form" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="pickup_booking_id" id="pickup_booking_id">
        <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
            <div class="col-span-12 sm:col-span-12">
                <label>Pickup Driver</label>
                <select class="input w-full border mt-2 flex-1" name="pickup_driver" id="pickup_driver">
                    <option value="">SELECT</option>
                    @foreach($staff as $key => $row)
                    <option value="{{$row->id}}">{{$row->name}} - {{$row->mobile}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        </form>
        <div class="px-5 py-3 text-right border-t border-gray-200">
            <button type="button" data-dismiss="modal" class="button w-20 border text-gray-700 mr-1">Cancel</button>
            <button onclick="UpdatePickup()" id="saveButton" type="button" class="button w-20 bg-theme-1 text-white">Update</button>
        </div>
    </div>
</div>
<!-- END: Header & Footer Modal -->

<!-- BEGIN: Header & Footer Modal -->
<div class="modal" id="delivery_modal">
    <div class="modal__content">
        <div class="flex items-center px-5 py-5 sm:py-3 border-b border-gray-200">
            <h2 id="modal-title" class="font-medium text-base mr-auto">
                Update Driver
            </h2>
        </div>
        <form class="validate-form" id="delivery_form" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" name="delivery_booking_id" id="delivery_booking_id">
        <div class="p-5 grid grid-cols-12 gap-4 row-gap-3">
            <div class="col-span-12 sm:col-span-12">
                <label>Delivery Driver</label>
                <select class="input w-full border mt-2 flex-1" name="delivery_driver" id="delivery_driver">
                    <option value="">SELECT</option>
                    @foreach($staff as $key => $row)
                    <option value="{{$row->id}}">{{$row->name}} - {{$row->mobile}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        </form>
        <div class="px-5 py-3 text-right border-t border-gray-200">
            <button type="button" data-dismiss="modal" class="button w-20 border text-gray-700 mr-1">Cancel</button>
            <button onclick="UpdateDelivery()" id="saveButton" type="button" class="button w-20 bg-theme-1 text-white">Update</button>
        </div>
    </div>
</div>
<!-- END: Header & Footer Modal -->

@endsection
@section('extra-js')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js
"></script>
<script type="text/javascript">
$('.booking').addClass('side-menu--active');

function search_url(){
  var from_date = $('#from_date').val();
  var to_date = $('#to_date').val();
  var fdate;
  var tdate;
  if(from_date!=""){
    fdate = from_date;
  }else{
    fdate = '1';
  }
  if(to_date!=""){
    tdate = to_date;
  }else{
    tdate = '1';
  }
  var status = $('#status').val();
  return '/admin/get-booking/'+fdate+'/'+tdate+'/'+status;
}

var orderPageTable = $('#datatable').DataTable({
    "processing": true,
    "serverSide": true,
    //"pageLength": 50,
    "ajax":{
        "url": search_url(),
        "dataType": "json",
        "type": "POST",
        "data":{ _token: "{{csrf_token()}}"}
    },
    "columns": [
        // { data: 'DT_RowIndex', name: 'DT_RowIndex'},
        { data: 'booking_id', name: 'booking_id'},
        { data: 'booking_date', name: 'booking_date' },
        { data: 'customer_details', name: 'customer_details' },
        { data: 'payment_type', name: 'payment_type' },
        { data: 'total', name: 'total' },
        { data: 'payment_status', name: 'payment_status' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action' },
    ]
});

$('#search').click(function(){
    var new_url = search_url();
    orderPageTable.ajax.url(new_url).load(null, false);
    //orderPageTable.draw();
});

function UpdatePayment(id){
    var r = confirm("Are you sure");
    if (r == true) {
      $.ajax({
        url : '/admin/update-booking-payment/'+id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Update',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
                var new_url = search_url();
                orderPageTable.ajax.url(new_url).load(null, false);
            });
        }
      });
    } 
}

function UpdateStatus(id,status){
    var r = confirm("Are you sure");
    if (r == true) {
      $.ajax({
        url : '/admin/update-booking-status/'+id+'/'+status,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Update',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
                var new_url = search_url();
                orderPageTable.ajax.url(new_url).load(null, false);
            });
        }
      });
    } 
}

function OpenPickup(id){
    $('input[name=pickup_booking_id]').val(id);
    $(".label-error").remove();
    $('.input').removeClass('error');
    $('#pickup_modal').modal('show');
}

function OpenDelivery(id){
    $('input[name=delivery_booking_id]').val(id);
    $(".label-error").remove();
    $('.input').removeClass('error');
    $('#delivery_modal').modal('show');
}

function UpdatePickup(){
  $(".label-error").remove();
  $('.input').removeClass('error');
  var formData = new FormData($('#pickup_form')[0]);
    $.ajax({
        url : '/admin/update-pickup',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {                
            $("#pickup_form")[0].reset();
            $('#pickup_modal').modal('hide');
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Update',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
                var new_url = search_url();
                orderPageTable.ajax.url(new_url).load(null, false);
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

function UpdateDelivery(){
  $(".label-error").remove();
  $('.input').removeClass('error');
  var formData = new FormData($('#delivery_form')[0]);
    $.ajax({
        url : '/admin/update-delivery',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(data)
        {                
            $("#delivery_form")[0].reset();
            $('#delivery_modal').modal('hide');
            Swal.fire({
                //title: "Please Check Your Email",
                text: 'Successfully Update',
                type: "success",
                confirmButtonClass: 'button text-white bg-theme-1 shadow-md mr-2',
                buttonsStyling: false,
            }).then(function() {
                var new_url = search_url();
                orderPageTable.ajax.url(new_url).load(null, false);
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
</script>
@endsection
