<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>updateStatus</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <td align="center">
                    <h1>
                    updateStatus
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
                        You have just started the course "{{ $data->course }}".
                        <br>
                        The course cost {{ $data->price }} NIS.
                        <br>
                        The course ends at {{ $data->end_at }}.
                        <br>
                        Thanks
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