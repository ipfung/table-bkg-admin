<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Invoice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC&display=swap" rel="stylesheet">
    <style>
        body {
            font-size: 13px;
            background: #fff !important;
        }
        {{--@font-face {--}}
        {{--    font-family: msyh;--}}
        {{--    src: url('{{ public_path('fonts/chinese.msyh.ttf') }}') format('truetype');--}}
        {{--            src: url('{{ asset('fonts/wt011.ttf') }}') format('truetype');--}}
        {{--}--}}
        * {
            font-family: 'Noto Sans TC', sans-serif;
        }

        .container {
            width: 21cm;
            margin: 0 auto;
        }

        .a4 {
            /*height:29.7cm;*/
            position:relative;
            overflow:hidden;
            margin:10px;
            /*padding:25px 28px 460px 28px;*/
            /*-webkit-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);*/
            /*-moz-box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);*/
            /*box-shadow: 0px 0px 5px 0px rgba(0,0,0,0.75);*/
            background-color: #fff;
        }
        #invoice {
            margin-top: 25px;
        }
        .logo {
            margin:0 0 20px 0;
        }
        .header2 {
            font-size: 20px;
            font-weight: 700;
        }
        .inv-caption {
            margin-top:0;
            text-align: center;
        }
        .inv-header-table {
            width:auto;
            border-spacing:0;
        }
        .inv-header-table th,
        .inv-header-table td {
            line-height:18px;
            vertical-align:top;
        }
        .inv-header-table th {
            padding-right:8px;
            font-weight:normal;
            text-align:left;
            white-space:nowrap;
        }
        .invoiceDetailRow:last-child > td {
            border-bottom:1px solid #DDDDDD;
        }
        .classDates {
            width:100%;
        }
        .classDates td {
            padding:0 8px 0 0;
            line-height:18px;
        }
        .charge-details-table {
            margin-top: 20px;
        }
        .charge-details-table tbody > tr.order-item > td {
            border-top: 1px solid #ddd;
            padding: 7px;
            vertical-align: top;
        }
        .course-details-table2 {
            margin-left: 15px;
            width:100%;
        }
        .course-details-table2 .charge-detail {
            width: 35%;
        }
        .charge-caption, .inv-title {
            white-space: nowrap;
            padding: 7px;
        }
        .charge-caption, .inv-value {
            white-space: nowrap;
            padding: 7px;
            font-weight: 500;
        }
        .charge-detail {
            white-space: nowrap;
        }
        .lesson-dates {
            display: flex;
            flex-wrap: wrap;
        }
        .lesson-dates .lesson-date {
            flex: 1;
            padding-left: 7px;
        }
        .amount {
            text-align: right;
        }
        .inv-table-striped > tbody > tr:nth-child(odd) > td,
        .inv-table-striped > tbody > tr:nth-child(odd) > th {
            background: none;
        }

        .table-hover > tbody > tr:hover > td,
        .table-hover > tbody > tr:hover > th {
            background: none;
        }

        .td_colon{
            width:3%;
        }

        @page {
            margin:0cm 0.5cm 0cm 0.5cm;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body onload="window.print()">
<div id="invoice">
    <div class="container">
        <div class="a4">
            <table width="100%" border="0" cellpadding="1">
                <tr>
{{--                    <td class="txt2" width="60%" rowspan="4">--}}
{{--                        @if ($center->logo)--}}
{{--                            <img src="{{ $center->logo }}">--}}
{{--                        @endif--}}
{{--                    </td>--}}
                    <td class="header2" colspan="2">{{ $order->location->company->name }}</td>
                </tr>
                <tr>
                    <td class="txt2" width="10%">Tel :&#160;</td>
                    <td class="txt2">{{ $order->location->company->phone }}</td>
                </tr>
                <tr>
                    <td class="txt2">Email :&#160;</td>
                    <td class="txt2">{{ $order->location->company->email }}</td>
                </tr>
                <tr>
                    <td class="txt2">Website :&#160;</td>
                    <td class="txt2">{{ $order->location->company->website }}</td>
                </tr>
            </table>

            <h2 class="inv-caption">
                @if ($uri == 'receipt')
                    收　據　Receipt
                @else
                    發 票　Invoice
                @endif
            </h2>

            <table width="100%" class="inv-header-table">
                <tr>
                    <td class="inv-title">學生姓名 Student Name</td>
                    <td class="inv-value">：{{ $order->customer->name }}</td>
                </tr>
                <tr>
                    <td class="inv-title">收據號碼 Receipt No.</td>
                    <td class="inv-value">：{{ $order->order_number }}</td>
                </tr>
                <tr>
                    <td class="inv-title">日期 Date</td>
                    <td class="inv-value">：{{ $order->order_date }}</td>
                </tr>
            </table>

            <table width="100%" class="charge-details-table">
                <thead>
                <tr>
                    <td class="charge-caption">描述<br />Description</td>
                    <td class="charge-caption amount">原價<br />Orig. Amt</td>
                    <td class="charge-caption amount">折扣<br />Discount</td>
                    <td class="charge-caption amount">數量<br />Qty</td>
                    <td class="charge-caption amount">金額<br />Amt</td>
                </tr>
                </thead>
                <tbody>
                @foreach ($order->details as $key=>$item)
                    @if ($item->order_type == 'booking')
                        <tr class="order-item">
                            <td class="charge-detail">
                                預約 Appointment：
                                <table class="course-details-table2">
                                    <tr>
                                        <td class="charge-detail" nowrap="nowrap">日期時間 Date & Time</td>
                                        <td class="td_colon">：</td>
                                        <td>{{ DateTime::createFromFormat('Y-m-d H:i:s', $item->description->start_time)->format('Y-m-d') }} {{ DateTime::createFromFormat('Y-m-d H:i:s', $item->description->start_time)->format('h:iA') }} - {{ DateTime::createFromFormat('Y-m-d H:i:s', $item->description->end_time)->format('h:iA') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="charge-detail" nowrap="nowrap">枱號 Table No.</td>
                                        <td class="td_colon">：</td>
                                        <td>{{ $item->description->room->name }}</td>
                                    </tr>
                                    {{--                                <tr>--}}
                                    {{--                                    <td class="charge-detail" nowrap="nowrap" valign="top">上課日期 Class Dates</td>--}}
                                    {{--                                    <td class="td_colon" valign="top">：</td>--}}
                                    {{--                                    <td valign="top">--}}
                                    {{--                                        <div class="lesson-dates">--}}
                                    {{--                                            @foreach ($item->dates as $d)--}}
                                    {{--                                                <div class="lesson-date">{{ $d->lesson_date }}</div>--}}
                                    {{--                                            @endforeach--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </td>--}}
                                    {{--                                </tr>--}}
                                    {{--                                @if ($item->promotion_id > 0)--}}
                                    {{--                                    <tr>--}}
                                    {{--                                        <td class="charge-detail" nowrap="nowrap">促銷活動 Promotions</td>--}}
                                    {{--                                        <td class="td_colon">: </td>--}}
                                    {{--                                        <td>{{ $item->promotion->name }}--}}
                                    {{--                                            @if ($item->promotion->discount_method == 1)--}}
                                    {{--                                                (金額折扣 ${{ $item->promotion->discount_value }})--}}
                                    {{--                                            @elseif ($item->promotion->discount_method == 2)--}}
                                    {{--                                                (百分比折扣 {{ $item->promotion->discount_value }})--}}
                                    {{--                                            @elseif ($item->promotion->discount_method == 3)--}}
                                    {{--                                                (全單總和 ${{ $item->promotion->discount_value }})--}}
                                    {{--                                            @endif--}}
                                    {{--                                        </td>--}}
                                    {{--                                    </tr>--}}
                                    {{--                                @endif--}}
                                </table>
                            </td>
                            <td class="charge-detail amount">
                                ${{ $item->original_price }}
                            </td>
                            <td class="charge-detail amount">
                                @if ($item->discount > 0)
                                    ${{ $item->discount }}
                                @endif
                            </td>
                            <td class="charge-detail amount">
                                1
                            </td>
                            <td class="charge-detail amount">
                                ${{ $item->discounted_price }}
                            </td>
                        </tr>
                    @elseif ($item->order_type == 'token')
                        <tr class="order-item">
                            <td class="charge-detail">
                                <div>課程名稱 Course Title：{{ $item->description->package->name }} / {{ $item->description->package->description }}</div>
                                <div>有效日期 Validity：{{ $item->description->start_date }} 至 to {{ $item->description->end_date }}</div>
                                @if ($item->description->free)
                                <div>免費堂數目 Free Lesson：{{ $item->description->free->quantity }} / {{ $item->description->free->no_of_session }}</div>
                                @endif
                            </td>
                            <td class="charge-detail amount">
                                @if ($item->original_price > 0)
                                    ${{ $item->original_price }}
                                @endif
                            </td>
                            <td class="charge-detail amount">
                                @if ($item->discount > 0)
                                    ${{ $item->discount }}
                                @endif
                            </td>
                            <td class="charge-detail amount">
                                1
                            </td>
                            <td class="charge-detail amount">
                                @if ($item->discounted_price > 0)
                                    ${{ $item->discounted_price }}
                                @endif
                            </td>
                        </tr>
                    @elseif ($item->order_type == 'package')
                        @if (!empty($item->description->package))
                            <tr class="order-item">
                                <td class="charge-detail">
                                    <div>課程名稱 Course Title：{{ $item->description->package->name }} / {{ $item->description->package->description }}</div>
                                    <div>上課日期 Class Dates：</div>
                                </td>
                                <td class="charge-detail amount">
                                    @if ($item->original_price > 0)
                                        ${{ $item->original_price }}
                                    @endif
                                </td>
                                <td class="charge-detail amount">
                                    @if ($item->discount > 0)
                                        ${{ $item->discount }}
                                    @endif
                                </td>
                                <td class="charge-detail amount">
                                    1
                                </td>
                                <td class="charge-detail amount">
                                    @if ($item->discounted_price > 0)
                                        ${{ $item->discounted_price }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                            <tr>
                                <td valign="top" class="package">
                                    <div class="lesson-dates">
                                        <div class="lesson-date">
                                            {{ ++$key }}.
                                            {{ DateTime::createFromFormat('Y-m-d H:i:s', $item->description->start_time)->format('Y-m-d') }} {{ DateTime::createFromFormat('Y-m-d H:i:s', $item->description->start_time)->format('h:iA') }} - {{ DateTime::createFromFormat('Y-m-d H:i:s', $item->description->end_time)->format('h:iA') }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                                        {{--                                @if ($item->promotion_id > 0)--}}
                                        {{--                                    <tr>--}}
                                        {{--                                        <td class="charge-detail" nowrap="nowrap">促銷活動 Promotions</td>--}}
                                        {{--                                        <td class="td_colon">: </td>--}}
                                        {{--                                        <td>{{ $item->promotion->name }}--}}
                                        {{--                                            @if ($item->promotion->discount_method == 1)--}}
                                        {{--                                                (金額折扣 ${{ $item->promotion->discount_value }})--}}
                                        {{--                                            @elseif ($item->promotion->discount_method == 2)--}}
                                        {{--                                                (百分比折扣 {{ $item->promotion->discount_value }})--}}
                                        {{--                                            @elseif ($item->promotion->discount_method == 3)--}}
                                        {{--                                                (全單總和 ${{ $item->promotion->discount_value }})--}}
                                        {{--                                            @endif--}}
                                        {{--                                        </td>--}}
                                        {{--                                    </tr>--}}
                                        {{--                                @endif--}}
                    @endif
                @endforeach
                @if ($order->discount > 0)
                    <tr class="order-item">
                        <td colspan="4" class="charge-caption amount">金額 Amount:</td>
                        <td class="charge-detail amount">${{ $order->order_total }}</td>
                    </tr>
                    <tr class="order-item">
                        <td colspan="4" class="charge-caption amount">折扣 Discount:</td>
                        <td class="charge-detail amount">{{ $order->discount }}</td>
                    </tr>
                @endif
                <tr class="order-item">
                    <td colspan="4" class="charge-caption amount">總額 Total:</td>
                    <td class="charge-detail amount">${{ $order->total_amount }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="charge-caption amount">付款情況 Payment Status:</td>
                    <td class="charge-detail">
                        @if ($order->payment_status == 'pending')
                            <b>未付款</b>
                        @elseif ($order->payment_status == 'paid')
                            <b>已付款</b>
                        @elseif ($order->payment_status == 'partially')
                            <b>只付了一部份</b>
                        @endif
                        <b>{{ strtoupper($order->payment_status) }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="charge-caption amount">付款方法 Payment Method:</td>
                    <td class="charge-detail">
                        @if ($order->payment_status == 'paid')
                            <b>{{ strtoupper($order->payment->gateway) }}</b>
                        @else
                            <b>不適用</b>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="charge-caption amount">付款日期 Payment Date:</td>
                    <td class="charge-detail">
                        @if ($order->payment->status == 'paid' && $order->payment->payment_date_time)
                            <b>{{ $order->payment->payment_date_time }}</b>
                        @else
                            <b>不適用</b>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
