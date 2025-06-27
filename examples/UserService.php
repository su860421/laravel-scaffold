<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    // Business logic method examples
    // public function registerUser(array $data)
    // {
    //     $data['password'] = bcrypt($data['password']);
    //     return $this->repository->create($data);
    // }

    // public function activateUser(int $id)
    // {
    //     return $this->repository->update($id, ['status' => 'active']);
    // }
}
