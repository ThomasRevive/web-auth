<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth - Passkey</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/assets/js/passkey.js" type="text/javascript"></script>
</head>
<body>
    <div id="passkey_container">
        <form action="" method="post" id="passkey_form">
            <input type="text" name="username" placeholder="Username" />
            <input type="submit" value="Create Passkey" />
        </form>

        <br />

        <form action="" method="post" id="passkey_verify_form">
            <input type="text" name="username" placeholder="Username" />
            <input type="submit" value="Verify Passkey" />
        </form>
    </div>
</body>
</html>