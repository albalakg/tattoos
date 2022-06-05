<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Course Almost Expired</title>

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
    <table>
        @include('mails.templates.header')
        <tbody>
            <tr>
                <td align="center">
                    <div class="content">
                        <div>
                            <h1>
                                היי {{ $data['name'] }}, <span class="blue--text">הקורס עומד להיגמר!</span>
                            </h1>
                            <p>
                                זהו השבוע האחרון בו יהיה לך גישה לתכני הקורס "{{ $data['course_name'] }}".
                                <br>
                                התוקף לקורס נגמר בתאריך "{{ $data['end_at'] }}".
                                <br>
                                נצלו את הרגעים האחרונים וכנסו לאתר.
                                <br>
                                ניתן להיכנס לעמוד הקורס דרך הכפתור למטה
                            </p>
                        </div>
                        <br>
                        <img src="{{ URL::to('/') }}/files/general/dark-logo.png" />
                        <br>
                        <br>
                        <br>
                        <br>

                        <a href="{{URL::to('/')}}/courses/{{ $data['course_id'] }}">
                            <button>
                                היכנס לקורס
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