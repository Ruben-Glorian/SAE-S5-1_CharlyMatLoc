<?php
namespace charlyMatLoc\src\application_core\application\ports\spi;

Interface PanierRepositoryInterface{
    public function listerPanier(string $userId): array;
    public function ajouterOutil(int $idOutil, string $date, string $userId): void;
    public function verifDoublons(int $idOutil, string $date, string $userId): bool;
}