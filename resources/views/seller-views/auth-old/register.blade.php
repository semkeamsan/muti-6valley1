@extends('layouts.front-end.app')

@section('title',\App\CPU\translate('Seller Apply'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endpush


@section('content')

    <div class="container main-card rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">

        <div class="card o-hidden border-0 shadow-lg my-4">
            <div class="card-body ">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center mb-2 ">
                                <h3 class=""> {{\App\CPU\translate('Shop')}} {{\App\CPU\translate('Application')}}</h3>
                                <hr>
                            </div>
                            <form class="user" action="{{route('shop.apply')}}" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <h5 class="black">{{\App\CPU\translate('Seller')}} {{\App\CPU\translate('Info')}} </h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                               name="f_name" value="{{old('f_name')}}"
                                               placeholder="{{\App\CPU\translate('first_name')}}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control form-control-user" id="exampleLastName"
                                               name="l_name" value="{{old('l_name')}}"
                                               placeholder="{{\App\CPU\translate('last_name')}}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <input type="email" class="form-control form-control-user"
                                               id="exampleInputEmail" name="email" value="{{old('email')}}"
                                               placeholder="{{\App\CPU\translate('email_address')}}" required>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <input type="tel" class="form-control form-control-user"
                                               id="phone" name="phone[main]" value="{{old('phone')}}"
                                               placeholder="{{\App\CPU\translate('phone_number')}}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                        <input type="password" class="form-control form-control-user" minlength="6"
                                               id="exampleInputPassword" name="password"
                                               placeholder="{{\App\CPU\translate('password')}}" required>
                                    </div>
                                    <div class="col-sm-6 mt-4">
                                        <input type="password" class="form-control form-control-user" minlength="6"
                                               id="exampleRepeatPassword"
                                               placeholder="{{\App\CPU\translate('repeat_password')}}" required>
                                        <div
                                            class="pass invalid-feedback">{{\App\CPU\translate('Repeat_password_not_match')}}
                                            .
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-6  mb-3 mb-sm-0 mt-4">
                                        <div class="pb-1 text-center">
                                            <img
                                                style="border: 1px solid; border-radius: 10px; max-height:200px;"
                                                id="viewer"
                                                src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}"
                                                class="text-center mx-auto img-thumbnail w-auto"

                                                alt="banner image"/>
                                        </div>

                                        <div class="form-group mt-3">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="image" id="customFileUpload"
                                                       class="custom-file-input"
                                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                       for="customFileUpload">{{\App\CPU\translate('Upload_profile_image')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6  mb-3 mb-sm-0 mt-4">
                                        <div class="pb-1 text-center">
                                            <img
                                                style="border: 1px solid; border-radius: 10px; max-height:200px;"
                                                id="viewerIDCard"
                                                class="text-center mx-auto img-thumbnail w-auto"
                                                src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}"
                                                alt="banner image"/>
                                        </div>

                                        <div class="form-group mt-3">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="id_card_image" id="IDCardUpload"
                                                       class="custom-file-input"
                                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                       for="customFileUpload">{{\App\CPU\translate('Upload_id_card_image')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="black">{{\App\CPU\translate('Shop')}} {{\App\CPU\translate('Info')}}</h5>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0 ">
                                        <input type="text" class="form-control form-control-user" id="shop_name"
                                               name="shop_name" placeholder="{{\App\CPU\translate('shop_name')}}"
                                               value="{{old('shop_name')}}" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <textarea name="shop_address" class="form-control" id="shop_address" rows="1"
                                                  placeholder="{{\App\CPU\translate('shop_address')}}">{{old('shop_address')}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6  mb-3 mb-sm-0 mt-4 ">
                                        <div class="pb-1 text-center">
                                            <img
                                                style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                                id="viewerLogo"
                                                src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}"
                                                class="text-center mx-auto img-thumbnail w-auto"

                                                alt="banner image"/>
                                        </div>

                                        <div class="form-group mt-3">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="logo" id="LogoUpload" class="custom-file-input"
                                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                       for="LogoUpload">{{\App\CPU\translate('Upload_shop_logo')}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6  mb-3 mb-sm-0 mt-4 ">
                                        <div class="pb-1 text-center">

                                            <img
                                                style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                                id="viewerBanner"
                                                src="{{asset('public\assets\back-end\img\400x400\img2.jpg')}}"
                                                class="text-center mx-auto img-thumbnail w-auto"

                                                alt="banner image"/>

                                        </div>

                                        <div class="form-group mt-3">
                                            <div class="custom-file" style="text-align: left">
                                                <input type="file" name="banner" id="BannerUpload"
                                                       class="custom-file-input"
                                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                                       style="overflow: hidden; padding: 2%">
                                                <label class="custom-file-label"
                                                       for="BannerUpload">{{\App\CPU\translate('Upload_Shop_Banner')}}</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group d-flex flex-wrap justify-content-between">

                                    <div class="form-group mb-1">
                                        <strong>
                                            <input type="checkbox" class="mr-1"
                                                   name="remember" id="agree_term">
                                        </strong>
                                        <label for="remember" style="cursor: pointer">
                                            {{\App\CPU\translate('i_agree_to_Your_terms')}}
                                            <a class="font-size-sm" target="_blank" href="{{route('terms')}}">
                                                {{\App\CPU\translate('terms_and_condition')}}
                                            </a>
                                        </label>
                                    </div>

                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block" id="apply"
                                        disabled>{{\App\CPU\translate('Apply')}} {{\App\CPU\translate('Shop')}} </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small"
                                   href="{{route('seller.auth.login')}}">{{\App\CPU\translate('already_have_an_account?_login.')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
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
    <script>
        $('#agree_term').change(function () {
            // console.log('jell');
            if ($(this).is(':checked')) {
                $('#apply').removeAttr('disabled');
            } else {
                $('#apply').attr('disabled', 'disabled');
            }

        });

        $('#exampleInputPassword ,#exampleRepeatPassword').on('keyup', function () {
            var pass = $("#exampleInputPassword").val();
            var passRepeat = $("#exampleRepeatPassword").val();
            if (pass == passRepeat) {
                $('.pass').hide();
            } else {
                $('.pass').show();
            }
        });
        $('#apply').on('click', function () {

            var image = $("#image-set").val();
            if (image == "") {
                $('.image').show();
                return false;
            }
            var pass = $("#exampleInputPassword").val();
            var passRepeat = $("#exampleRepeatPassword").val();
            if (pass !== passRepeat) {
                $('.pass').show();
                return false;
            }


        });

        function Validate(file) {
            var x;
            var le = file.length;
            var poin = file.lastIndexOf(".");
            var accu1 = file.substring(poin, le);
            var accu = accu1.toLowerCase();
            if ((accu != '.png') && (accu != '.jpg') && (accu != '.jpeg')) {
                x = 1;
                return x;
            } else {
                x = 0;
                return x;
            }
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        function readlogoURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewerLogo').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewerBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readImageURL(input, target) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(target).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#LogoUpload").change(function () {
            readlogoURL(this);
        });

        $("#BannerUpload").change(function () {
            readBannerURL(this);
        });

        $("#IDCardUpload").change(function () {
            readImageURL(this, '#viewerIDCard');
        });
    </script>



    <script>

        const phoneInputField = document.querySelector("#phone");
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
