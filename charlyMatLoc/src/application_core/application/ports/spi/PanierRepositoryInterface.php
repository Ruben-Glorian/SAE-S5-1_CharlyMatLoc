<?php
namespace charlyMatLoc\src\application_core\application\ports\spi;

Interface PanierRepositoryInterface{
    public function listerPanier(): array;
    public function ajouterOutil(int $idOutil, string $date): void;
    public function verifDoublons(int $idOutil, string $date): bool;
}