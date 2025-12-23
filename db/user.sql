SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE users (
  idUser int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  firstName varchar(50) NOT NULL,
  lastName varchar(50) NOT NULL,
  role varchar(50) NOT NULL DEFAULT 'user',
  mail varchar(50) UNIQUE NOT NULL,
  nickName varchar(50) NOT NULL,
  password varchar(60) DEFAULT NULL,
  address varchar(50) DEFAULT NULL,
  address_2 varchar(50) DEFAULT NULL,
  address_3 varchar(50) DEFAULT NULL,
  zip int(11) DEFAULT NULL,
  city varchar(50) DEFAULT NULL,
  country varchar(50) DEFAULT NULL,
  dateInscription datetime NOT NULL DEFAULT current_timestamp(),
  isVerified int(1) NOT NULL DEFAULT 0,
  verifyToken varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
