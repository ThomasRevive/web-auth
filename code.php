<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/classes/Authenticator.php');

$authenticator = new Authenticator();

$optUrl = $authenticator->generateOPTCode('AuthTesting', 'Thomas');
$qrCodeUrl = $authenticator->generateQrCode($optUrl);

?>

<img src="<?php echo $qrCodeUrl; ?>" />