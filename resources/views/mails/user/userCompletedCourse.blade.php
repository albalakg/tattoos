<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Course Completed</title>

    <style>
        .header {
            height: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
        }

        .header img {
            width: 30%;
        }

        @media only screen and (max-width: 600px) {
            table {
                width: 100%;
                border-spacing: unset;
            }

            .header {
                border-radius: unset;
            }
        }

        body {
            direction: rtl;
            font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 0;
            margin: 0;
        }

        table {
            width: 40%;
            height: 70vh;
            color: #fff;
            margin: auto;
            background-color: #173656;
            overflow: hidden;
        }

        .white--text {
            color: #fff;
        }

        .divider {
            height: 3px;
            width: 30px;
            background-color: #e6b260;
        }

        h2 {
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            opacity: .1;
            font-size: 5em;
        }

        .star {
            position: absolute;
            left: -2px;
            opacity: .7;
            max-height: 100vh;
            top: -10vh;
        }

        .circle {
            position: absolute;
            right: 0;
            top: -50px;
            opacity: .5;
            height: 50vh;
            width: 50vh;
        }

        tr {
            z-index: 2;
            position: relative;
        }

        @media only screen and (max-width: 600px) {
            table {
                width: 100%;
                border-spacing: unset;
                height: 100vh;
            }
        }

    </style>
</head>
<body>
    <table dir="rtl">
        <thead>
            <tr>
                <td class="header" align="center">
                    <img src="{{ URL::to('/') }}/files/general/white-logo.png" />
                </td>
            </tr>
            <tr>
                <td>
                    <img class="circle" src="{{ URL::to('/') }}/files/general/circle.png" />
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <img class="star" src="{{ URL::to('/') }}/files/general/star.png" />
                </td>
            </tr>
            <tr>
                <td align="center">
                    <h1>
                        מזל טוב {{ $data['name'] }}!
                    </h1>
                    <h2>
                        מזל טוב 
                    </h2>
                    <div class="divider"></div>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <p>
                        <strong>
                        סיימת את האקדמיה "{{ $data['course_name'] }}" בהצלחה!
                        </strong>
                        <br>
                        מקווים מאוד שנהנת והשתפרת. נשמח אם תוכלו לשתף הלאה.                         
                        <br>
                        <br>
                        אתם מוזמנים להמשיך לחזור ולתרגל שיעורים עד התאריך "{{ $data['end_at'] }}".
                        <br>
                        <br>
                        בקרוב, יצא שלב ההמשך.
                        <br>
                        נתזכר אותך ונשמח לראותך שוב!
                    </p>
                </td>
            </tr>
            @include('mails.templates.footer')
        </tbody>
    </table>
</body>
</html>