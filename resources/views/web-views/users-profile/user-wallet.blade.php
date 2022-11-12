@extends('layouts.front-end.app')

@section('title',\App\CPU\translate('My Wallet'))

@push('css_or_js')
    <style>
        .widget-categories .accordion-heading > a:hover {
            color: #FFD5A4 !important;
        }

        .widget-categories .accordion-heading > a {
            color: #FFD5A4;
        }

        body {
            font-family: 'Titillium Web', sans-serif;
        }

        .card {
            border: none
        }

        .totals tr td {
            font-size: 13px
        }

        .product-qty span {
            font-size: 14px;
            color: #6A6A6A;
        }

        .spandHeadO {
            color: #FFFFFF !important;
            font-weight: 600 !important;
            font-size: 14px;

        }

        .tdBorder {
            border- {{Session::get('direction') === "rtl" ? 'left' : 'right'}}: 1px solid #f7f0f0;
            text-align: center;
        }

        .bodytr {
            text-align: center;
            vertical-align: middle !important;
        }

        .sidebar h3:hover + .divider-role {
            border-bottom: 3px solid {{$web_config['primary_color']}}                                   !important;
            transition: .2s ease-in-out;
        }

        tr td {
            padding: 10px 8px !important;
        }

        td button {
            padding: 3px 13px !important;
        }

        @media (max-width: 600px) {
            .sidebar_heading {
                background: {{$web_config['primary_color']}};
            }

            .orderDate {
                display: none;
            }

            .sidebar_heading h1 {
                text-align: center;
                color: aliceblue;
                padding-bottom: 17px;
                font-size: 19px;
            }
        }
    </style>
@endpush

@section('content')

    <div class="container rtl" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-9 mt-2 sidebar_heading">
                <h1 class="h3  mb-0 p-3 float-{{Session::get('direction') === "rtl" ? 'right' : 'left'}} headerTitle">{{\App\CPU\translate('my_wallet')}}</h1>
            </div>
        </div>
    </div>

    <!-- Page Content-->
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl"
         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="row">
            <!-- Sidebar-->
        @include('web-views.partials._profile-aside')
        <!-- Content  -->
            <section class="col-lg-9 col-md-9">
                
                <div class="card box-shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span>
                                    {{\App\CPU\translate('transaction_history')}}
                                </span>
                            </div>
                            <div>
                                <span>
                                    {{\App\CPU\translate('wallet_amount')}} : {{\App\CPU\Helpers::currency_converter($total_wallet_balance)}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="overflow: auto">
                            <table class="table">
                                <thead>
                                <tr style="background-color: #6b6b6b;">
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO ">{{\App\CPU\translate('sl#')}}</span></div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO">{{\App\CPU\translate('transaction_type')}} </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO">{{\App\CPU\translate('credit')}} </span>
                                        </div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO"> {{\App\CPU\translate('debit')}}</span></div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO"> {{\App\CPU\translate('balance')}}</span></div>
                                    </td>
                                    <td class="tdBorder">
                                        <div class="py-2"><span
                                                class="d-block spandHeadO"> {{\App\CPU\translate('date')}}</span></div>
                                    </td>
                                </tr>
                                </thead>
    
                                <tbody>
                                @foreach($wallet_transactio_list as $key=>$item)
                                    <tr>
                                        <td class="bodytr">
                                            {{$wallet_transactio_list->firstItem()+$key}}
                                        </td>
                                        <td class="bodytr"><span class="text-capitalize">{{str_replace('_', ' ',$item['transaction_type'])}}</span></td>
                                        <td class="bodytr"><span class="">{{\App\CPU\Helpers::currency_converter($item['credit'])}}</span></td>
                                        <td class="bodytr"><span class="">{{\App\CPU\Helpers::currency_converter($item['debit'])}}</span></td>
                                        <td class="bodytr"><span class="">{{\App\CPU\Helpers::currency_converter($item['balance'])}}</span></td>
                                        <td class="bodytr"><span class="">{{$item['created_at']}}</span></td>
                                        
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @if($wallet_transactio_list->count()==0)
                                <center class="mt-3 mb-2">{{\App\CPU\translate('no_transaction_found')}}</center>
                            @endif

                            <div class="card-footer">
                                {{$wallet_transactio_list->links()}}
                            </div>
                        </div>
                        
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('script')
    
@endpush
