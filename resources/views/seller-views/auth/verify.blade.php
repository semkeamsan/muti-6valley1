<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{\App\CPU\translate('Seller | Verify OTP')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/back-end')}}/css/toastr.css">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 32rem; background-image: url({{asset('public/assets/admin')}}/svg/components/abstract-bg-4.svg);">
        <!-- SVG Bottom Shape -->
        <figure class="position-absolute right-0 bottom-0 left-0">
            <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1921 273">
                <polygon fill="#fff" points="0,273 1921,273 1921,0 "/>
            </svg>
        </figure>
        <!-- End SVG Bottom Shape -->
    </div>

    <!-- Content -->
    <div class="container py-5 py-sm-7">
        @php($e_commerce_logo=\App\Model\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
        <a class="d-flex justify-content-center mb-5" href="javascript:">
            <img class="z-index-2" src="{{asset("storage/app/public/company/".$e_commerce_logo)}}" alt="Logo"
                 onerror="this.src='{{asset('public/assets/back-end/img/400x400/img2.jpg')}}'"
                 style="width: 8rem;">
        </a>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="alert alert-danger" id="firebase-error" style="display: none"></div>
                <div class="alert alert-success" id="firebase-success" style="display: none"></div>
                <div class="card border-0 box-shadow">
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="h4 mb-1">{{\App\CPU\translate('one_step_ahead')}}</h2>
                            <p class="font-size-sm text-muted mb-4">{{\App\CPU\translate('verify_information_to_continue')}}
                                .</p>
                        </div>
                        <form class="needs-validation_" id="frm_verify" action="{{ route('seller.auth.verify') }}"
                              method="post">
                            @csrf
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
    <!-- End Content -->
</main>

{{--     {{route('customer.auth.verify', $user->id)}}       --}}
<!-- JS Implementing Plugins -->
<script src="{{asset('public/assets/back-end')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset('public/assets/back-end')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/back-end')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

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
        // console.log('phone', phoneNumber)
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
