<?php

use lbuchs\WebAuthn\WebAuthn;

include_once(__DIR__ . '/../autoload.php');

try {
    session_start();

    $user = new User();
    $userData = $user->get($_POST['username']);

    $webAuthN = new WebAuthn('Auth Test', 'auth.dev.local', ['none']);

    $createArgs = $webAuthN->getCreateArgs(\hex2bin(str_pad($userData['user_id'], 16, STR_PAD_RIGHT)), $userData['username'],$userData['username']);

    header('Content-Type: application/json');
    echo json_encode($createArgs);

    $_SESSION['challenge'] = $webAuthN->getChallenge();
}
catch(Exception $ex) {
    $return = new stdClass();
    $return->success = false;
    $return->msg = $ex->getMessage();

    header('Content-Type: application/json');
    echo json_encode($return);
}

exit;