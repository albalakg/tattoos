<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Activity Reminder</title>

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
                                היי {{ $data['name'] }}, <span class="blue--text">ראינו שלא היית פעיל תקופה.</span>
                            </h1>
                            <p>
                                נשמח לדעת אם הכל תקין, במידה ולא, מוזמן תמיד להשאיר הודעה באתר.
                                <br>
                                אל תוותר והקפד להתאמן, כי מי שמתרגל יותר, משתפר יותר.
                                <br>
                                מוזמן להמשיך מהשיעור האחרון שבו עצרת.
                                <br>
                                לכניסה לשיעור, לחץ על הכפתור למטה.
                                <br>
                                <br>
                                בברכה,
                                <br>
                                צוות גולדנס.
                            </p>
                        </div>
                        <br>
                        <a href="{{ config('app.client_url') }}/courses/{{ $data['course_id'] }}/lessons/{{ $data['lesson_id'] }}">
                            <button>
                                היכנס לשיעור
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