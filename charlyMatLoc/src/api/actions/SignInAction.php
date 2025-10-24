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

    public function __invoke(Request $request, Response $response): Response{
        try {
            //recup des données
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            //vérif des champs
            if (($email==='') OR ($password==='')){
                throw new \Exception("Email ou mot de passe non fourni");
            }
            $credentials = new CredentialsDTO($data['email'], $data['password']);
            //appelle le provider d'authentification pour vérifier les identifiants
            $resSignIn = $this->authnProvider->signin($credentials);

            //recup le token et le profil utilisateur
            $authDTO = $resSignIn[0];
            $profile = $resSignIn[1];

            //rep a renvoyer
            $res = [
                'token' => $authDTO->accesToken,
                'profile' => $profile
            ];

            $contentType = $request->getHeaderLine('Content-Type');
            if (str_contains($contentType, 'application/json')) {
                $response->getBody()->write(json_encode($res, JSON_PRETTY_PRINT));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
            } else {
                $view = \Slim\Views\Twig::fromRequest($request);
                return $view->render($response, 'connected.twig', $res);
            }
        }catch (\Exception $e){
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}