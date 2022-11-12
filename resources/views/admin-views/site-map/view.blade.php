@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Generate Sitemap'))
@push('css_or_js')
    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-generate"></i> {{\App\CPU\translate('Generate')}} {{\App\CPU\translate('Sitemap')}}</h1>
                </div>
            </div>
        </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-body text-center">
                    <a href="{{ route('admin.business-settings.web-config.mysitemap-download') }}" class="btn btn-primary">
                        {{\App\CPU\translate('Download Generate Sitemap')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
