@extends('layouts.back-end.app')
{{--@section('title','Customer')--}}
@section('title', \App\CPU\translate('order_settings'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{\App\CPU\translate('order_settings')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card gx-2 gx-lg-3">
            <div class="card-body">
                <form action="{{route('admin.business-settings.order-settings.update-order-settings')}}" method="post" enctype="multipart/form-data" id="add_fund">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            @php($billing_input_by_customer=\App\CPU\Helpers::get_business_settings('billing_input_by_customer'))
                            <div class="form-group">
                                <label>{{\App\CPU\translate('billing_input_by_customer')}}</label><small
                                    style="color: red">*</small>
                                <div class="input-group input-group-md-down-break">
                                    <!-- Custom Radio -->
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="1"
                                                name="billing_input_by_customer"
                                                id="billing_input_by_customer1" {{$billing_input_by_customer==1?'checked':''}}>
                                            <label class="custom-control-label"
                                                for="billing_input_by_customer1">{{\App\CPU\translate('active')}}</label>
                                        </div>
                                    </div>
                                    <!-- End Custom Radio -->

                                    <!-- Custom Radio -->
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="0"
                                                name="billing_input_by_customer"
                                                id="billing_input_by_customer2" {{$billing_input_by_customer==0?'checked':''}}>
                                            <label class="custom-control-label"
                                                for="billing_input_by_customer2">{{\App\CPU\translate('deactive')}}</label>
                                        </div>
                                    </div>
                                    <!-- End Custom Radio -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" id="submit" class="btn btn-primary">{{\App\CPU\translate('submit')}}</button>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>
@endsection

@push('script_2')

@endpush
