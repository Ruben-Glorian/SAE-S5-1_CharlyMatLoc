<?php

namespace charlyMatLoc\webui\actions;

use charlyMatLoc\src\api\actions\AbstractAction;
use charlyMatLoc\src\application_core\application\usecases\ServiceReservation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ReservationsViewAction extends AbstractAction {
    private ServiceReservation $serviceReservation;

    public function __construct(ServiceReservation $serviceReservation) {
        $this->serviceReservation = $serviceReservation;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        $userId = $_SESSION['user_id'] ?? null;
        $queryParams = $rq->getQueryParams();
        if ($userId === null && isset($queryParams['user_id'])) {
            $userId = (int)$queryParams['user_id'];
        }

        $reservations = [];
        $total = 0.0;

        if ($userId !== null) {
            $data = $this->serviceReservation->listerReservations($userId);
            $reservations = $data['items'] ?? [];
            $total = $data['total'] ?? 0.0;
        }

        $view = Twig::fromRequest($rq);
        return $view->render($rs, 'reservations.twig', [
            'reservations' => $reservations,
            'total' => $total,
            'user_id' => $userId
        ]);
    }
}
