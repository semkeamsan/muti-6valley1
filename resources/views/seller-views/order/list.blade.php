@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Order List'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="content container-fluid">

          <!-- Page Header -->
          <div class="page-header mb-1">
            <div class="flex-between align-items-center">
                <div>
                    <span class="h1"> <span class="page-header-title">{{ ucwords(str_replace('_',' ',$status)) }}</span> {{\App\CPU\translate('Order')}} {{\App\CPU\translate('status')}}</span>
                    <span class="badge badge-soft-dark mx-2">{{$orders->total()}}</span>
                </div>

            </div>
            <br>
            <!-- End Row -->

            <!-- order count overview -->

        <div class="card mt-2">
            <span class="h4 ml-3 mt-3">{{\App\CPU\translate('orders')}} {{\App\CPU\translate('status')}}</span>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                        <!-- Card -->
                        <a class="card card-hover-shadow h-100" href="{{route('seller.orders.list',['pending'])}}" style="background: #FFFFFF">
                            <div class="card-body">
                                <div class="flex-between align-items-center mb-1">
                                    <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <h6 class="card-subtitle" style="color: #F14A16!important;">{{\App\CPU\translate('pending')}}</h6>
                                        <span class="card-title h2" style="color: #F14A16!important;">


                                            {{ \App\Model\Order::where('order_type','default_type')
                                                                ->where(['seller_is'=>'seller'])
                                                                ->where(['seller_id'=>$sellerId])
                                                                ->where(['order_status'=>'pending'])
                                                                ->count()}}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <i class="tio-shopping-cart" style="font-size: 30px;color: #F14A16;"></i>
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </a>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                        <!-- Card -->
                        <a class="card card-hover-shadow h-100" href="{{route('seller.orders.list',['confirmed'])}}" style="background: #FFFFFF;">
                            <div class="card-body">
                                <div class="flex-between align-items-center mb-1">
                                    <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <h6 class="card-subtitle" style="color: #F14A16!important;">{{\App\CPU\translate('confirmed')}}</h6>
                                        <span class="card-title h2" style="color: #F14A16!important;">
                                            {{\App\Model\Order::where('order_type','default_type')
                                                                ->where('seller_is','seller')
                                                                ->where(['seller_id'=>$sellerId])
                                                                ->where(['order_status'=>'confirmed'])
                                                                ->count()}}
                                        </span>
                                    </div>

                                    <div class="mt-2">
                                        <i class="tio-checkmark-circle" style="font-size: 30px;color: #F14A16"></i>
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </a>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                        <!-- Card -->
                        <a class="card card-hover-shadow h-100" href="{{route('seller.orders.list',['processing'])}}" style="background: #FFFFFF">
                            <div class="card-body">
                                <div class="flex-between align-items-center gx-2 mb-1">
                                    <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <h6 class="card-subtitle" style="color: #F14A16!important;">{{\App\CPU\translate('Processing')}}</h6>
                                        <span class="card-title h2" style="color: #F14A16!important;">
                                            {{\App\Model\Order::where('order_type','default_type')
                                                                ->where(['order_status'=>'processing'])
                                                                ->where('seller_is','seller')
                                                                ->where(['seller_id'=>$sellerId])
                                                                ->count()}}
                                        </span>
                                    </div>

                                    <div class="mt-2">
                                        <i class="tio-time" style="font-size: 30px;color: #F14A16"></i>
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </a>
                        <!-- End Card -->
                    </div>

                    <div class="col-sm-6 col-lg-3 mb-3 mb-lg-5">
                        <!-- Card -->
                        <a class="card card-hover-shadow h-100" href="{{route('seller.orders.list',['out_for_delivery'])}}" style="background: #FFFFFFff">
                            <div class="card-body">
                                <div class="flex-between align-items-center gx-2 mb-1">
                                    <div style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <h6 class="card-subtitle" style="color: #F14A16!important;">{{\App\CPU\translate('out_for_delivery')}}</h6>
                                        <span class="card-title h2" style="color: #F14A16!important;">
                                            {{\App\Model\Order::where('order_type','default_type')
                                                            ->where('seller_is','seller')
                                                            ->where(['seller_id'=>$sellerId])
                                                            ->where(['order_status'=>'out_for_delivery'])
                                                            ->count()}}
                                        </span>
                                    </div>

                                    <div class="mt-2">
                                        <i class="tio-bike" style="font-size: 30px;color: #F14A16"></i>
                                    </div>
                                </div>
                                <!-- End Row -->
                            </div>
                        </a>
                        <!-- End Card -->
                    </div>

                    <div class="col-12">
                        <div class="card card-body" style="background: #FFFFFF!important;">
                            <div class="row gx-lg-4">
                                <div class="col-sm-6 col-lg-3">
                                    <div class="media flex-between align-items-center" style="cursor: pointer"
                                         onclick="location.href='{{route('seller.orders.list',['delivered'])}}'">
                                        <div class="media-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                            <h6 class="card-subtitle">{{\App\CPU\translate('delivered')}}</h6>
                                            <span class="card-title h3">
                                                {{\App\Model\Order::where('order_type','default_type')
                                                                    ->where('seller_is','seller')
                                                                    ->where(['seller_id'=>$sellerId])
                                                                    ->where(['order_status'=>'delivered'])
                                                                    ->count()}}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                                <i class="tio-checkmark-circle-outlined"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3 column-divider-sm">
                                    <div class="media flex-between align-items-center" style="cursor: pointer"
                                         onclick="location.href='{{route('seller.orders.list',['canceled'])}}'">
                                        <div class="media-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                            <h6 class="card-subtitle">{{\App\CPU\translate('canceled')}}</h6>
                                            <span class="card-title h3">
                                                {{\App\Model\Order::where('order_type','default_type')
                                                                    ->where('seller_is','seller')
                                                                    ->where(['seller_id'=>$sellerId])
                                                                    ->where(['order_status'=>'canceled'])
                                                                    ->count()}}
                                            </span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-remove-from-trash"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3 column-divider-lg">
                                    <div class="media flex-between align-items-center" style="cursor: pointer"
                                         onclick="location.href='{{route('seller.orders.list',['returned'])}}'">
                                        <div class="media-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                            <h6 class="card-subtitle">{{\App\CPU\translate('returned')}}</h6>
                                            <span class="card-title h3">
                                                {{\App\Model\Order::where('order_type','default_type')
                                                                    ->where('seller_is','seller')
                                                                    ->where(['seller_id'=>$sellerId])
                                                                    ->where(['order_status'=>'returned'])
                                                                    ->count()}}
                                            </span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-history"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3 column-divider-sm">
                                    <div class="media flex-between align-items-center" style="cursor: pointer"
                                         onclick="location.href='{{route('seller.orders.list',['failed'])}}'">
                                        <div class="media-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                            <h6 class="card-subtitle">{{\App\CPU\translate('failed')}}</h6>
                                            <span
                                                class="card-title h3">
                                                {{\App\Model\Order::where('order_type','default_type')
                                                                ->where('seller_is','seller')
                                                                ->where(['seller_id'=>$sellerId])
                                                                ->where(['order_status'=>'failed'])
                                                                ->count()}}
                                            </span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                          <i class="tio-message-failed"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div>
                <h2 class="h4 ml-3 mt-4">{{\App\CPU\translate('payment')}} {{\App\CPU\translate('status')}}</h2><br>
            </div>

            <div class="container-fluid mb-3">
                <div class="card">
                    <div class="card-body" style="background: #FFFFFF!important;">
                        <div class="row gx-lg-4">
                            <div class="col-sm-6 col-lg-6">
                                <div class="media flex-between align-items-center" style="cursor: pointer"
                                    onclick="location.href='{{route('seller.orders.list',['paid'])}}'">
                                    <div class="media-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <h6 class="card-subtitle">{{\App\CPU\translate('paid')}}</h6>
                                        <span class="card-title h3">
                                            {{\App\Model\Order::where('order_type','default_type')
                                                                ->where('seller_is','seller')
                                                                ->where(['seller_id'=>$sellerId])
                                                                ->where(['payment_status'=>'paid'])
                                                                ->count()}}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                            <i class="tio-checkmark-circle-outlined"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-lg-none">
                                    <hr>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-6 column-divider-sm">
                                <div class="media flex-between align-items-center" style="cursor: pointer"
                                    onclick="location.href='{{route('seller.orders.list',['unpaid'])}}'">
                                    <div class="media-body" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <h6 class="card-subtitle">{{\App\CPU\translate('unpaid')}}</h6>
                                        <span
                                            class="card-title h3">
                                            {{\App\Model\Order::where('order_type','default_type')
                                                                ->where('seller_is','seller')
                                                                ->where(['seller_id'=>$sellerId])
                                                                ->where(['payment_status'=>'unpaid'])
                                                                ->count()}}
                                        </span>
                                    </div>
                                    <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                    <i class="tio-message-failed"></i>
                                    </span>
                                </div>
                                <div class="d-lg-none">
                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- order count overview end-->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between flex-grow-1">
                            <div class="col-12 col-sm-4 col-md-4 mt-2">
                                <form action="{{ url()->current() }}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('search')}}" aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit" class="btn btn-primary">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                            <div class="col-12 col-sm-7 col-md-7">
                                <form action="" method="GET" id="form-data">
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <input  type="date" name="from" id="from_date" value="{{ $from }}"
                                                        class="form-control mt-2" title="{{ \App\CPU\translate('from') }} {{ \App\CPU\translate('date') }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <input type="date" name="to" id="to_date" value="{{ $to }}"
                                                        class="form-control mt-2" title="{{ ucfirst(\App\CPU\translate('to')) }} {{ \App\CPU\translate('date') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-right mt-2">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <button type="submit" class="btn btn-primary" onclick="formUrlChange(this)" data-action="{{ url()->current() }}">
                                                        {{\App\CPU\translate('filter')}}
                                                    </button>
                                                </div>
                                                <div class="col-6  col-md-6 text-left">
                                                    <button type="submit" class="btn btn-success" onclick="formUrlChange(this)" data-action="{{ route('seller.orders.order-bulk-export', ['status' => $status]) }}">
                                                        {{\App\CPU\translate('export')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{\App\CPU\translate('SL#')}}</th>
                                    <th>{{\App\CPU\translate('Order')}}</th>
                                    <th>{{\App\CPU\translate('Date')}}</th>
                                    <th>{{\App\CPU\translate('customer_name')}}</th>
                                    <th>{{\App\CPU\translate('Phone')}}</th>
                                    <th>{{\App\CPU\translate('Payment')}}</th>
                                    <th>{{\App\CPU\translate('Status')}} </th>
                                    <th style="width: 30px">{{\App\CPU\translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $k=>$order)
                                    <tr>
                                        <td>
                                            {{$orders->firstItem()+$k}}
                                        </td>
                                        <td>
                                            <a href="{{route('seller.orders.details',$order['id'])}}">{{$order['id']}}</a>
                                        </td>
                                        <td>{{date('d M Y',strtotime($order['created_at']))}}</td>
                                        <td> {{$order->customer ? $order->customer['f_name'].' '.$order->customer['l_name'] : 'Customer Data not found'}}</td>
                                        <td>{{ $order->customer ? $order->customer->phone : '' }}</td>
                                        <td>
                                            @if($order->payment_status=='paid')
                                                <span class="badge badge-soft-success">
                                                <span class="legend-indicator bg-success" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('paid')}}
                                                </span>
                                            @else
                                                <span class="badge badge-soft-danger">
                                                <span class="legend-indicator bg-danger" style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{\App\CPU\translate('unpaid')}}
                                                </span>
                                            @endif
                                            </td>
                                            <td class="text-capitalize ">
                                                @if($order->order_status=='pending')
                                                    <label
                                                        class="badge badge-primary">{{$order['order_status']}}</label>
                                                @elseif($order->order_status=='processing' || $order->order_status=='out_for_delivery')
                                                    <label
                                                        class="badge badge-warning">{{str_replace('_',' ',$order['order_status'])}}</label>
                                                @elseif($order->order_status=='delivered' || $order->order_status=='confirmed')
                                                    <label
                                                        class="badge badge-success">{{$order['order_status']}}</label>
                                                @elseif($order->order_status=='returned')
                                                    <label
                                                        class="badge badge-danger">{{$order['order_status']}}</label>
                                                @else
                                                    <label
                                                        class="badge badge-danger">{{$order['order_status']}}</label>
                                                @endif
                                            </td>
                                            <td>
                                                <a  class="btn btn-primary btn-sm mr-1"
                                                    title="{{\App\CPU\translate('view')}}"
                                                    href="{{route('seller.orders.details',[$order['id']])}}">
                                                    <i class="tio-visible"></i>

                                                </a>
                                                <a  class="btn btn-info btn-sm mr-1" target="_blank"
                                                    title="{{\App\CPU\translate('invoice')}}"
                                                    href="{{route('seller.orders.generate-invoice',[$order['id']])}}">
                                                    <i class="tio-download"></i>
                                                </a>
                                            </td>
                                        </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Footer -->
                     <div class="card-footer">
                        {{$orders->links()}}
                    </div>
                    @if(count($orders)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                        </div>
                    @endif
                    <!-- End Footer -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if(fr != ''){
                $('#to_date').attr('required','required');
            }
            if(to != ''){
                $('#from_date').attr('required','required');
            }
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{\App\CPU\translate('Invalid date range')}}!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
