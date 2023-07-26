<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Update Email Request</title>

    <style>
        body {
            direction: rtl;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 0;
            margin: 0;
        }

        table {
            width: 40%;
            margin: auto;
        }

        .content {
            position: relative;
            top: -200px;
            background-color: #fff;
            width: 94%;
            border-radius: 10px;
            box-shadow: 0 0 10px 2px #8885;
        }

        .content div {
            padding: 1px 10px;
            padding-bottom: 10px;
            text-align: right;
        }

        .content img {
            width: 60%;
        }

        .blue--text {
            color: #16588f;
        }

        button {
            background-color: #e6b260;
            padding: 10px 30px;
            border-radius: 25px;
            border: 1px solid #fff;
            box-shadow: 0 0 5px 1px #e6b26088;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        @media only screen and (max-width: 600px) {
            table {
                width: 100%;
                border-spacing: unset;
            }
        }
    </style>
</head>

<body>
    <table dir="rtl">
        @include('mails.templates.header')
        <tbody>
            <tr>
                <td align="center">
                    <div class="content">
                        <div>
                            <h1>
                                היי {{ $data['name'] }}, <span class="blue--text">רוצה לעדכן את הכתובת מייל?</span>
                            </h1>
                            <p>
                                שמחים לעזור לך בהחלפת הכתובת אמייל.
                                <br>
                                רק לפני שמחליפים צריך לאמת את הכתובת חדשה.
                                <br>
                                לחץ על הכפתור למטה בשביל להחליף את הכתובת.
                            </p>
                        </div>
                        <br>
                        <a href="{{ config('app.client_url') }}/email-confirmation?token={{ $data['token'] }}&email={{ $data['email'] }}">
                            <button>
                                אמת כתובת מייל
                            </button>
                        </a>
                        <br>
                        <br>
                        <br>
                        <img src="{{ URL::to('/') }}/files/general/logo.png" />
                        <br>
                        <br>
                    </div>
                </td>
            </tr>
            @include('mails.templates.footer')
        </tbody>
    </table>
</body>

</html>