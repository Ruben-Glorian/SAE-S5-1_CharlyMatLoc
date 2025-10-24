<?php
namespace charlyMatLoc\src\api\providers;

use charlyMatLoc\src\application_core\application\ports\api\dtos\AuthDTO;
use charlyMatLoc\src\application_core\application\ports\api\dtos\CredentialsDTO;
use charlyMatLoc\src\application_core\application\ports\api\dtos\ProfileDTO;
use charlyMatLoc\src\application_core\application\ports\api\ServiceUserInterface;

class JWTAuthnProvider implements AuthnProviderInterface{

    private ServiceUserInterface $serviceUser;
    private JWTManager $JWTManager;

    public function __construct(JWTManager $jwtManager, ServiceUserInterface $serviceUser){
        $this->JWTManager = $jwtManager;
        $this->serviceUser = $serviceUser;
    }

    //authentifie l'utilisateur et génère les tokens jwt
    public function signin(CredentialsDTO $credentials): array
    {
        //recherche l'utilisateur par ses id
        $user = $this->serviceUser->byCredentials($credentials);
        //prépare le payload (le tableau avec les données du token + infos utilisateur) du token jwt
        $payload = [
            'iss' => 'http://charlyMatLoc', //émetteur du token
            'iat' => time(),                //date de création
            'exp' => time()+3600,           //date d'expiration (1h)
            'sub' => $user->id,             //id utilisateur
            'data' => [
                'user' => $user->email      //email utilisateur
            ]
        ];
        //génère le token d'accès et le token de rafraîchissement
        $accessToken  = $this->JWTManager->createAccesToken($payload);
        $refreshToken = $this->JWTManager->createRefreshToken($payload);

        //retourne l'objet AuthDTO (tokens) et le profil utilisateur
        return [new AuthDTO($accessToken, $refreshToken), new ProfileDTO($user->id,$user->email)];
    }

    //inscrit un nouvel utilisateur et retourne son profil
    public function register(CredentialsDTO $credentials): ProfileDTO
    {
        //crée l'utilisateur via le service
        $user = $this->serviceUser->register($credentials);
        return new ProfileDTO($user->id, $user->email);
    }
}