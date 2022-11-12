@extends('layouts.front-end.app')

@section('title', \App\CPU\translate('Register'))

@push('css_or_js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <style>
        @media (max-width: 500px) {
            #sign_in {
                margin-top: -23% !important;
            }

        }

        .iti--allow-dropdown {
            width: 100%;
        }
    </style>

@endpush

@section('content')
    <div class="container py-4 py-lg-5 my-4"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger" id="firebase-error" style="display: none"></div>
                <div class="alert alert-success" id="firebase-success" style="display: none"></div>
                <div class="card border-0 box-shadow mt-2">
                    <div class="card-body">
                        <h2 class="h4 mb-1">{{\App\CPU\translate('no_account')}}</h2>
                        <p class="font-size-sm text-muted mb-4">{{\App\CPU\translate('register_control_your_order')}}
                            .</p>
                        <form  class="needs-validation_" action="{{ route('customer.auth.sign-up') }}"
                              method="post" id="sign-up-form">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-fn">{{\App\CPU\translate('First Name')}}</label>
                                        <input class="form-control" value="{{old('f_name')}}" type="text" name="f_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div
                                            class="invalid-feedback">{{\App\CPU\translate('Please enter your first name')}}
                                            !
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-ln">{{\App\CPU\translate('Last Name')}}</label>
                                        <input class="form-control" type="text" value="{{old('l_name')}}" name="l_name"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                        <div
                                            class="invalid-feedback">{{\App\CPU\translate('Please enter your last name')}}
                                            !
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-email">{{\App\CPU\translate('email_address')}}</label>
                                        <input class="form-control" type="email" value="{{old('email')}}" name="email"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required id="email">
                                        <div
                                            class="email-invalid invalid-feedback">
                                            {{\App\CPU\translate('Email already taken')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg-phone">{{\App\CPU\translate('phone_number')}}</label>
                                        <input class="form-control" type="number" value="{{old('phone')}}" id="phone"
                                               name="phone[main]"
                                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                               required>
                                        <div
                                            class="phone-invalid invalid-feedback">{{\App\CPU\translate('Phone already taken')}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="password" type="password" id="si-password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{\App\CPU\translate('minimum_8_characters_long')}}"
                                                   required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{\App\CPU\translate('Show password')}} </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="si-password">{{\App\CPU\translate('confirm_password')}}</label>
                                        <div class="password-toggle">
                                            <input class="form-control" name="con_password" type="password"
                                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                   placeholder="{{\App\CPU\translate('minimum_8_characters_long')}}"
                                                   id="si-password"
                                                   required>
                                            <label class="password-toggle-btn">
                                                <input class="custom-control-input" type="checkbox"
                                                       style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"><i
                                                    class="czi-eye password-toggle-indicator"></i><span
                                                    class="sr-only">{{\App\CPU\translate('Show password')}} </span>
                                            </label>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="reg-password-confirm">{{\App\CPU\translate('confirm_password')}}</label>
                                        <input class="form-control" type="password" name="con_password">
                                        <div class="invalid-feedback">Passwords do not match!</div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="form-group d-flex flex-wrap justify-content-between">

                                <div class="form-group mb-1">
                                    <strong>
                                        <input type="checkbox" class="mr-1"
                                               name="remember" id="agree">
                                    </strong>
                                    <label for="agree">{{\App\CPU\translate('i_agree_to_Your_terms')}}<a
                                            class="font-size-sm" target="_blank" href="{{route('terms')}}">
                                            <span
                                                class="text-primary">{{\App\CPU\translate('terms_and_condition')}}</span>
                                        </a></label>
                                </div>

                            </div>
                            <div class="flex-between row" style="direction: {{ Session::get('direction') }}">
                                <div class="mx-1">
                                    <div class="text-right">
                                        <button class="btn btn-primary" id="sign-up" type="submit" disabled>
                                            <i class="czi-user {{Session::get('direction') === "rtl" ? 'ml-2 mr-n1' : 'mr-2 ml-n1'}}"></i>
                                            {{\App\CPU\translate('sing_up')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="mx-1">
                                    <a class="btn btn-outline-primary" href="{{route('customer.auth.login')}}">
                                        <i class="fa fa-sign-in"></i> {{\App\CPU\translate('sing_in')}}
                                    </a>
                                </div>
                                <div class="col-12 mt-3">
                                    <div class="row">
                                        @foreach (\App\CPU\Helpers::get_business_settings('social_login') as $socialLoginService)
                                            @if (isset($socialLoginService) && $socialLoginService['status']==true)
                                                <div
                                                    class="col-sm-6 text-center mt-1  @if( $socialLoginService['login_medium'] == 'apple') d-none @endif"
                                                >
                                                    <a class="btn btn-outline-primary"
                                                       href="{{route('customer.auth.service-login', $socialLoginService['login_medium'])}}"
                                                       style="width: 100%">
                                                        <i class="czi-{{ $socialLoginService['login_medium'] }} {{Session::get('direction') === "rtl" ? 'ml-2 mr-n1' : 'mr-2 ml-n1'}}"></i>
                                                        {{\App\CPU\translate('sing_up_with_'.$socialLoginService['login_medium'])}}
                                                    </a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function check_user(key, value) {
            $.get({
                url: '{{ route('api.v1.check_user') }}',
                dataType: 'json',
                data: {
                    key: key,
                    value: value
                },
                beforeSend: function () {

                },
                success: function (data) {
                    console.log(data)
                    if (data.exist) {
                        $('#sign-up').attr('disabled', true)
                        if (key == 'email') {
                            $('.email-invalid').show()
                        }
                        if (key == 'phone') {
                            $('.phone-invalid').show()
                        }


                    } else {
                        $('#sign-up').removeAttr('disabled')
                        if (key == 'email') {
                            $('.email-invalid').hide()
                        }

                        if (key == 'phone') {
                            $('.phone-invalid').hide()
                        }

                    }
                },
                complete: function () {
                },
            });
        }

        const phoneInputField = document.querySelector("#phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            separateDialCode: true,
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


        // // listen to the address dropdown for changes
        phoneInputField.addEventListener('keyup', function () {
            //iti.setCountry(this.value);
            const country_data = phoneInput.getSelectedCountryData();
            const country_code_iso_2 = country_data.iso2
            const diaCode = country_data.dialCode
            const phone = +$(this).val()

            const full_phone_number = "+" + diaCode + phone

            console.log(full_phone_number)

            check_user('phone', full_phone_number)

        });

        $('#email').keyup(function () {
            //alert('kkk')
            check_user('email', $(this).val())
        })
    </script>
    <script>
        $('#agree').change(function () {
            // console.log('jell');
            if ($(this).is(':checked')) {
                $('#sign-up').removeAttr('disabled');
            } else {
                $('#sign-up').attr('disabled', 'disabled');
            }
        });

    </script>
@endpush
