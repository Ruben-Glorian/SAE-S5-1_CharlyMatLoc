<?php

namespace charlyMatLoc\src\api\actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use charlyMatLoc\src\infrastructure\repositories\PDOAuthRepository;
use charlyMatLoc\src\application_core\application\ports\api\dtos\CredentialsDTO;

class SignUpAction {
    public function __construct(private readonly PDOAuthRepository $authRepository) {}

    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if ($email === '' || $password === '') {
                throw new \Exception("Email ou mot de passe non fourni");
            }

            $dto = new CredentialsDTO(email: $email, password: $password);
            $this->authRepository->save($dto);

            $response->getBody()->write(json_encode(['success' => true, 'email' => $email], JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}