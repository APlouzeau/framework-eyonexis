<?php

class ModelUserAuth extends ModelBaseUser
{
    public function login(string $mail, string $password)
    {
        $query = "SELECT * FROM users WHERE mail = :mail";
        $req = $this->conn->prepare($query);
        $req->bindValue(":mail", $mail);
        $req->execute();

        $data = $req->fetch(PDO::FETCH_ASSOC);
        if (!$data || !password_verify($password, $data['password'])) {
            return null;
        }
        if ($data['isVerified'] == 0) {
            return ['isVerified' => false];
        } else {
            $user =
                [
                    'idUser' => $data['idUser'],
                    'nickName' => $data['nickName'],
                    'firstName' => $data['firstName'],
                    'lastName' => $data['lastName'],
                    'mail' => $data['mail'],
                    'role' => $data['role'],
                ];
            return $user;
        }
    }
}
