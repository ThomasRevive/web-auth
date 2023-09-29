<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/classes/Authenticator.php');

$authenticator = new Authenticator();

$optUrl = $authenticator->generateOPTCode('AuthTesting', 'Thomas');
$qrCodeUrl = $authenticator->generateQrCode($optUrl);

if (!empty($_POST)) {
    $authCode = $_POST['auth_code'];

    if ($authenticator->verifyAuthCode($authCode)) {
        echo 'code works';
    }
    else {
        echo 'invalid code';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h4>QR Code</h4>

    <div class="qr-code">
        <img src="<?php echo $qrCodeUrl; ?>" />
    </div>

    <h4>Verify Code</h4>

    <form action="" method="post">
        <input type="text" name="auth_code" />
        <input type="submit" value="Verify" />
    </form>
</body>
</html>