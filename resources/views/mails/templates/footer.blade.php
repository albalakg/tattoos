<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>

    <style>
        .links {
            display: flex;
            justify-content: space-between;
            width: 50%;
            margin: auto;
        }

        .link {
            margin: auto;
        }

        .link img {
            width: 30px;
            max-width: 30px;
        }
        
    </style>
</head>
<body>
    <tr>
        <td align="center">
            <div class="links">
                <div class="link">
                    <a target="_blank" href="https://instagram.com/goldens.fa?igshid=MjEwN2IyYWYwYw==">
                        <img src="{{ URL::to('/') }}/files/general/instagram.png" />
                    </a>
                </div>
                <div class="link">
                    <a target="_blank" href="https://www.facebook.com/profile.php?id=100094130323045">
                        <img src="{{ URL::to('/') }}/files/general/facebook.png" />
                    </a>
                </div>
                <div class="link">
                    <a target="_blank" href="https://www.tiktok.com/@goldens.fa?lang=en">
                        <img src="{{ URL::to('/') }}/files/general/tik-tok.png" />
                    </a>
                </div>
                <div class="link">
                    <a target="_blank" href="https://www.youtube.com/@GOLDENSACADEMY">
                        <img src="{{ URL::to('/') }}/files/general/youtube.png" />
                    </a>
                </div>
            </div>
            <br>
            <small dir="ltr">
                Goldens Sport Services LTD.
            </small>
            <br>
            <small>
                המקום שלך להתפתח
            </small>
            <br>
            <br>
        </td>
    </tr>
</body>
</html>