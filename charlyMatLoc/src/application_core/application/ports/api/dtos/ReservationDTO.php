<?php

namespace charlyMatLoc\src\application_core\application\ports\api\dtos;

class ReservationDTO {
    public function __construct(
        public readonly int $user_id,
        public readonly int $outil_id,
        public readonly string $date_location,
        public readonly float $tarif = 0.0,
        public readonly string $status = 'reserved'
    ) {}

    public function toArray(): array {
        return [
            'user_id' => $this->user_id,
            'outil_id' => $this->outil_id,
            'date_location' => $this->date_location,
            'date_reservation' => date('Y-m-d H:i:s'),
        ];
    }
}

