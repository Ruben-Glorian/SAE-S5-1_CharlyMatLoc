<?php

namespace charlyMatLoc\webui\actions;

use charlyMatLoc\src\api\actions\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class SigninViewAction extends AbstractAction {
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $query = $request->getQueryParams();
        $params = [
            'error' => $query['error'] ?? null,
            'email' => $query['email'] ?? ''
        ];

        $view = Twig::fromRequest($request);
        return $view->render($response, 'signin.twig', $params);
    }
}
