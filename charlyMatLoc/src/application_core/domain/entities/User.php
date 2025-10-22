<?php

namespace charlyMatLoc\src\application_core\domain\entities;

class User{

    private string $id;
    private string $email;
    private string $password;

    public function __construct(string $id, string $email, string $password){
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getId(): string{
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}