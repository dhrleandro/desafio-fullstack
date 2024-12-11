<?php

namespace App\Repositories;

use App\Domain\Entities\User;

interface UserRepository
{
    public function getById(int $id): ?User;
}