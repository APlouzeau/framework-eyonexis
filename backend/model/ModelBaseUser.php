<?php

require_once APP_PATH . "/class/ClassDatabase.php"; // Inclure la classe PDOServer

class ModelBaseUser
extends ClassDatabase
{
    public function checkMail(string $mail)
    {
        $query = "SELECT idUser FROM users WHERE mail = :mail";
        $req = $this->conn->prepare($query);
        $req->bindValue(":mail", $mail);
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $idUser = $data['idUser'];
            return $idUser;
        } else {
            return false;
        }
    }

    public function checkPassword(int $idUser, string $password)
    {
        $query = "SELECT password FROM users WHERE idUser = :idUser";
        $req = $this->conn->prepare($query);
        $req->bindValue(":idUser", $idUser, PDO::PARAM_INT);
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        return $data && password_verify($password, $data['password']) ? true : false;
    }

    public function setNewToken(int $idUser, string $token)
    {
        $query = "UPDATE users SET verifyToken = :verifyToken WHERE idUser = :idUser";
        $req = $this->conn->prepare($query);
        $req->bindValue(":verifyToken", $token, PDO::PARAM_STR);
        $req->bindValue(":idUser", $idUser, PDO::PARAM_INT);
        return $req->execute();
    }

    public function verifyEmail(string $verifyToken)
    {
        $req = $this->conn->prepare("SELECT idUser FROM users WHERE verifyToken = :verifyToken");
        $req->bindValue(":verifyToken", $verifyToken);
        $req->execute();
        $data = $req->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return false; // Token invalide
        }
        $query = "UPDATE users SET isVerified = 1 WHERE verifyToken = :verifyToken";
        $req = $this->conn->prepare($query);
        $req->bindValue(":verifyToken", $verifyToken);
        if ($req->execute()) {
            $query = "UPDATE users SET verifyToken = NULL WHERE verifyToken = :verifyToken";
            $req = $this->conn->prepare($query);
            $req->bindValue(":verifyToken", $verifyToken);
            $req->execute();
            return $data['idUser'];
        } else {
            return false;
        }
    }

    public function updatePassword(int $idUser, string $password)
    {
        $query = "UPDATE users SET password = :password WHERE idUser = :idUser";
        $req = $this->conn->prepare($query);
        $req->bindValue(":idUser", $idUser, PDO::PARAM_INT);
        $req->bindValue(":password", $password,  PDO::PARAM_STR);
        return $req->execute();
    }
}
