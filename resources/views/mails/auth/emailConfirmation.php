<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiToo - Forgot Password</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td align="center">
                    <h1>
                        Email Confirmation
                    </h1>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                    <p>
                        Hey, we just want to make sure it is your email.
                        <br>
                        Please just click on the link below to confirm it.
                        <br>
                        <a href="{{URL::to('/')}}/email-confirmation&token={{ $data->token }}"></a>
                        <br>
                        Thanks for your time!
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>