<?php

class ModelUserRegistration extends ModelBaseUser
{
    public function register(EntitieUser $user)
    {
        $query = "INSERT INTO users (firstName, lastName, mail, nickName, password, verifyToken) VALUES (:firstName, :lastName, :mail, :nickName, :password, :verifyToken)";

        $req = $this->conn->prepare($query);
        $req->bindValue(":firstName", $user->getFirstName());
        $req->bindValue(":lastName", $user->getLastName());
        $req->bindValue(":mail", $user->getMail());
        $req->bindValue(":nickName", $user->getNickName());
        $req->bindValue(":password", $user->getPassword());
        $req->bindValue(":verifyToken", $user->getVerifyToken());

        return $req->execute();
    }
}
