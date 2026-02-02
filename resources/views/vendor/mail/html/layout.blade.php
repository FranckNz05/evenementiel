<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .wrapper {
            background-color: #f2f2f2;
            padding: 20px 0;
        }

        .content {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        .header, .footer {
            background-color: #001F3F;
            color: #FFD700;
            text-align: center;
            padding: 20px;
            font-weight: bold;
            font-size: 18px;
        }

        .inner-body {
            width: 570px;
            margin: 0 auto;
            background-color: #ffffff;
        }

        .content-cell {
            padding: 35px;
        }

        .button a {
            background-color: #FFD700;
            border: 1px solid #FFD700;
            color: #001F3F !important;
            display: inline-block;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button a {
                width: 100% !important;
                text-align: center !important;
            }
        }
    </style>
</head>
<body>
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">

                <!-- Header -->
                {{ $header ?? '' }}

                <!-- Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0">
                        <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                            role="presentation">
                            <!-- Body content -->
                            <tr>
                                <td class="content-cell">
                                    {{ Illuminate\Mail\Markdown::parse($slot) }}

                                    {{ $subcopy ?? '' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                {{ $footer ?? '' }}

            </table>
        </td>
    </tr>
</table>
</body>
</html>
