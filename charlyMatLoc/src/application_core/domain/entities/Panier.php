<?php


namespace charlyMatLoc\src\application_core\domain\entities;

class Panier
{
    private int $id;
    private string $outil_id;
    private string $date_location;
    private string $date_ajout;
    public function __construct(
        int     $id,
        string  $outil_id,
        string  $date_location,
        string  $date_ajout
    )
    {
        $this->id = $id;
        $this->outil_id = $outil_id;
        $this->date_location = $date_location;
        $this->date_ajout = $date_ajout;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getOutilId(): string
    {
        return $this->outil_id;
    }
    public function getDateLocation(): string
    {
        return $this->date_location;
    }
    public function getDateAjout(): string
    {
        return $this->date_ajout;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'outil_id' => $this->outil_id,
            'date_location' => $this->date_location,
            'date_ajout' => $this->date_ajout
        ];
    }
}