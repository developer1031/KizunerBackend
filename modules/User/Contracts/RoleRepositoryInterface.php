<?php

namespace Modules\User\Contracts;

use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface
{
    /**
     * @param array $data
     * @return Role
     */
    public function create(array $data);

    public function update(array $data);

    public function delete($id);
}
