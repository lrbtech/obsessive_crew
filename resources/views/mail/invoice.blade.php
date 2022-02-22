<html>

<body style="background-color:#e2e1e0;font-family: Open Sans, sans-serif;font-size:100%;font-weight:400;line-height:1.4;color:#000;">
  <table style="max-width:670px;margin:50px auto 10px;background-color:#fff;padding:50px;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);-moz-box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24);box-shadow:0 1px 3px rgba(0,0,0,.12),0 1px 2px rgba(0,0,0,.24); border-top: solid 10px green;">
    <thead>
      <tr>
        <th style="text-align:left;">
        <?php
        $url = asset('');
        ?>
            <!-- <img style="max-width: 150px;" src="{{$url}}images/logo_icon.png"> -->
            Visit <a href="http://autotech.ae/">Autotech.ae</a>
        </th>
        <th style="text-align:right;font-weight:400;">{{date('d-m-Y',strtotime($booking->date))}}</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="height:35px;"></td>
      </tr>
      <tr>
        <td colspan="2" style="border: solid 1px #ddd; padding:10px 20px;">
          <!-- <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Order status</span><b style="color:green;font-weight:normal;margin:0">Success</b></p> -->
          <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:150px">Order status</span><b style="color:green;font-weight:normal;margin:0">
            @if($booking->status == 0)
            Order Placed
            @elseif($booking->status == 1)
            Order Accepted
            @elseif($booking->status == 2)
            Received
            @elseif($booking->status == 3)
            Processing
            @elseif($booking->status == 4)
            Completed
            @elseif($booking->status == 5)
            Delivered
            @endif
            </b></p>
          <!-- <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Transaction ID</span> abcd1234567890</p> -->
          <p style="font-size:14px;margin:0 0 6px 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Booking ID</span> {{$booking->booking_id}}</p>
          <p style="font-size:14px;margin:0 0 0 0;"><span style="font-weight:bold;display:inline-block;min-width:146px">Order amount</span> AED {{$booking->total}}</p>
        </td>
      </tr>
      <tr>
        <td style="height:35px;"></td>
      </tr>
      <tr>
        <td style="width:50%;padding:20px;vertical-align:top">
          <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px">Name</span> {{$customer->first_name}} {{$customer->last_name}}</p>
          <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Email</span> {{$customer->email}}</p>
          <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Phone</span> {{$customer->mobile}}</p>
          <!-- <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">ID No.</span> 2556-1259-9842</p> -->
        </td>
        <td style="width:50%;padding:20px;vertical-align:top">
          <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Address</span> {{$booking->address}}</p>
          <!-- <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Number of gusets</span> 2</p>
          <p style="margin:0 0 10px 0;padding:0;font-size:14px;"><span style="display:block;font-weight:bold;font-size:13px;">Duration of your vacation</span> 25/04/2017 to 28/04/2017 (3 Days)</p> -->
        </td>
      </tr>
      <tr>
        <td colspan="2" style="font-size:20px;padding:30px 15px 0 15px;">Booking Details</td>
      </tr>
      <tr>
        <td colspan="2" style="padding:15px;">
          @if(count($booking_service)>0)
            @foreach($booking_service as $row)
            <p style="font-size:14px;margin:0;padding:10px;border:solid 1px #ddd;font-weight:bold;">
                <span style="display:block;font-size:13px;font-weight:normal;">{{$row->service_name_english}}</span> AED {{$row->price}} <b style="font-size:12px;font-weight:300;"> /(Service)</b>
            </p>
            @endforeach
          @endif
        </td>
      </tr>
    </tbody>
    <tfooter>
      <!-- <tr>
        <td colspan="2" style="font-size:14px;padding:50px 15px 0 15px;">
          <strong style="display:block;margin:0 0 10px 0;">Regards</strong> Bachana Tours<br> Gorubathan, Pin/Zip - 735221, Darjeeling, West bengal, India<br><br>
          <b>Phone:</b> 03552-222011<br>
          <b>Email:</b> contact@bachanatours.in
        </td>
      </tr> -->
    </tfooter>
  </table>
</body>

</html>