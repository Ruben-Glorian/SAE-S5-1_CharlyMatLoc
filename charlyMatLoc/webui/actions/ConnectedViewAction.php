<?php

namespace charlyMatLoc\webui\actions;

use charlyMatLoc\src\api\actions\AbstractAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ConnectedViewAction extends AbstractAction {
    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        $profile = $rq->getAttribute('profile', null);

        $params = ['profile' => $profile];

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'connected.twig', $params);
    }
}
