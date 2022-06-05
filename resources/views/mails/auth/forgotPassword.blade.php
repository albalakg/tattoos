<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiToo - Forgot Password</title>

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

        .header {
            background-color: #173656;
            height: 250px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
        }

        .header img {
            width: 30%;
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

            .header {
                border-radius: unset;
            }
        }

    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td class="header" align="center">
                    <img src="{{ URL::to('/') }}/files/general/white-logo.png" />
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                    <div class="content">
                        <div>
                            <h1>
                                היי {{ $data['name'] }}, <span class="blue--text">שכחת במקרה את הסיסמה?</span>
                            </h1>
                            <p>
                            נשמח לעזור לך ליצור סיסמה חדשה.
                            <br>
                            כל מה שצריך זה ללחוץ על הכפתור למטה ולאחר מכן למלא את הטופס.
                            </p>
                        </div>
                        <br>
                        <img src="{{ URL::to('/') }}/files/general/dark-logo.png" />
                        <br>
                        <br>
                        <br>
                        <br>

                        <a href="{{ URL::to('/')}}/reset-password?token={{ $data['token'] }}">
                            <button>
                                אפס סיסמה
                            </button>
                        </a>
                        
                        <br>
                        <br>
                        <br>
                        <br>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <div class="links">
                        <div class="link">
                            <a href="">
                                <img src="{{ URL::to('/') }}/files/general/instagram.png" />
                            </a>
                        </div>
                        <div class="link">
                            <a href="">
                                <img src="{{ URL::to('/') }}/files/general/facebook.png" />
                            </a>
                        </div>
                        <div class="link">
                            <a href="">
                                <img src="{{ URL::to('/') }}/files/general/linkedin.png" />
                            </a>
                        </div>
                        <div class="link">
                            <a href="">
                                <img src="{{ URL::to('/') }}/files/general/twitter.png" />
                            </a>
                        </div>
                    </div>
                    <br>
                    <small>
                        GOLDENS
                    </small>
                    <br>
                    <small>
                        המקום שלך להתפתח
                    </small>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>