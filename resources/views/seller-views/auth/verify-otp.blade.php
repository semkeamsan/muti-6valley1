<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=devisce-width, initial-scale=1, shrink-to-fit=no">
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <style>
        .iti--allow-dropdown {
            width: 100%;
        }
    </style>
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
                <h2 class="h3 mb-4">{{\App\CPU\translate('provide_your_otp_and_proceed')}}?</h2>
                <div class="card py-2 mt-4">
                    <form id="frm_verify" class="card-body needs-validation"
                          action="{{route('seller.auth.otp-verification-submit')}}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label>{{\App\CPU\translate('Enter your OTP')}}</label>
                            <div id="divOuter">
                                <div id="divInner">
                                    <input class="form-control" name="otp" id="code" type="number" maxlength="6"/>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">{{\App\CPU\translate('proceed')}}</button>
                    </form>
                    <div id="recaptcha-container"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->
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
        const phoneNumber = '{{ Session::get("forgot_password_identity")}}'
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
</body>
</html>
