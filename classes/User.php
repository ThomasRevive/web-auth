<?php

class User extends Model {
    public function create($username) {
        // $authenticator = new Authenticator();
        // $secret = $authenticator->generateSecret();

        $secret = new EAMann\TOTP\Key();

        $dt = new \DateTime();
        $userDateTime = $dt->format('Y-m-d H:i:s');

        $db = $this->database->prepare('INSERT INTO users (username, 2fa_secret, date_created) VALUES (:username, :code, :date_created)');

        $db->bindParam('username', $username, PDO::PARAM_STR);
        $db->bindParam('code', $secret, PDO::PARAM_STR);
        $db->bindParam('date_created', $userDateTime, PDO::PARAM_STR);

        return $db->execute();
    }

    public function get($username) {
        $db = $this->database->prepare('SELECT * FROM users WHERE username = :username');

        $db->bindParam('username', $username, PDO::PARAM_STR);

        $db->execute();

        $user = $db->fetch(PDO::FETCH_ASSOC);

        return $user;
    }
}