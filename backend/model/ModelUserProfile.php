<?php

class ModelUserProfile extends ModelBaseUser
{
    public function getUser(EntitieUser $user)
    {
        $req = $this->conn->prepare('SELECT idUser, nickName, firstName, lastName, mail, role, address, address_2, address_3, zip, city, country FROM users WHERE idUser = :idUser');
        $req->bindValue(":idUser", $user->getIdUser(), PDO::PARAM_INT);
        $req->execute();
        $data = $req->fetch();
        if ($data) {
            $user = new EntitieUser(
                [
                    'idUser' => $data['idUser'],
                    'nickName' => $data['nickName'],
                    'firstName' => $data['firstName'],
                    'lastName' => $data['lastName'],
                    'mail' => $data['mail'],
                    'address' => $data['address'] ?? null,
                    'address2' => $data['address_2'] ?? null,
                    'address3' => $data['address_3'] ?? null,
                    'zip' => $data['zip'] ?? null,
                    'city' => $data['city'] ?? null,
                    'country' => $data['country'] ?? null,
                    'role' => $data['role'] ?? null
                ]
            );
        } else {
            return null; // Utilisateur non trouvé
        }
        return $data ? $user : null;
    }
    public function updateUser(EntitieUser $user)
    {
        $query = "UPDATE users SET firstName = :firstName, lastName = :lastName, mail = :mail";
        $params = [
            ':firstName' => $user->getFirstName(),
            ':lastName' => $user->getLastName(),
            ':mail' => $user->getMail(),
            ':idUser' => $user->getIdUser()
        ];

        // Ajouter le surnom si il est fourni
        if ($user->getNickName() !== null) {
            $query .= ", nickName = :nickName";
            $params[':nickName'] = $user->getNickName();
        }

        // Ajouter l'adresse si elle est fournie
        if ($user->getAddress() !== null) {
            $query .= ", address = :address";
            $params[':address'] = $user->getAddress();
        }
        // Ajouter l'adresse 2 si elle est fournie
        if ($user->getAddress2() !== null) {
            $query .= ", address_2 = :address2";
            $params[':address2'] = $user->getAddress2();
        }
        // Ajouter l'adresse 3 si elle est fournie
        if ($user->getAddress3() !== null) {
            $query .= ", address_3 = :address3";
            $params[':address3'] = $user->getAddress3();
        }

        // Ajouter le code postal si il est fourni
        if ($user->getZip() !== null) {
            $query .= ", zip = :zip";
            $params[':zip'] = $user->getZip();
        }

        // Ajouter la ville si elle est fournie
        if ($user->getCity() !== null) {
            $query .= ", city = :city";
            $params[':city'] = $user->getCity();
        }

        // Ajouter le pays si il est fourni
        if ($user->getCountry() !== null) {
            $query .= ", country = :country";
            $params[':country'] = $user->getCountry();
        }

        $query .= " WHERE idUser = :idUser";

        $req = $this->conn->prepare($query);

        foreach ($params as $param => $value) {
            $req->bindValue($param, $value);
        }

        return $req->execute();
    }
}
