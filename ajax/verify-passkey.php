<?php

use lbuchs\WebAuthn\WebAuthn;

include_once(__DIR__ . '/../autoload.php');

$username = $_POST['username'];

$user = new User();
$userData = $user->get($username);

$userPasskey = new UserPasskey();
$passKeyData = $userPasskey->get($userData['user_id']);