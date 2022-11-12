@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Payment Method'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ route('admin.dashboard') }}">{{ \App\CPU\translate('Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{ \App\CPU\translate('Payment Method') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ \App\CPU\translate('edit_payment_method')}}
                    </div>
                    <div class="card-body"
                         style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.payment-method.update', $paymentMethod) }}" method="POST"
                              enctype="multipart/form-data">
                            @method('put')
                            @csrf
                            @php($language = \App\Model\BusinessSetting::where('type', 'pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs mb-4">
                                @foreach (json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                           href="#"
                                           id="{{ $lang }}-link">{{ \App\CPU\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="row">
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-12">
                                            @foreach (json_decode($language) as $lang)
                                                    <?php
                                                    if (count($paymentMethod['translations'])) {
                                                        $translate = [];
                                                        foreach ($paymentMethod['translations'] as $t) {
                                                            if ($t->locale == $lang && $t->key == 'name') {
                                                                $translate[$lang]['name'] = $t->value;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                <div
                                                    class="form-group {{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
                                                    id="{{ $lang }}-form">
                                                    <label class="input-label">{{ \App\CPU\translate('name') }}
                                                        ({{ strtoupper($lang) }})
                                                    </label>
                                                    <input type="text" name="name[]"
                                                           value="{{ $lang == $default_lang ? $paymentMethod['name'] : $translate[$lang]['name'] ?? '' }}"
                                                           class="form-control"
                                                           placeholder="{{ \App\CPU\translate('New Name') }}"
                                                        {{ $lang == $default_lang ? 'required' : '' }}>
                                                </div>
                                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                                            @endforeach
                                            <div class="col-12 p-0">
                                                <div class="form-group">
                                                    <label
                                                        class="input-label">{{ \App\CPU\translate('Account Name') }}</label>
                                                    <input type="text" name="account_name"
                                                           value="{{$paymentMethod['account_name'] }}"
                                                           class="form-control"
                                                           required>
                                                </div>

                                            </div>
                                            <div class="col-12 p-0">
                                                <div class="form-group">
                                                    <label
                                                        class="input-label">{{ \App\CPU\translate('Account Number') }}</label>
                                                    <input type="text" name="account_number"
                                                           value="{{$paymentMethod['account_number'] }}"
                                                           class="form-control"
                                                           required>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-6">
                                    <label>{{ \App\CPU\translate('image') }}</label><small style="color: red">
                                        ( {{ \App\CPU\translate('ratio') }} 3:1 )</small>
                                    <div class="custom-file" style="text-align: left">
                                        <input id="image" type="hidden" name="image"
                                               value="{{ old('image', $paymentMethod['image']) }}">
                                        <input type="file" id="input-image" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp">
                                        <label class="custom-file-label"
                                               for="input-image">{{ \App\CPU\translate('choose') }}
                                            {{ \App\CPU\translate('file') }}</label>

                                    </div>
                                    <div style="height: 250px;">
                                        <img class="w-100 h-100 border" style="object-fit: contain"
                                             onerror="this.src='{{ asset('public/assets/front-end/img/image-place-holder.png') }}'"
                                             src="{{ old('image', asset('storage/app/public/payment-methods/' .  $paymentMethod['image'])) }}">
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{ \App\CPU\translate('update') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $default_lang }}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        var $ajax = null;
        $(document).on('input', 'input#input-image', function (e) {

            if (e.target.files.length) {
                if ($ajax) {
                    $ajax.abort();
                }
                var formData = new FormData();
                formData.append('_token', `{{ csrf_token() }}`);
                formData.append('image', e.target.files[0]);
                $ajax = $.ajax({
                    url: `{{ route('api.v1.temp_image') }}`,
                    method: 'post',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: (res) => {
                        $(this).parent().parent().find('img').attr('src', res.url);
                        if (res.status) {
                            $(this).parent().find('#image').val(res.url);
                        } else {
                            $(this).parent().find('#image').val('');
                        }
                        if (res.message) {
                            toastr.error(res.message);
                        }
                    }

                });
            }

            // $(this).parent().find('img').attr('src', URL.createObjectURL(e.target.files[0]));
        });
    </script>
@endpush
