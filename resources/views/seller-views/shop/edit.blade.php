@extends('layouts.back-end.app-seller')
@section('title', \App\CPU\translate('Shop Edit'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <!-- Content Row -->
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 ">{{\App\CPU\translate('Edit Shop Info')}}</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{route('seller.shop.update',[$shop->id])}}" method="post"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{\App\CPU\translate('Shop Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{$shop->name}}" class="form-control"
                                               id="name"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">{{\App\CPU\translate('Contact')}} <small class="text-danger">(
                                                * )</small></label>
                                        <input type="tel" name="contact[main]" value="{{$shop->contact}}"
                                               class="form-control" id="phone"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">{{\App\CPU\translate('Address')}} <span
                                                class="text-danger">*</span></label>
                                        <textarea type="text" rows="4" name="address" class="form-control"
                                                  id="address"
                                                  required>{{$shop->address}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="name">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('image')}}</label>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileUpload"
                                                   class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="customFileUpload">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <img
                                            style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                            id="viewer"
                                            onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                            src="{{asset('storage/app/public/shop/'.$shop->image)}}"
                                            alt="Product thumbnail"/>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 mt-2">
                                    <div class="form-group">
                                        <div class="flex-start">
                                            <div
                                                for="name">{{\App\CPU\translate('Upload')}} {{\App\CPU\translate('Banner')}} </div>
                                            <div class="mx-1" for="ratio"><small
                                                    style="color: red">{{\App\CPU\translate('Ratio')}} : ( 6:1 )</small>
                                            </div>
                                        </div>
                                        <div class="custom-file text-left">
                                            <input type="file" name="banner" id="BannerUpload" class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="BannerUpload">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <img
                                            style="width: auto; height:auto; border: 1px solid; border-radius: 10px; max-height:200px"
                                            id="viewerBanner"
                                            onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                            src="{{asset('storage/app/public/shop/banner/'.$shop->banner)}}"
                                            alt="Product thumbnail"/>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary float-right"
                                    id="btn_update">{{\App\CPU\translate('Update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
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

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $("#BannerUpload").change(function () {
            readBannerURL(this);
        });
    </script>

    <script>
        $(document).ready(function () {
            $(window).load(function () {
                const _phone_number = '{{$shop['contact']}}'
                console.log(_phone_number);
                const phoneInputField = document.querySelector("#phone");
                const phoneInput = window.intlTelInput(phoneInputField, {
                    separateDialCode: true,
                    hiddenInput: "full",
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                });

                if (_phone_number) {
                    phoneInput.setNumber(_phone_number);
                    const country_data = phoneInput.getSelectedCountryData();
                    console.log({country_data})
                    const country_code_iso_2 = country_data.iso2
                    const diaCode = country_data.dialCode
                    const phone = _phone_number.replace(diaCode, '').replace('+', '')
                    const full_phone_number = `+${diaCode}${phone}`;
                    console.log({full_phone_number})
                    $("input[name='contact[full]']").val(full_phone_number)

                }

                @if(old('phone.full'))
                phoneInput.setNumber('{{ old('phone.full') }}');
                @endif

                // // listen to the address dropdown for changes
                phoneInputField.addEventListener('keyup', function () {
                    //iti.setCountry(this.value);
                    const country_data = phoneInput.getSelectedCountryData();
                    const country_code_iso_2 = country_data.iso2
                    const diaCode = country_data.dialCode
                    const phone = $(this).val()

                    const full_phone_number = "+" + diaCode + phone

                    console.log({full_phone_number})

                    $("input[name='contact[full]']").val(full_phone_number)
                });
            });
        });

    </script>
@endpush
