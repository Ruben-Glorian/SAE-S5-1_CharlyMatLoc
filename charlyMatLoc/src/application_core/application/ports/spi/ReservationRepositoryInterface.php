<?php
namespace charlyMatLoc\src\application_core\application\ports\spi;

Interface ReservationRepositoryInterface{
    public function listerReservations(string $userId): array;
    public function ajouterOutil(int $idOutil, string $date, ?string $userId = null): void;
}