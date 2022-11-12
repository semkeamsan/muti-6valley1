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
                    <h1 class="page-header-title">{{\App\CPU\translate('add_fund')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card gx-2 gx-lg-3">
            <div class="card-body">
                <form action="{{route('admin.customer.wallet.add-fund')}}" method="post" enctype="multipart/form-data" id="add_fund">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="customer">{{\App\CPU\translate('customer')}}</label>
                                <select id='customer' name="customer_id" data-placeholder="{{\App\CPU\translate('select_customer')}}" class="js-data-example-ajax form-control" required>

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="amount">{{\App\CPU\translate('amount')}}</label>
                        
                                <input type="number" class="form-control" name="amount" id="amount" step=".01" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="input-label" for="referance">{{\App\CPU\translate('reference')}} <small>({{\App\CPU\translate('optional')}})</small></label>
                        
                                <input type="text" class="form-control" name="referance" id="referance">
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
                confirmButtonText: '{{\App\CPU\translate('add')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '{{route('admin.customer.wallet.add-fund')}}',
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
                                toastr.success('{{\App\CPU\translate("fund_added_successfully")}}', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        }
                    });
                }
            })
        })

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('admin.customer.customer-list-search')}}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });
    </script>
@endpush