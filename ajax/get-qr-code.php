<?php

use RobThree\Auth\TwoFactorAuth;

include_once(__DIR__ . '/../autoload.php');

$user = new User();

$userData = $user->get($_POST['username']);

$tfa = new TwoFactorAuth();
$qrCodeUrl = $tfa->getQRCodeImageAsDataUri('Demo', $userData['2fa_secret']);

if (empty($qrCodeUrl)) {
    die('Failed to generate QR code');
}

?>

<img src="<?php echo $qrCodeUrl; ?>" />