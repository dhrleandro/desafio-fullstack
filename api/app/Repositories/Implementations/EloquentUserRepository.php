<?php

namespace App\Repositories\Implementations;


use App\Repositories\UserRepository;
use App\Domain\Entities\User;
use App\Models\User as EloquentUser;
use function PHPUnit\Framework\throwException;

class EloquentUserRepository implements UserRepository
{
    public function getById(int $userId): ?User
    {
        $user = EloquentUser::with(['contract' => function ($query) {
            $query->where('active', true)
                 ->orderBy('created_at', 'desc')
                 ->limit(1)
                 ->select('id', 'user_id');
        }])->find($userId);

        if (!$user) {
            return null;
        }

        $activeContractId = $user->contract?->first()?->id;
        return User::create($user->id, $activeContractId);
    }
}