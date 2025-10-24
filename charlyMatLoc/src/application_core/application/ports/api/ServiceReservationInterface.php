<?php
namespace charlyMatLoc\src\application_core\application\ports\api;

Interface ServiceReservationInterface{
    public function listerReservations(string $userId): array;
    public function ajouterOutil(int $idOutil, string $date, ?string $userId = null): void;
}