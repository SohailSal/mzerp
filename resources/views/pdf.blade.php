<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ public_path('css/app.css') }}">

    </head>
    <body class="font-sans antialiased">
    {{-- <div class="border rounded-lg m-5 p-5 inline-block" style="color:white;background-color:gray;">
        @for ($i = 0; $i < 10; $i++)
        This is:  {{ $a }} <br>
        @endfor
    </div> --}}

    <div class="row invoice-wrapper">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <td>
                                <h4>
                                    <span class="">StackCoder</span>
                                </h4>
                            </td>
                            <td class="text-right">
                                <strong>Date: 28 April 2020</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br><br>
            <div class="row invoice-info">
                <div class="col-md-12">
                    <table class="table">
                        <tr>
                            <td>
                                <div class="">
                                    From
                                    <address>
                                        <strong>StackCoder</strong><br>
                                        HSR Layout Sector 6, Bnagalore<br>
                                        Email: stackcoder.in@gmail.com
                                    </address>
                                </div>
                            </td>
                            <td>
                                <div class="">
                                    To
                                    <address>
                                        <strong class="billing_name">Balaji Hakari</strong><br>
                                        <span class="billing_address">#32, Madhura Chetana Colony, Hubli</span><br>
                                        <span class="billing_gst">#TAXNUMBER</span><br>
                                        Phone: +91-7019101234<br>
                                        Email: customer@gmail.com
                                    </address>
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    <b>Invoice #1001</b><br>
                                    Paid for REASON
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Product Name</td>
                                <td>Amount paid for Product Name</td>
                                <td class="text-right">&#8377; 1000</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Sub Total</td>
                                <td class="text-right"><strong>&#8377; 1000</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">TAX (18%)</td>
                                <td class="text-right"><strong>&#8377; 180</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Total Payable</td>
                                <td class="text-right"><strong>&#8377; 1180</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <br><br><br>
            <div>
                <small><small>NOTE: This is system generate invoice no need of signature</small></small>
            </div>
        </div>
    </div>
</body>
</html>
