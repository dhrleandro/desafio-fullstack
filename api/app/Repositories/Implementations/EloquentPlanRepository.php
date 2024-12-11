<?php

namespace App\Repositories\Implementations;

use App\Domain\Entities\Plan;
use App\Models\Plan as EloquentPlan;
use App\Repositories\PlanRepository;

class EloquentPlanRepository implements PlanRepository
{
    /**
     * @return Plan[]
     */
    public function fetchAll(): array
    {
        $plans = [];
        $eloquentPlans = EloquentPlan::all();

        foreach ($eloquentPlans as $eloquentPlan) {
            $plans[] = Plan::fromArray($eloquentPlan->toArray());
        }

        return $plans;
    }

    public function getById(int $planId): ?Plan
    {
        $eloquentPlan = EloquentPlan::find($planId);
        if (!$eloquentPlan) {
            return null;
        }

        return Plan::fromArray($eloquentPlan->toArray());
    }
}