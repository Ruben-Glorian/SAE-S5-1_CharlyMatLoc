<?php

namespace charlyMatLoc\src\application_core\application\usecases;

use charlyMatLoc\src\application_core\application\ports\api\ServiceReservationInterface;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;

class ServiceReservation implements ServiceReservationInterface
{
    private ReservationRepositoryInterface $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function listerReservations(string $userId): array
    {
        return $this->reservationRepository->listerReservations($userId);
    }

    public function ajouterOutil(int $idOutil, string $date, ?string $userId = null): void
    {
        $this->reservationRepository->ajouterOutil($idOutil, $date);
    }
}