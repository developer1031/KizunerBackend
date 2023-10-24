<?php

namespace Modules\User\Contracts;

use App\User;
use Modules\User\Http\Requests\UserApiCreateRequest;

interface UserRepositoryInterface
{
    public function create(array $data);

    public function update(array $data);

    public function get(string $id);

    public function delete(string $id);

    public function updateInfo(string $userId, array $data);

    public function getByEmail(string $email);

    public function  getUserNameByIds(array $ids);
}
