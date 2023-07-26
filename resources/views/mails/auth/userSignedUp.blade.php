<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Email Confirmation</title>

    <style>
        body {
            direction: rtl;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 0;
            margin: 0;
        }

        table {
            width: 30%;
            margin: auto;
        }

        .content {
            background-color: #fff;
            width: 100%;
            border-radius: 10px;
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

        .links {
            display: flex;
            justify-content: space-between;
            width: 50%;
            margin: auto;
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
                                היי {{ $data['name'] }}, <span class="blue--text">ברוכים הבאים!</span>
                            </h1>
                            <p>
                                אנו מאוד מעריכים את בחירתך להירשם לאקדמיה של גולדנס.
                                <br>
                                אנו מאמנים שהאקדמיה תעניק לך את הידע והכלים להתקדם בכדורגל ותרחיב את היכולות שלך לשלוט ולהבין את המשחק ברמה הגבוהה ביותר.
                                <br>
                                לפני שנתחיל ותוכל להיכנס למשתמש ללמוד ולתרגל, צריך לאשר את כתובת המייל על ידי לחיצה על הכפתור למטה.
                                <br>
                                <br>
                                אנחנו מקווים שתמצא עניין ותועלת באקדמיה, נעשה כל שביכולתנו להפוך אותך לשחקן אלוף. 
                                <br>
                                אנא לא להתבייש ליצור קשר עמנו בכל שאלה או צורך. אנחנו כאן לשירותך.
                                <br>
                                תודה על שנתת לנו את האמון. אנחנו מצפים ללוות אותך במסע הלמידה המרגש הזה.
                                <br>
                                <br>
                                בברכה,
                                <br>
                                צוות גולדנס.

                            </p>
                        </div>
                        <br>
                        <a href="{{ config('app.client_url') }}/email-confirmation?token={{ $data['token'] }}&email={{ $data['email'] }}{{ $data['redirect'] ? '&redirect=' . $data['redirect'] : ''}}">
                            <button>
                                התחל ללמוד
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