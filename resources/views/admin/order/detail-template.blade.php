    <table style=" font-family: Arial, sans-serif;border-collapse: collapse;margin: 0 auto;border: 1px solid #ececec;background-color: #fff;box-shadow: 0px 0px 30px 0px rgba(82, 63, 105, 0.05);"
        width="500" cellspacing="5" cellpadding="5">
        <tbody>
            <tr>
                <td width="5"></td>
                <td>
                    <h1 style="white-space: nowrap;">Order Details</h1>
                </td>
                <td>&nbsp;</td>
                <td style="text-align: right;">
                    <p style="width: 340px;text-align: right;font-size: 15px;float: right;color: #6f6e6e;">Cecilia Chapman, 711-2880
                        Nulla St, Mankato Mississippi 96522</p>
                </td>
                <td width="5"></td>
            </tr>
            <tr>
                <td colspan="5" height="35"></td>
            </tr>

            <tr>
                <td colspan="5">
                    <table style=" font-family: Arial, sans-serif;border-collapse: collapse;margin: 0 auto;"
                        width="100%" cellspacing="5" cellpadding="5">
                        <tbody>
                            <tr style="border-top: 1px solid #ddd;">
                                <td width="5"></td>
                                <td style="">
                                    <p style="font-size: 14px;margin-bottom: 0;text-transform: uppercase;letter-spacing: 0.5px;"><strong>Order Date</strong></p>
                                    <p style="font-size: 15px; color: #6f6e6e; margin-top:6px;">{{ date('M d, Y', strtotime($data->created_at)) }}</p>
                                </td>
                                <td style="">
                                    <p style="font-size: 14px;margin-bottom: 0;text-transform: uppercase;letter-spacing: 0.5px;"><strong>ORDER NO.</strong></p>
                                    <p style="font-size: 15px; color: #6f6e6e; margin-top:6px;">{{ $data->uuid }}</p>
                                </td>
                                <td style="">
                                    <p style="font-size: 14px;margin-bottom: 0;text-transform: uppercase;letter-spacing: 0.5px;"><strong>DELIVERED TO.</strong>
                                    </p>
                                    <p style="font-size: 15px; color: #6f6e6e; margin-top:6px;">{{ $data->address }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="5" height="35"></td>
            </tr>
            <tr>
                <td colspan="5">
                   @php $product = $data->product; @endphp
                    <table style="font-family: Arial, sans-serif;border-collapse: collapse;margin: 0 auto;"
                        width="100%" cellspacing="5" cellpadding="5">
                        <tbody>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <th></th>
                                <th align="left" style="font-size: 13px;color: #9b9b9b; text-transform: uppercase;">Ordered Items</th>
                                <th align="right" style="font-size: 13px;color: #9b9b9b;text-transform: uppercase;">Qty</th>
                                <th align="right" style="font-size: 13px;color: #9b9b9b;text-transform: uppercase;">Unit Price</th>
                                <th align="right" style="font-size: 13px;color: #9b9b9b;text-transform: uppercase;">Amount</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td width="5"></td>
                                <td>
                                    <p style="font-size: 15px; color: #333; text-align: left;margin-top:6px; font-weight: bold;"><img
                                            style="vertical-align: middle;
                                        display: inline-block;border-radius: 6px;" width="50"
                                            src="{{$product->display_path}}">
                                            {{ $product->title }}</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; color: #333;text-align: right;margin-top:6px;font-weight: bold;">{{ $data->quantity }}</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; color: #333;text-align: right;margin-top:6px;font-weight: bold;">${{ $data->price }}</p>
                                </td>
                                <td>
                                    <p style="font-size: 15px; color: #333;text-align: right;margin-top:6px;font-weight: bold;color:#3699FF">${{ $data->quantity*$data->price }}</p>
                                </td>
                                <td width="5"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="5" height="35"></td>
            </tr>
            <tr style="background-color: #F3F6F9;">
                <td colspan="5">
                    <table style="font-family: Arial, sans-serif;border-collapse: collapse;margin: 20px auto 0;"
                        width="100%" cellspacing="5" cellpadding="5">
                        <tbody>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <th></th>
                                <th align="left" style="font-size: 13px;color: #9b9b9b;">PAYMENT TYPE</th>
                                <th align="left" style="font-size: 13px;color: #9b9b9b;">PAYMENT STATUS</th>
                                <th align="left" style="font-size: 13px;color: #9b9b9b;">PAYMENT DATE</th>
                                <th align="right" style="font-size: 13px;color: #9b9b9b;">TOTAL PAID</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td width="5"></td>
                                <td>
                                    <p style="font-size: 14px; color: #333; text-align: left;margin-top:6px; font-weight: bold;">{{ $data->payment_method_name==1 ? 'Credit Card' : 'Paypal' }}</p>
                                </td>
                                <td>
                                    <p style="font-size: 14px; color: #333;text-align: left;margin-top:6px;font-weight: bold;">{{ $data->payment_status_name }}</p>
                                </td>
                                <td>
                                    <p style="font-size: 14px; color: #333;text-align: left;margin-top:6px;font-weight: bold;">{{ date('M d, Y', strtotime($data->created_at)) }}</p>
                                </td>
                                <td>
                                    <p style="font-size: 22px; color: #333;text-align: right;margin-top:6px;font-weight: bold;color:#3699FF">${{ $data->amount }}</p>
                                </td>
                                <td width="5"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="5" height="35"></td>
            </tr>
        </tbody>
    </table>