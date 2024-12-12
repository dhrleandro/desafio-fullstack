<?php

namespace App\Http\Controllers;

use App\CQS\Queries;
use App\Exceptions\ResponseException;
use App\Models\User as EloquentUser;

class UserController extends Controller
{
    protected Queries $queries;

    public function __construct(Queries $queries)
    {
        $this->queries = $queries;
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $userId = config("api.user_id");

        $user = EloquentUser::with(['contract' => function ($query) {
            $query->where('active', true)
                 ->orderBy('created_at', 'desc')
                 ->limit(1)
                 ->select('id', 'user_id');
        }])->find($userId);

        if (!$user) {
            throw new ResponseException(
                'User not found',
                ['user_id'=> $userId]
            );
        }

        $activeContract = $user->contract?->first();
        $user  = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'active_contract' => $activeContract
        ];
        
        if (!$activeContract) {
            unset($user['active_contract']);
        }

        return response()->json($user);
    }
}
