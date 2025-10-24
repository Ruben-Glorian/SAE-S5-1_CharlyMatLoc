<?php

namespace charlyMatLoc\src\infrastructure\repositories;

use charlyMatLoc\src\application_core\application\ports\spi\AuthRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\api\dtos\CredentialsDTO;
use charlyMatLoc\src\application_core\domain\entities\User;

class PDOAuthRepository implements AuthRepositoryInterface {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    //recherche un utilisateur par son id
    public function findById(string $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        //retourne un objet User si trouvé
        return new User(
            id: $row['id'],
            email: $row['email'],
            password: $row['password']
        );
    }

    //enregistre un nouvel utilisateur à partir d'un credentialsDTO
    public function save(CredentialsDTO $dto):void {
        $passwordhash = password_hash($dto->password, PASSWORD_BCRYPT); //hash du mdp
        $uuid = bin2hex(random_bytes(16)); //génère un id unique
        $sql = "INSERT INTO users (id, email, password) VALUES (:id, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':email', $dto->email);
        $stmt->bindParam(':password', $passwordhash);
        $stmt->execute();
    }

    //recherche un utilisateur par son email
    public function findByEmail(string $email): ?User {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        //retourne un objet User si trouvé
        return new User(
            id: $row['id'],
            email: $row['email'],
            password: $row['password']
        );

    }

    //inscrit un nouvel utilisateur à partir d'un objet credentials
    public function register($credentials) {
        $uuid = bin2hex(random_bytes(16)); //génère un id unique
        $passwordhash = password_hash($credentials->password, PASSWORD_BCRYPT); //hash du mot de passe
        $sql = "INSERT INTO users (id, email, password) VALUES (:id, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $uuid, \PDO::PARAM_STR);
        $stmt->bindParam(':email', $credentials->email, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $passwordhash, \PDO::PARAM_STR);
        $stmt->execute();
    }
}