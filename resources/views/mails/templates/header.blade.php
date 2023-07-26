<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>

    <style>
        .header {
            height: 250px;
            border-radius: 10px;
            padding: 20px;
        }

        thead td {
            width: 33%;
            background-color: #173656;
        }

        .header div {
            width: 50%;
            margin: auto;
        }

        .header div img {
            width: 100%;
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
                <div>
                    <!-- <img src="{{ URL::to('/') }}/files/general/white-logo.png" /> -->
                    <img src="{{ URL::to('/') }}/files/general/gstar.png" />
                </div>
            </td>
        </tr>
    </thead>
</body>
</html>