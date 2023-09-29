<?php

use lbuchs\WebAuthn\WebAuthn;

require_once(__DIR__ . '/../autoload.php');

session_start();

$post = $_POST;

$clientDataJSON = base64_decode($post['clientDataJSON']);
$attestationObject = base64_decode($post['attestationObject']);
$challenge = $_SESSION['challenge'];

$webAuthN = new WebAuthn('Auth Test', 'auth.dev.local', ['none']);

// processCreate returns data to be stored for future logins.
// in this example we store it in the php session.
// Normaly you have to store the data in a database connected
// with the user name.

try {
    $data = $webAuthN->processCreate($clientDataJSON, $attestationObject, $challenge, false, true, false);

    $dataArray = [];

    foreach ($data as $key => $value) {
        if (is_object($value)) {
            $value = chunk_split(strval($value), 64);

        } else if (is_string($value) && strlen($value) > 0 && htmlspecialchars($value, ENT_QUOTES) === '') {
            $value = chunk_split(bin2hex($value), 64);
        }

        $dataArray[$key] = $value;
    }

    $userPasskey = new UserPasskey();
    $newPassKey = $userPasskey->create(1, $dataArray);

    $msg = 'registration success.';
    if ($data->rootValid === false) {
        $msg = 'registration ok, but certificate does not match any of the selected root ca.';
    }

    $return = new stdClass();
    $return->success = true;
    $return->msg = $msg;

    header('Content-Type: application/json');
    echo json_encode($return, JSON_UNESCAPED_SLASHES);
    exit;
}
catch(Exception $ex) {
    $return = new stdClass();
    $return->success = false;
    $return->msg = $ex->getMessage();

    header('Content-Type: application/json');
    echo json_encode($return, JSON_UNESCAPED_SLASHES);
    exit;
}
