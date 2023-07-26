<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>

    <style>
        .header {
            background-color: #173656;
            box-shadow: inset 0 0 0px 80px #fff;
            height: 250px;
            border-radius: 10px;
            padding: 20px;
        }

        .header img {
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
    <thead>
        <tr>
            <td class="header" align="center">
                <!-- <img src="{{ URL::to('/') }}/files/general/white-logo.png" /> -->
                <img src="{{ URL::to('/') }}/files/general/gstar.png" />
            </td>
        </tr>
    </thead>
</body>
</html>