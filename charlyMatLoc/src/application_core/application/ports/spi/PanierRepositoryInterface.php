<?php
namespace charlyMatLoc\src\application_core\application\ports\spi;

Interface PanierRepositoryInterface{
    public function listerPanier(): array;
}