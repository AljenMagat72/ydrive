<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Registration - YDrive Canada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; margin:0; padding:20px; color:#333;">

    <div style="background:#ffffff; max-width:600px; margin:0 auto; padding:30px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05);">

        <!-- YDrive Logo -->
        <div style="text-align:center; margin-bottom:20px;">
            <img
                src="{{ $message->embed(public_path('images/ydrive.jpg')) }}"
                alt="Ydrive Logo"
                style="max-width:150px; width:100%; height:auto;">
        </div>

        <p>Hello,</p>

        <p>Your admin registration link is below. Please use it to create your administrator account:</p>

        <p style="text-align:center;">
            <a href="{{ $link }}"
                style="display:inline-block; padding:12px 20px; background-color:#007bff; color:#fff !important; text-decoration:none; border-radius:5px; font-weight:bold;">
                Register Admin Account
            </a>
        </p>

        <p>If the button above doesn’t work, you can also use the link below:</p>

        <p>
            <a href="{{ $link }}" style="color:#007bff;">{{ $link }}</a>
        </p>

        <p>If you did not request this registration, you may safely ignore this message.</p>

        <p>Thank you,<br>
            <strong>YDrive Canada Team</strong>
        </p>

        <div style="margin-top:20px; font-size:12px; color:#777; text-align:center;">
            &copy; {{ date('Y') }} YDrive Canada. All rights reserved.
        </div>

    </div>

    <!-- Responsive Fix -->
    <style>
        @media only screen and (max-width: 620px) {
            body {
                padding: 10px !important;
            }

            .container {
                padding: 20px !important;
            }

            img {
                max-width: 100% !important;
                height: auto !important;
            }

            a.button {
                padding: 10px 16px !important;
                font-size: 16px !important;
            }
        }
    </style>

</body>

</html>
