<?php
namespace charlyMatLoc\src\api\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;

class CorsMiddleware implements MiddlewareInterface{
    private array $allowedOrigins;
    private bool $requireOrigin;
    private bool $allowAll;

    /**
     * @param array $allowedOrigins Liste d'origines autorisées (vide = pas de whitelist)
     * @param bool $requireOrigin Si true, absence de header Origin -> exception
     * @param bool $allowAll Si true, utilisera '*' comme Access-Control-Allow-Origin
     */
    public function __construct(array $allowedOrigins = [], bool $requireOrigin = false, bool $allowAll = true){
        $this->allowedOrigins = $allowedOrigins;
        $this->requireOrigin = $requireOrigin;
        $this->allowAll = $allowAll;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface{
        $origin = $request->getHeaderLine('Origin');

        if($this->requireOrigin && $origin === ''){
            throw new HttpUnauthorizedException($request, 'missing Origin Header (cors)');
        }

        $response = $handler->handle($request);

        //Déterminer la valeur de Access-Control-Allow-Origin
        if($this->allowAll){
            $allowOrigin = '*';
        }elseif (!empty($this->allowedOrigins)){
            $allowOrigin = in_array($origin, $this->allowedOrigins, true) ? $origin : 'null';
        }else{
            //Par défaut on renvoit l'origine fournie si présente, sinon *
            $allowOrigin = $origin !== '' ? $origin : '*';
        }

        return $response
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Max-Age', 3600)
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}