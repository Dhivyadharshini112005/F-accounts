<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Salary Advance Voucher {{ $displayVoucherNumber }}
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #e5e7eb;
        }

        .print-actions {
            text-align: center;
            padding: 15px;
        }

        .print-btn,
        .back-btn {
            display: inline-block;
            border: none;
            padding: 10px 20px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .print-btn {
            background: #111827;
            color: #ffffff;
            margin-right: 8px;
        }

        .back-btn {
            background: #e5e7eb;
            color: #111827;
        }

        .voucher {
            width: 210mm;
            height: 148.5mm;
            margin: 20px auto;
            padding: 13mm 12mm 8mm;
            background: #ffffff;
            overflow: hidden;
            border: 1px solid #d1d5db;
        }

        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .logo-section {
            width: 50%;
            text-align: left;
        }

        .ftaxi-logo {
            width: 175px;
            max-height: 56px;
            height: auto;
            object-fit: contain;
        }

        .voucher-title {
            width: 50%;
            text-align: right;
        }

        .voucher-title h1 {
            margin: 0;
            font-size: 23px;
            font-weight: 800;
        }

        .voucher-number {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 700;
        }

        .voucher-date {
            margin-top: 3px;
            font-size: 12px;
        }

        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .details td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            font-size: 12px;
        }

        .details .label {
            width: 105px;
            background: #f8fafc;
            font-weight: 700;
        }

        .amount-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #111827;
            padding: 10px 11px;
            margin-bottom: 10px;
        }

        .amount-label {
            font-size: 14px;
            font-weight: 800;
        }

        .amount-value {
            font-size: 21px;
            font-weight: 800;
        }

        .amount-words {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            margin-bottom: 11px;
            font-size: 12px;
            font-weight: 600;
        }

        .amount-words strong {
            font-weight: 700;
        }

        .description {
            border: 1px solid #cbd5e1;
            padding: 9px 10px;
            min-height: 40px;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .description strong {
            font-weight: 700;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            gap: 25px;
            margin-top: 18px;
        }

        .signature-box {
            flex: 1;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #111827;
            margin-top: 18px;
            padding-top: 5px;
        }

        .signature-label {
            font-size: 11px;
            font-weight: 700;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #111827;
            margin-top: 9px;
            padding-top: 5px;
            font-size: 9px;
            color: #475569;
        }

        .footer-brand {
            font-weight: 800;
            color: #111827;
        }

        @media print {

            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: #ffffff;
            }

            .print-actions {
                display: none !important;
            }

            .voucher {
                width: 210mm;
                height: 148.5mm;
                min-height: 148.5mm;
                max-height: 148.5mm;
                margin: 0;
                padding: 13mm 12mm 8mm;
                border: none;
                overflow: hidden;
            }

        }

    </style>

</head>


<body>


<div class="print-actions">

    <button
        type="button"
        class="print-btn"
        onclick="window.print()"
    >
        Print Voucher
    </button>


    <a
        href="{{ route('expenses.advances.index') }}"
        class="back-btn"
    >
        Back to Advances
    </a>

</div>


<div class="voucher">


    {{-- HEADER --}}

    <div class="voucher-header">

        <div class="logo-section">

            <img
                src="{{ asset('images/logo.png') }}"
                alt="F-TAXI Logo"
                class="ftaxi-logo"
            >

        </div>


        <div class="voucher-title">

            <h1>
                SALARY ADVANCE VOUCHER
            </h1>


            <div class="voucher-number">

                Advance Voucher No:
                {{ $displayVoucherNumber }}

            </div>


            <div class="voucher-date">

                Date:

                {{ \Carbon\Carbon::parse(
                    $advance->advance_date
                )->format('d-m-Y') }}

            </div>

        </div>

    </div>


    {{-- DETAILS --}}

    <table class="details">

        <tr>

            <td class="label">
                Employee Name
            </td>

            <td>
                {{ $advance->employee_name }}
            </td>

            <td class="label">
                Category
            </td>

            <td>
                Salary Advance
            </td>

        </tr>


        <tr>

            <td class="label">
                Payment Mode
            </td>

            <td>

                @if(
                    $advance->payment_mode === 'account'
                )

                    A/C

                @else

                    Cash

                @endif

            </td>


            <td class="label">
                UPI ID
            </td>

            <td>

                @if(
                    $advance->payment_mode === 'account'
                )

                    {{ $advance->upi_id ?? '-' }}

                @else

                    -

                @endif

            </td>

        </tr>

    </table>


    {{-- AMOUNT --}}

    <div class="amount-box">

        <div class="amount-label">
            Salary Advance Amount
        </div>


        <div class="amount-value">

            ₹{{ number_format(
                (float) $advance->amount,
                2
            ) }}

        </div>

    </div>


    {{-- AMOUNT WORDS --}}

    <div class="amount-words">

        <strong>
            Amount in Words:
        </strong>

        Rupees
        {{ $amountInWords }}
        Only

    </div>


    {{-- DESCRIPTION --}}

    <div class="description">

        <strong>
            Description:
        </strong>

        {{ $advance->description ?? '-' }}

    </div>


    {{-- SIGNATURES --}}

    <div class="signature-section">

        <div class="signature-box">

            <div class="signature-line">

                <div class="signature-label">
                    Received By
                </div>

            </div>

        </div>


        <div class="signature-box">

            <div class="signature-line">

                <div class="signature-label">
                    Prepared By
                </div>

            </div>

        </div>


        <div class="signature-box">

            <div class="signature-line">

                <div class="signature-label">
                    Authorized By
                </div>

            </div>

        </div>

    </div>


    {{-- FOOTER --}}

    <div class="footer">

        <div class="footer-brand">
            F-TAXI
        </div>

        <div>
            Salary Advance Voucher
        </div>

    </div>


</div>


</body>

</html>