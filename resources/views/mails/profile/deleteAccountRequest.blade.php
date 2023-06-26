<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Delete Account Request</title>

    <style>
        body {
            direction: rtl;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .cancel--button {
            background-color: #333;
            box-shadow: 0 0 5px 1px #3338;
        }

        a {
            text-decoration: none;
        }

        .actions {
            display: flex;
            justify-content: space-between;
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
                                היי {{ $data['name'] }}, <span class="blue--text">בטוח שרוצה למחוק את המשתמש?</span>
                            </h1>
                            <p>
                            במידה ואתה בטוח שרוצה למחוק את המשתמש נשמח לעזור לך בזה.
                            <br>
                            רק לפני כן, נרצה להזכיר לך שעם המחיקה, כל המידע על המשתמש כולל כל ההתקדמות, יימחקו תמידית.
                            <br>
                            לא אהיה ניתן לשחזר את המידע.
                            <br>
                            למחיקת המשתמש, תלחץ על הפתור למטה.
                            </p>
                        </div>
                        <br>
                        <img src="{{ URL::to('/') }}/files/general/dark-logo.png" />
                        <br>
                        <br>
                        <br>
                        <br>

                        <div class="actions">
                            <a href="{{ config('app.client_url') }}/delete-account&token={{ $data['token'] }}&status=1">
                                <button>
                                    מחק משתמש
                                </button>
                            </a>

                            <a href="{{ config('app.client_url') }}/delete-account&token={{ $data['token'] }}&status=0">
                                <button class="cancel--button">
                                    בטל מחיקה
                                </button>
                            </a>
                        </div>
                        
                        <br>
                        <br>
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