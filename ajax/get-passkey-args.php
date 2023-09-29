<?php

use lbuchs\WebAuthn\WebAuthn;

include_once(__DIR__ . '/../autoload.php');

$username = $_POST['username'];

$user = new User();
$userData = $user->get($username);

$userPasskey = new UserPasskey();
$passKeyData = $userPasskey->get($userData['user_id']);

$keyCreds = json_decode($passKeyData['data'], true);

$webAuthN = new WebAuthn('Auth Test', 'auth.dev.local', ['none']);
$getArgs = $webAuthN->getGetArgs([$keyCreds['credentialId']]);

header('Content-Type: application/json');
echo json_encode($getArgs);

exit;