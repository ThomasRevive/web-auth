<?php
use RobThree\Auth\TwoFactorAuth;

require_once(__DIR__ . '/autoload.php');

if (!empty($_POST)) {
    if (!empty($_POST['generate_user'])) {
        $user = new User();

        $newUser = $user->create($_POST['username']);

        if ($newUser) {
            echo 'User created';
        }
        else {
            echo 'User failed to create';
        }
    }
    else if (!empty($_POST['verify_user'])) {
        $authCode = $_POST['auth_code'];

        $user = new User();
        $userData = $user->get($_POST['username']);

        $tfa = new TwoFactorAuth();

        if ($tfa->verifyCode($userData['2fa_secret'], $authCode)) {
            echo 'Code works';
        } else {
            echo 'Invalid code';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Test</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/assets/js/auth.js" type="text/javascript"></script>
</head>
<body>
    <h4>Generate User</h4>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" />
        <input type="submit" name="generate_user" value="Generate" />
    </form>

    <h4>Get QR Code</h4>

    <form action="" method="post" id="qr_code_form">
        <input type="text" name="username" placeholder="Username" />
        <input type="submit" name="get_qr_code" value="Get QR Code" />
    </form>

    <div class="qr-code-container"></div>

    <h4>Verify Code</h4>

    <form action="" method="post">
        <input type="text" name="username" placeholder="Username" />
        <input type="text" name="auth_code" placeholder="OPT" />
        <input type="submit" name="verify_user" value="Verify" />
    </form>
</body>
</html>