@extends('layouts.front-end.app')
@section('title', \App\CPU\translate('Forgot Password'))
@push('css_or_js')
    <style>
        .text-primary {
            color: {{$web_config['primary_color']}}                         !important;
        }

        .iti--allow-dropdown {
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endpush

@section('content')
    @php($verification_by=\App\CPU\Helpers::get_business_settings('forgot_password_verification'))
    <!-- Page Content-->
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h2 class="h3 mb-4">{{\App\CPU\translate('Forgot your password')}}?</h2>
                <p class="font-size-md">
                    {{\App\CPU\translate('Change your password in three easy steps')}}.
                    {{\App\CPU\translate('This helps to keep your new password secure')}}
                    .</p>

                <div class="card py-2 mt-4">
                    <form class="card-body needs-validation" action="{{route('customer.auth.forgot-password')}}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label for="recover-email">{{\App\CPU\translate('Enter your phone number')}}</label>
                            <div class="mb-4">
                                <input class="form-control" type="tel" name="phone[main]" id="recover-phone"
                                >
                            </div>
                            <div
                                class="invalid-feedback">{{\App\CPU\translate('Please provide valid phone number')}}
                            </div>
                        </div>
                        <button class="btn btn-primary btn-block"
                                type="submit">{{\App\CPU\translate('proceed')}}</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
    <script>
        const phoneInputField = document.querySelector("#recover-phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            separateDialCode: true,
            preferredCountries: ['kh', 'th'],
            hiddenInput: "full",
            initialCountry: "auto",
            geoIpLookup: function (success) {
                $.get("https://ipinfo.io", function () {
                }, "jsonp").always(function (resp) {
                    const countryCode = (resp && resp.country) ? resp.country : "kh";
                    success(countryCode);
                }).fail(() => {
                    success("kh");
                });
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
        if (`{{ old('phone.full') }}`) {
            phoneInput.setNumber(`{{ old('phone.full') }}`);
            phoneInput.setNumber(`{{ old('phone.main') }}`);
        }
    </script>

@endpush
