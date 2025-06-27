<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use JoeSu\LaravelScaffold\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    // Custom method examples
    // public function findByEmail(string $email)
    // {
    //     return $this->model->where('email', $email)->first();
    // }

    // public function findActiveUsers()
    // {
    //     return $this->model->where('status', 'active')->get();
    // }
}
