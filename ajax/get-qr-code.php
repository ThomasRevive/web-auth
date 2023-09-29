<?php

include_once(__DIR__ . '/../autoload.php');

$user = new User();

$userData = $user->get($_POST['username']);

$authenticator = new Authenticator($userData['2fa_secret']);

$optUrl = $authenticator->generateOPTCode('AuthTesting', $userData['username']);
// $qrCodeUrl = $authenticator->generateQrCode($optUrl);

$key = EAMann\TOTP\Key::import($userData['2fa_secret']);

$qrCodeUrl = $key->qrCode('AuthTesting', $userData['username']);

if (empty($qrCodeUrl)) {
    die('Failed to generate QR code');
}

?>

<img src="<?php echo $qrCodeUrl; ?>" />