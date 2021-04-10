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
                        Have you forgot your password?
                    </h1>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                    <p>
                        You have 1 hour to reset your password with this token.
                        <br>
                        Afterwards, you will have to get a new one.
                        <br>
                        Your token is: <strong>{{ $data->token }}</strong>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>