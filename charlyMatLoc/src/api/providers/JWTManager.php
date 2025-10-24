<?php

namespace charlyMatLoc\src\api\providers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException ;
use Firebase\JWT\BeforeValidException;

class JWTManager{
    private string $key;
    //algo utilisé pour la signature (par défaut HS256)
    private string $alg;

    public function __construct(string $key, string $alg = 'HS256'){
        $this->key = $key;
        $this->alg = $alg;
    }

    //génère un token jwt à partir d'un payload
    public function generateToken(array $payload): string {
        return JWT::encode($payload, $this->key, $this->alg);
    }

    //crée un token d'accès (type access)
    public function createAccesToken(array $payload): string {
        $payload['type'] = 'access'; // Ajoute le type au payload
        return $this->generateToken($payload);
    }

    //crée un token de rafraîchissement (type refresh)
    public function createRefreshToken(array $payload): string {
        $payload['type'] = 'refresh'; // Ajoute le type au payload
        return $this->generateToken($payload);
    }

    //décode et verif un token jwt, retourne le payload sous forme de tableau
    public function decodeToken(string $token): array {
        try {
            $decoded = JWT::decode($token, new Key($this->key, $this->alg));
            return (array) $decoded;
        } catch (ExpiredException $e) {
            throw new \Exception("Token expiré : " . $e->getMessage());
        }catch(SignatureInvalidException $e1){
            throw new \Exception("Erreur de la signature du token : " . $e1->getMessage());
        }catch (BeforeValidException $e2){
            throw new \Exception("Token pas encore valide : " . $e2->getMessage());
        }
    }
}