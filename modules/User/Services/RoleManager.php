<?php

namespace Modules\User\Services;

use Modules\User\Contracts\RoleRepositoryInterface;
use Modules\User\Http\Requests\RoleStoreRequest;
use Modules\User\Http\Requests\RoleUpdateRequest;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class RoleManager
{

    private $dataTables;

    private $roleRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        DataTables $dataTables
    ) {
        $this->roleRepository = $roleRepository;
        $this->dataTables = $dataTables;
    }

    public function save(RoleStoreRequest $request)
    {
        $role = $this->roleRepository->create($request->all(['name', 'guard_name']));
        return $role;
    }

    public function update(RoleUpdateRequest $request)
    {
        $role = $this->roleRepository->update($request->all(['id', 'name', 'guard_name']));
        return $role;
    }

    public function delete($id)
    {
        return $this->roleRepository->delete($id);
    }

    public function getDatatable()
    {
        return $this->dataTables->eloquent(Role::query())->make(true);
    }

    public function get($id)
    {
        return $this->roleRepository->get($id);
    }
}
