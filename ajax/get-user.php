<?php

include_once(__DIR__ . '/../autoload.php');

$user = new User();

$userData = $user->get($_POST['username']);

header('Content-Type: application/json');
echo json_encode($userData, JSON_UNESCAPED_SLASHES);
exit;