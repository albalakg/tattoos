<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - New Course</title>

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
                                היי {{ $data['name'] }}, <span class="blue--text">ברכות על הצטרפותך לאקדמיה של גולדנס!</span>
                            </h1>
                            <p>
                                <!-- At the moment we do not mention the course name, need to be done when there are 2 courses -->
                                אנו שמחים ומתרגשים על החלטתך לקחת את הקריירה שלך לשלב הבא.
                                <br>
                                נתמסר לתהליך כדי להשיג את מטרותיך בעולם הכדורגל.
                                <br>
                                יש לך גישה עכשיו לכל תכני האקדמיה המקיפה שלנו עד התאריך "{{ $data['end_at'] }}".
                                <br>
                                אנא הקפד לעקוב אחרי תוכנית האימון שהגדרנו עבורך, באפשרותך לערוך בהתאם ליומך והרמת ההתקדמות שלך.
                                <br>
                                בשביל להיכנס לאקדמיה לחץ על הכפתור למטה.
                                <br>
                                <br>
                                אנא לא להתבייש ליצור קשר עם הצוות שלנו, אם יש לך שאלות או צרכים מיוחדים. אנחנו כאן כדי לתמוך בך בכל צעד בדרך.
                                <br>
                                תודה שבחרת להיות גולדנס, אנו מצפים לראות את ההתקדמות שלך.
                                <br>
                                בהצלחה!
                                <br>
                                <br>
                                <strong>שימו לב:</strong> פתחנו עבורכם קהילה של אלופים, שבה תוכלו לשתף את קצב ההתקדמות שלכם, להעלות ביצועים, לספר על חווית האימונים, לשאול כל שאלה ואפילו לקבוע לתרגל עם שחקנים אחרים.
                                <br><br>
                                להצטרפות לקהילה בפייסבוק, <a target="_blank" href="https://www.facebook.com/profile.php?id=100094130323045">לחץ כאן</a>
                                <br>
                                <br>
                                בברכה,
                                <br>
                                צוות גולדנס.
                            </p>
                        </div>
                        <br>
                        <img src="{{ URL::to('/') }}/files/general/logo.png" />
                        <br>
                        <br>
                        <br>
                        <br>

                        <a href="{{ config('app.client_url') }}/courses/{{ $data['course_id'] }}">
                            <button>
                                היכנס לאקדמיה
                            </button>
                        </a>

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