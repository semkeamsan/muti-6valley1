@extends('layouts.front-end.app')

@section('title', \App\CPU\translate('Verify'))

@push('css_or_js')
    <style>
        @media (max-width: 500px) {
            #sign_in {
                margin-top: -23% !important;
            }

        }
    </style>
@endpush

@section('content')
    <div class="container py-4 py-lg-5 my-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger" id="firebase-error" style="display: none"></div>
                <div class="alert alert-success" id="firebase-success" style="display: none"></div>
                <div class="card border-0 box-shadow">
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="h4 mb-1">{{\App\CPU\translate('one_step_ahead')}}</h2>
                            <p class="font-size-sm text-muted mb-4">{{\App\CPU\translate('verify_information_to_continue')}}
                                .</p>
                        </div>
                        <form class="needs-validation_" id="frm_verify" action="{{ route('customer.auth.verify') }}"
                              method="post">
                            @csrf
                            <div class="col-sm-12">
                                @php($email_verify_status = \App\CPU\Helpers::get_business_settings('email_verification'))
                                @php($phone_verify_status = \App\CPU\Helpers::get_business_settings('phone_verification'))
                                <div class="form-group">
                                    @if(\App\CPU\Helpers::get_business_settings('email_verification'))
                                        <label for="reg-phone" class="text-primary">
                                            *
                                            {{\App\CPU\translate('please') }}
                                            {{\App\CPU\translate('provide') }}
                                            {{\App\CPU\translate('verification') }}
                                            {{\App\CPU\translate('token') }}
                                            {{\App\CPU\translate('sent_in_your_email') }}
                                        </label>
                                    @elseif(\App\CPU\Helpers::get_business_settings('phone_verification'))
                                        <label for="reg-phone" class="text-primary">
                                            *
                                            {{\App\CPU\translate('please') }}
                                            {{\App\CPU\translate('provide') }}
                                            {{\App\CPU\translate('OTP') }}
                                            {{\App\CPU\translate('sent_in_your_phone') }}
                                        </label>
                                        <input type="hidden" value="{{$user->phone}}" name="phone" id="phone">
                                    @else
                                        <label for="reg-phone"
                                               class="text-primary">* {{\App\CPU\translate('verification_code') }}
                                            / {{ \App\CPU\translate('OTP')}}</label>
                                    @endif
                                    <input class="form-control" id="code" type="number" name="token" maxlength="6"
                                           minlength="6" required>
                                </div>
                            </div>
                            <input type="hidden" value="{{$user->id}}" name="id">
                            <button type="submit"
                                    class="btn btn-outline-primary">{{\App\CPU\translate('verify')}}</button>
                        </form>
                        <div id="recaptcha-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--     {{route('customer.auth.verify', $user->id)}}       --}}

@push('script')
    {{--    @if(\App\CPU\Helpers::get_business_settings('phone_verification'))--}}
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
            const phoneNumber = "{{$user->phone}}"
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
                // console.log(user);
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
    {{--    @endif--}}
@endpush
