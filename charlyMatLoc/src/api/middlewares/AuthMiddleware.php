<?php

namespace charlyMatLoc\src\api\middlewares;

use _PHPStan_90b10482a\Nette\Neon\Exception;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use charlyMatLoc\src\application_core\application\ports\api\dtos\ProfileDTO;

class AuthMiddleware
{
    //clef secrète utilisée pour décoder le token jwt
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            //recup le header authz et extrait le token jwt
            $authHeader = $request->getHeaderLine('Authorization');
            $token = sscanf($authHeader, "Bearer %s")[0] ;
            //décode le token jwt avec la clef secrète et l'algo HS512
            $payload = JWT::decode($token, new Key($this->secretKey, 'HS512'));
        } catch (ExpiredException $e) {
            throw new Exception("Token expiré");
        } catch (SignatureInvalidException $e) {
            throw new Exception("Signature token invalide");
        } catch (BeforeValidException $e) {
            throw new Exception("Token pas encore valide");
        } catch (\UnexpectedValueException $e) {
            throw new Exception("Valeur non attendu reçu");
        }


        //créé le profil à partir du token
        $profile = new ProfileDTO(
            id: $payload->sub,
            email: $payload->data->user,
        );

        //ajoute le profil dans la requête pour l'action suivante
        $request = $request->withAttribute('profile', $profile);

        //passe la requête au handler suivant (action ou middleware)
        return $handler->handle($request);
    }
}
