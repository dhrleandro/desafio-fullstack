<?php

namespace App\Repositories;

use App\Domain\Entities\Plan;

interface PlanRepository
{
    /**
     * @return Plan[]
     */
    public function fetchAll(): array;
    public function getById(int $planId): ?Plan;
}