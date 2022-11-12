@extends('layouts.front-end.app')

@section('title',\App\CPU\translate('Choose Payment Method'))

@push('css_or_js')
    <style>
        .stripe-button-el {
            display: none !important;
        }

        .razorpay-payment-button {
            display: none !important;
        }
    </style>

    {{--stripe--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    {{--stripe--}}
@endpush

@section('content')
    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <div class="col-md-12 mb-5 pt-5">
                <div class="feature_header" style="background: #dcdcdc;line-height: 1px">
                    <span>{{ \App\CPU\translate('payment_method')}}</span>
                </div>
            </div>
            <section class="col-lg-8">
                <hr>
                <div class="checkout_details mt-3">
                    @include('web-views.partials._checkout-steps',['step'=>3])
                    <!-- Payment methods accordion-->
                    <h2 class="h6 pb-3 mb-2 mt-5">{{\App\CPU\translate('choose_payment')}}</h2>

                    <div class="row">
                        @php($config=\App\CPU\Helpers::get_business_settings('cash_on_delivery'))
                        @if($config['status'])
                            <div class="col-md-6 mb-4" style="cursor: pointer">
                                <div class="card">
                                    <div class="card-body" style="height: 100px">
                                        <form
                                            action="{{route('checkout-complete',['payment_method'=>'cash_on_delivery'])}}"
                                            method="get" class="needs-validation">
                                            <button class="btn btn-block click-if-alone" type="submit">
                                                <img width="150" style="margin-top: -10px"
                                                     src="{{asset('public/assets/front-end/img/cod.png')}}"/>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @php($coupon_discount = session()->has('coupon_discount') ? session('coupon_discount') : 0)
                        @php($amount = \App\CPU\CartManager::cart_grand_total() - $coupon_discount)
                        @php($digital_payment=\App\CPU\Helpers::get_business_settings('digital_payment'))

                        @if ($digital_payment['status']==1)
                            @php($config=\App\CPU\Helpers::get_business_settings('wallet_status'))
                            @if($config==1)
                                <div class="col-md-6 mb-4" style="cursor: pointer">
                                    <div class="card">
                                        <div class="card-body" style="height: 100px">
                                            {{-- <form action="{{route('checkout-complete-wallet')}}" method="get" class="needs-validation"> --}}
                                            <button class="btn btn-block click-if-alone" type="submit"
                                                    data-toggle="modal" data-target="#wallet_submit_button">
                                                <img width="150" style="margin-top: -10px"
                                                     src="{{asset('public/assets/front-end/img/wallet.png')}}"/>
                                            </button>
                                            {{-- </form> --}}
                                        </div>
                                    </div>
                                </div>
                            @endif

                        @endif

                        @foreach(\App\Model\PaymentMethod::query()->active()->get() as $payment_method)
                            <div class="col-md-6 mb-4" style="cursor: pointer">
                                <div class="card">
                                    <div class="card-body text-center payment-methods"

                                         style="height: 100px">
                                        <button class="btn btn-transparent p-0 btn-digital-payment-methods"
                                                type="submit"
                                                data-id="{{ $payment_method->id }}"
                                                data-name="{{ $payment_method->name }}"
                                                data-image="{{ asset('storage/app/public/payment-methods/' . $payment_method->image) }}"
                                                data-toggle="modal" data-target="#digital_payment_submit">
                                            <div class="d-flex align-items-lg-center">
                                                <img style="width: 60px;height:60px" src="{{ asset('storage/app/public/payment-methods/' . $payment_method->image) }}" />

                                                <div class="text-left pl-2">
                                                    <p class="m-0">
                                                        {{ \App\CPU\translate('Bank Name') }} : {{ $payment_method->name }}
                                                    </p>
                                                    <p class="m-0">
                                                        {{ \App\CPU\translate('Account Name') }} :
                                                        {{ $payment_method->account_name }}
                                                    </p>
                                                    <p class="m-0">
                                                        {{ \App\CPU\translate('Account Number') }} :
                                                        {{ $payment_method->account_number }}
                                                    </p>
                                                </div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                    <!-- Navigation (desktop)-->
                    <div class="row">
                        <div class="col-4"></div>
                        <div class="col-4">
                            <a class="btn btn-secondary btn-block" href="{{route('checkout-details')}}">
                                <span class="d-none d-sm-inline">{{\App\CPU\translate('Back to Shipping')}}</span>
                                <span class="d-inline d-sm-none">{{\App\CPU\translate('Back')}}</span>
                            </a>
                        </div>
                        <div class="col-4"></div>
                    </div>
                </div>
            </section>
            <!-- Sidebar-->
            @include('web-views.partials._order-summary')
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="wallet_submit_button" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{\App\CPU\translate('wallet_payment')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @php($customer_balance = auth('customer')->user()->wallet_balance)
                @php($remain_balance = $customer_balance - $amount)
                <form action="{{route('checkout-complete-wallet')}}" method="get" class="needs-validation">
                    @csrf
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="">{{\App\CPU\translate('your_current_balance')}}</label>
                                <input class="form-control" type="text"
                                       value="{{\App\CPU\Helpers::currency_converter($customer_balance)}}" readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="">{{\App\CPU\translate('order_amount')}}</label>
                                <input class="form-control" type="text"
                                       value="{{\App\CPU\Helpers::currency_converter($amount)}}" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="">{{\App\CPU\translate('remaining_balance')}}</label>
                                <input class="form-control" type="text"
                                       value="{{\App\CPU\Helpers::currency_converter($remain_balance)}}" readonly>
                                @if ($remain_balance<0)
                                    <label
                                        style="color: crimson">{{\App\CPU\translate('you do not have sufficient balance for pay this order!!')}}</label>
                                @endif
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{\App\CPU\translate('close')}}</button>
                        <button type="submit"
                                class="btn btn-primary" {{$remain_balance>0? '':'disabled'}}>{{\App\CPU\translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="digital_payment_submit" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        {{\App\CPU\translate('digital_payment')}}
                        (<span id="bank_name"></span>)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{route('checkout-complete',['payment_method'=>'digital_payment'])}}" method="get"
                      class="needs-validation">
                    @csrf
                    <div class="modal-body text-center">
                        <input type="hidden" name="payment_image_url" id="payment_image_url">
                        <input type="hidden" name="payment_method" value="digital_payment">
                        <input type="hidden" name="payment_method_id" id="payment_method_id">
                        <h5 class="modal-title mb-3">
                            {{\App\CPU\translate('please_upload_payment_image')}}
                        </h5>
                        <input type="file" class="d-none" id="payment_image"
                               placeholder="Choose images">

                        <img class="img-fluid img-thumbnail w-100"
                             src="{{ asset('public/assets/front-end/img/cloud-upload.png') }}" alt=""
                             id="payment_image_preview" style="cursor: pointer">


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{\App\CPU\translate('close')}}</button>
                        <button type="submit"
                                class="btn btn-primary" id="digital-payment-submit"
                                disabled>{{\App\CPU\translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script src="{{ asset('public/compressorjs/dist/compressor.min.js') }}"></script>


    @php($mode = App\CPU\Helpers::get_business_settings('bkash')['environment']??'sandbox')
    @if($mode=='live')
        <script id="myScript"
                src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
    @else
        <script id="myScript"
                src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
    @endif

    <script>
        setTimeout(function () {
            $('.stripe-button-el').hide();
            $('.razorpay-payment-button').hide();
        }, 10)
    </script>

    <script type="text/javascript">
        function BkashPayment() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $('#loading').show();
            // get token
            $.ajax({
                url: "{{ route('bkash-get-token') }}",
                type: 'POST',
                contentType: 'application/json',
                success: function (data) {
                    $('#loading').hide();
                    $('pay-with-bkash-button').trigger('click');
                    if (data.hasOwnProperty('msg')) {
                        showErrorMessage(data) // unknown error
                    }
                },
                error: function (err) {
                    $('#loading').hide();
                    showErrorMessage(err);
                }
            });
        }

        let paymentID = '';
        bKash.init({
            paymentMode: 'checkout',
            paymentRequest: {},
            createRequest: function (request) {
                setTimeout(function () {
                    createPayment(request);
                }, 2000)
            },
            executeRequestOnAuthorization: function (request) {
                $.ajax({
                    url: '{{ route('bkash-execute-payment') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "paymentID": paymentID
                    }),
                    success: function (data) {
                        if (data) {
                            if (data.paymentID != null) {
                                BkashSuccess(data);
                            } else {
                                showErrorMessage(data);
                                bKash.execute().onError();
                            }
                        } else {
                            $.get('{{ route('bkash-query-payment') }}', {
                                payment_info: {
                                    payment_id: paymentID
                                }
                            }, function (data) {
                                if (data.transactionStatus === 'Completed') {
                                    BkashSuccess(data);
                                } else {
                                    createPayment(request);
                                }
                            });
                        }
                    },
                    error: function (err) {
                        bKash.execute().onError();
                    }
                });
            },
            onClose: function () {
                // for error handle after close bKash Popup
            }
        });

        function createPayment(request) {
            // because of createRequest function finds amount from this request
            request['amount'] = "{{round(\App\CPU\Convert::usdTobdt($amount),2)}}"; // max two decimal points allowed
            $.ajax({
                url: '{{ route('bkash-create-payment') }}',
                data: JSON.stringify(request),
                type: 'POST',
                contentType: 'application/json',
                success: function (data) {
                    $('#loading').hide();
                    if (data && data.paymentID != null) {
                        paymentID = data.paymentID;
                        bKash.create().onSuccess(data);
                    } else {
                        bKash.create().onError();
                    }
                },
                error: function (err) {
                    $('#loading').hide();
                    showErrorMessage(err.responseJSON);
                    bKash.create().onError();
                }
            });
        }

        function BkashSuccess(data) {
            $.post('{{ route('bkash-success') }}', {
                payment_info: data
            }, function (res) {
                @if(session()->has('payment_mode') && session('payment_mode') == 'app')
                    location.href = '{{ route('payment-success')}}';
                @else
                    location.href = '{{route('order-placed')}}';
                @endif
            });
        }

        function showErrorMessage(response) {
            let message = 'Unknown Error';
            if (response.hasOwnProperty('errorMessage')) {
                let errorCode = parseInt(response.errorCode);
                let bkashErrorCode = [2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014,
                    2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026, 2027, 2028, 2029, 2030,
                    2031, 2032, 2033, 2034, 2035, 2036, 2037, 2038, 2039, 2040, 2041, 2042, 2043, 2044, 2045, 2046,
                    2047, 2048, 2049, 2050, 2051, 2052, 2053, 2054, 2055, 2056, 2057, 2058, 2059, 2060, 2061, 2062,
                    2063, 2064, 2065, 2066, 2067, 2068, 2069, 503,
                ];
                if (bkashErrorCode.includes(errorCode)) {
                    message = response.errorMessage
                }
            }
            Swal.fire("Payment Failed!", message, "error");
        }

        function click_if_alone() {
            let total = $('.checkout_details .click-if-alone').length;
            if (Number.parseInt(total) < 2) {
                $('.click-if-alone').click()
                $('.checkout_details').html('<h1>{{\App\CPU\translate('Redirecting_to_the_payment')}}......</h1>');
            }
        }

        click_if_alone();


        $(document).on("click", ".btn-digital-payment-methods", function () {
            const bank_name = $(this).data('name');
            const payment_method_id = $(this).data('id');
            console.log({bank_name})
            $("#bank_name").html(bank_name);
            $("#payment_method_id").val(payment_method_id);
        });

        function readURL(input, target) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(target).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#payment_image").change(function (e) {
            readURL(this, '#payment_image_preview');

            var file = e.target.files[0];

            new Compressor(file, {
                quality: 0.75,

                // The compression process is asynchronous,
                // which means you have to access the `result` in the `success` hook function.
                success(result) {

                    console.log('result', result)
                    let formData = new FormData();

                    // The third parameter is required for server
                    formData.append('image', result, result.name);

                    // Send the compressed image file to server with XMLHttpRequest.
                    console.log('here', formData);

                    formData.append('_token', `{{ csrf_token() }}`)
                    const upload_url = "{{ route('api.v1.temp_image') }}"
                    $.ajax({
                        url: upload_url,
                        method: 'POST',
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: (res) => {

                            if (res.status) {
                                $('#payment_image_url').val(res.url);
                                $('#payment_image_preview').attr('src', res.url);
                                $('#digital-payment-submit').prop('disabled', false)
                            } else {
                                $(e.target).parent().find('#image').val('');
                                $('#digital-payment-submit').prop('disabled', true)

                            }
                            if (res.message) {
                                toastr.error(res.message);
                            }
                        }
                    });
                },
                error(err) {
                    console.log(err.message);
                },
            });
        });

        $('#payment_image_preview').click(function () {
            $('#payment_image').click().trigger()
        })

    </script>
@endpush
