<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\api\providers\AuthnProviderInterface;
use charlyMatLoc\src\application_core\application\ports\api\dtos\CredentialsDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class SignInAction {
    public function __construct(
        private readonly AuthnProviderInterface $authnProvider
    )
    {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (($email==='') OR ($password==='')){
                throw new \Exception("Email ou mot de passe non fourni");
            }
            $credentials = new CredentialsDTO($data['email'], $data['password']);
            $resSignIn = $this->authnProvider->signin($credentials);

            $authDTO = $resSignIn[0];
            $profile = $resSignIn[1];
            $payload = [
                'access_token'  => $authDTO->accesToken,
                'refresh_token' => $authDTO->refreshToken,
            ];

            $res = [
                'payload' => $payload,
                'profile' => $profile
            ];


            $view = Twig::fromRequest($request);
            return $view->render($response, 'connected.twig', $res);


        }catch (\Exception $e){
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(400);
        }

    }
}



