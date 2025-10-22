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

    public function signin(CredentialsDTO $credentials): array
    {
        $user = $this->serviceUser->byCredentials($credentials);
        $payload = [
            'iss' => 'http://charlyMatLoc',
            'iat' => time(),
            'exp' => time()+3600,
            'sub' => $user->id,
            'data' => [
                'user' => $user->email
            ]
        ];
        $accessToken  = $this->JWTManager->createAccesToken($payload);
        $refreshToken = $this->JWTManager->createRefreshToken($payload);

        return [new AuthDTO($accessToken, $refreshToken), new ProfileDTO($user->id,$user->email)];
    }
    public function register(CredentialsDTO $credentials): ProfileDTO
    {
        $user = $this->serviceUser->register($credentials);
        return new ProfileDTO($user->id, $user->email);
    }
}