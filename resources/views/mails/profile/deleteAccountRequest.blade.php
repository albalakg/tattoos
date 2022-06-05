<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account Request</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td align="center">
                    <h1>
                        Delete Account Request
                    </h1>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">
                    <p>
                        Hello {{ $data->user_name }},
                        <br>
                        Are you sure you want to delete your account?
                        <br>
                        From the moment the account is deleted, there is no way to restore the lost data.
                    </p>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <a href="{{URL::to('/')}}/delete-account?email={{ $data->email }}&token={{ $data->token }}&status=1">
                        <button>
                            Confirm
                        </button>
                    </a>
                </td>
                <td align="right">
                    <a href="{{URL::to('/')}}/delete-account?email={{ $data->email }}&token={{ $data->token }}&status=0">
                        <button>
                            Cancel Request
                        </button>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>