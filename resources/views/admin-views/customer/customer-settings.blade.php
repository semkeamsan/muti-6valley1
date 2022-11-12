@extends('layouts.back-end.app')
{{--@section('title','Customer')--}}
@section('title', \App\CPU\translate('customer_settings'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">{{\App\CPU\translate('customer_settings')}}</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <div class="card gx-2 gx-lg-3">
        <div class="card-body">
            <form action="{{ route('admin.customer.customer-settings-update') }}" method="post" enctype="multipart/form-data" id="update-settings">
                @csrf
                <div class="row pb-4">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="customer_wallet">
                            <span class="pr-2">{{\App\CPU\translate('customer_wallet')}} :</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="section_visibility('customer_wallet')" name="customer_wallet" id="customer_wallet" value="1" data-section="wallet-section" {{isset($data['wallet_status'])&&$data['wallet_status']==1?'checked':''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="customer_loyalty_point">
                            <span class="pr-2">{{\App\CPU\translate('customer_loyalty_point')}}:</span>
                                <input type="checkbox" class="toggle-switch-input" onclick="section_visibility('customer_loyalty_point')" name="customer_loyalty_point" id="customer_loyalty_point" data-section="loyalty-point-section" value="1" {{isset($data['loyalty_point_status'])&&$data['loyalty_point_status']==1?'checked':''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <small class="nav-subtitle text-secondary border-bottom wallet-section">{{\App\CPU\translate('wallet')}} {{\App\CPU\translate('settings')}}</small>
                <div class="row pt-2 pb-4 wallet-section">
                    <div class="col-sm-12 col-12">
                        <div class="form-group">
                            <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="refund_to_wallet">
                            <span class="pr-2">{{\App\CPU\translate('refund_to_wallet')}}<span class="input-label-secondary" title="{{\App\CPU\translate('refund_to_wallet_hint')}}"><img src="{{asset('/public/assets/back-end/img/info-circle.svg')}}" alt="{{\App\CPU\translate('show_hide_food_menu')}}"></span> :</span>
                                <input type="checkbox" class="toggle-switch-input" name="refund_to_wallet" id="refund_to_wallet" value="1" {{isset($data['wallet_add_refund'])&&$data['wallet_add_refund']==1?'checked':''}}>
                                <span class="toggle-switch-label text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <small class="nav-subtitle text-secondary border-bottom loyalty-point-section">{{\App\CPU\translate('customer_loyalty_point')}} {{\App\CPU\translate('settings')}}</small>
                <div class="row pt-2 loyalty-point-section">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label class="input-label" for="loyalty_point_exchange_rate">1 {{\App\CPU\Helpers::currency_code()}} = {{\App\CPU\translate('how_much_point')}}</label>
                            <input type="number" class="form-control" name="loyalty_point_exchange_rate" step="1" value="{{$data['loyalty_point_exchange_rate']??'0'}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label class="input-label" for="intem_purchase_point">{{\App\CPU\translate('percentage_of_loyalty_point_on_order_amount')}}</label>
                            <input type="number" class="form-control" name="item_purchase_point" step=".01" value="{{$data['loyalty_point_item_purchase_point']??'0'}}">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label class="input-label" for="intem_purchase_point">{{\App\CPU\translate('minimum_point_to_transfer')}}</label>
                            <input type="number" class="form-control" name="minimun_transfer_point" min="0" step="1" value="{{$data['loyalty_point_minimum_point']??'0'}}">
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
    <script>
        $(document).on('ready', function () {

            @if (isset($data['wallet_status'])&&$data['wallet_status']!=1)
                $('.wallet-section').hide();
            @endif

            @if (isset($data['loyalty_point_status'])&&$data['loyalty_point_status']!=1)
                $('.loyalty-point-section').hide();
            @endif

            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });
        });
    </script>

    <script>
        function section_visibility(id)
        {
            console.log($('#'+id).data('section'));
            if($('#'+id).is(':checked'))
            {
                console.log('checked');
                $('.'+$('#'+id).data('section')).show();
            }
            else
            {
                console.log('unchecked');
                $('.'+$('#'+id).data('section')).hide();
            }
        }
        $('#add_fund').on('submit', function (e) {

            e.preventDefault();
            var formData = new FormData(this);

            Swal.fire({
                title: '{{\App\CPU\translate('are_you_sure')}}',
                text: '{{\App\CPU\translate('you_want_to_add_fund')}}'+$('#amount').val()+' {{\App\CPU\Helpers::currency_code().' '.\App\CPU\translate('to')}} '+$('#customer option:selected').text()+'{{\App\CPU\translate('to_wallet')}}',
                type: 'info',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: 'primary',
                cancelButtonText: '{{\App\CPU\translate('no')}}',
                confirmButtonText: '{{\App\CPU\translate('send')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                toastr.success('{{__("messages.fund_added_successfully")}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        }
                    });
                }
            })
        })
    </script>
@endpush
