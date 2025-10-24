<?php

namespace charlyMatLoc\src\application_core\domain\entities;

class Reservation {
    private int $id;
    private string $user_id;
    private string $outil_id;
    private string $date_location;
    private string $date_reservation;

    public function __construct(
        int    $id,
        string $user_id,
        string $outil_id,
        string $date_location,
        string $date_reservation
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->outil_id = $outil_id;
        $this->date_location = $date_location;
        $this->date_reservation = $date_reservation;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getOutilId(): string
    {
        return $this->outil_id;
    }

    public function getDateLocation(): string
    {
        return $this->date_location;
    }
    public function getDateReservation(): string
    {
        return $this->date_reservation;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'outil_id' => $this->outil_id,
            'date_location' => $this->date_location,
            'date_reservation' => $this->date_reservation
        ];
    }
}