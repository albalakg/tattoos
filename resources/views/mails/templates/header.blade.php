<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens -Forgot Password</title>

    <style>
        .header {
            background-color: #173656;
            height: 250px;
            border-radius: 10px;
            padding: 20px;
        }

        .header img {
            width: 30%;
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
    <thead>
        <tr>
            <td class="header" align="center">
                <img src="{{ URL::to('/') }}/files/general/white-logo.png" />
            </td>
        </tr>
    </thead>
</body>
</html>