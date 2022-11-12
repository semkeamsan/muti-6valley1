@extends('layouts.front-end.app')

@section('title', \App\CPU\translate('OTP_verification'))

@push('css_or_js')
    <style>
        #partitioned {
            padding-left: 2px;
            letter-spacing: 42px;
            border: 0;
            background-image: linear-gradient(to left, black 70%, rgba(255, 255, 255, 0) 0%);
            background-position: bottom;
            background-size: 50px 1px;
            background-repeat: repeat-x;
            background-position-x: 35px;
            width: 220px;
            min-width: 220px;
        }

        #divInner {
            left: 0;
            position: sticky;
        }

        #divOuter {
            width: 190px;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">

            <div class="col-lg-4 col-md-6">
                <div class="alert alert-danger" id="firebase-error" style="display: none"></div>
                <div class="alert alert-success" id="firebase-success" style="display: none"></div>
                <h2 class="h3 mb-4">{{\App\CPU\translate('provide_your_otp_and_proceed')}}?</h2>
                <div class="card py-2 mt-4">
                    <form id="frm_verify" class="card-body needs-validation"
                          action="{{route('customer.auth.otp-verification-submit')}}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label>{{\App\CPU\translate('Enter your OTP')}}</label>
                            <input class="form-control w-100" name="otp" id="code" type="number" maxlength="6"/>
                        </div>
                        <button class="btn btn-primary btn-block"
                                type="submit">{{\App\CPU\translate('proceed')}}</button>
                        <hr>
                        <div class="text-center mt-3">
                            <h3 id="btn-resend" class="small text-truncate font-weight-bold" style="cursor: pointer">
                                {{\App\CPU\translate('Did not receive OTP code')}}?
                                <span class="text-primary">{{\App\CPU\translate('Send again')}}</span>
                            </h3>
                        </div>
                    </form>
                    <div id="recaptcha-container"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @if(\App\CPU\Helpers::get_business_settings('phone_verification'))
        <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
        <script>
            const firebaseConfig = {
                apiKey: "{{ config('firebase.FIREBASE_API_KEY') }}",
                authDomain: "{{ config('firebase.FIREBASE_AUTH_DOMAIN') }}",
                projectId: "{{ config('firebase.FIREBASE_PROJECT_ID') }}",
                storageBucket: "{{ config('firebase.FIREBASE_STORAGE') }}",
                messagingSenderId: "{{ config('firebase.FIREBASE_MESSAGER_SENDER_ID') }}",
                appId: "{{ config('firebase.FIREBASE_MESSAGER_SENDER_ID') }}",
            };

            firebase.initializeApp(firebaseConfig);
        </script>
        <script type="text/javascript">

            window.onload = function () {
                render();
                var coderesult;
                phoneSendAuth();
            };

            function render() {
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': function (response) {
                        // reCAPTCHA solved, allow signInWithPhoneNumber.
                    }
                });
                recaptchaVerifier.render();
            }

            function phoneSendAuth() {
                const phoneNumber = '{{ Session::get("forgot_password_identity")}}'
                console.log('phone', phoneNumber)
                firebase.auth().signInWithPhoneNumber(phoneNumber, window.recaptchaVerifier).then(function (confirmationResult) {
                    window.confirmationResult = confirmationResult;
                    coderesult = confirmationResult;
                    // console.log('result', coderesult);
                }).catch(function (error) {
                    $("#firebase-error").text(error.message);
                    $("#firebase-error").show();
                    console.log('error', error.message)
                });
            }
        </script>
        <script>
            $("#frm_verify").submit(function (e) {
                e.preventDefault();
                var form = $(this).clone()
                const code = document.getElementById('code').value
                coderesult.confirm(code).then(function (result) {
                    var user = result.user;
                    console.log(user);
                    $("#firebase-success").text("OTP Verified");
                    $("#firebase-error").hide();
                    $("#firebase-success").show();
                    form.addClass('d-none')
                    form.appendTo('body')
                    form.submit()
                }).catch(function (error) {
                    e.preventDefault();
                    $("#firebase-error").text('Invalid code!. Please try again.');
                    $("#firebase-error").show();
                    console.log(error.message)
                });
            });
        </script>
    @endif

    <script>
        $('#btn-resend').click(function (e) {
            e.preventDefault()
            location.reload()
        })
    </script>

@endpush
