<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens - Order Completed</title>

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
    <table>
        @include('mails.templates.header')
        <tbody>
            <tr>
                <td align="center">
                    <div class="content">
                        <div>
                            <h1>
                                היי {{ $data['name'] }}, <span class="blue--text">ההזמנה בוצעה בהצלחה!</span>
                            </h1>
                            <small>
                            המספר הזמנה הינו: {{ $data['order_number'] }}
                            </small>
                            <p>
                            תודה רבה שרכשת את הקורס "{{ $data['course_name'] }}".
                            <br>
                            עלות הקורס הינה {{ $data ['price'] }} ש"ח.
                            <br>
                            הקורס זמין עד התאריך "{{ $data['end_at'] }}".
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