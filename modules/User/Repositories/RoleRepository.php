<?php

namespace Modules\User\Repositories;

use Modules\User\Contracts\RoleRepositoryInterface;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function create($data)
    {
        $role = new Role($data);
        $role->save();
        return $role;
    }

    public function get($id)
    {
        return Role::find($id);
    }

    public function update($data)
    {
        return Role::find($data['id'])
            ->update($data);
    }

    public function delete($id)
    {
        return Role::destroy($id);
    }
}
