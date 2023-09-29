<?php

class UserPasskey extends Model
{
    public function create($userId, $data)
    {
        // $dt = new \DateTime();
        // $userDateTime = $dt->format('Y-m-d H:i:s');

        $db = $this->database->prepare('INSERT INTO users_passkeys (user_id, data) VALUES (:userId, :keydata)');

        $data =  json_encode($data, JSON_UNESCAPED_SLASHES);

        $db->bindParam('userId', $userId, PDO::PARAM_STR);
        $db->bindParam('keydata', $data, PDO::PARAM_STR);

        return $db->execute();
    }

    public function get($userId) {
        $db = $this->database->prepare('SELECT * FROM users_passkeys WHERE user_id = :userId');

        $db->bindParam('userId', $userId, PDO::PARAM_STR);

        $db->execute();

        return $db->fetch(PDO::FETCH_ASSOC);
    }
}