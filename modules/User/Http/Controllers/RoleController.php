<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Http\Requests\RoleStoreRequest;
use Modules\User\Http\Requests\RoleUpdateRequest;
use Modules\User\Services\RoleManager;
use Spatie\Permission\Models\Role;

class RoleController
{
    public function index()
    {
        return view('module.user::admin.role.index');
    }

    public function data(RoleManager $roleManager)
    {
        return $roleManager->getDatatable();
    }

    public function create()
    {
        $role = new Role();
        return view('module.user::admin.role.create', compact('role'));
    }

    public function edit(RoleManager $roleManager, $id)
    {
        $role = $roleManager->get($id);
        return view('module.user::admin.role.edit', compact('role'));
    }

    public function store(RoleManager $roleManager, RoleStoreRequest $request)
    {
        $validated = $request->validated();

        if ($validated) {
            $response = $roleManager->save($request);

            return $response ? redirect(route('module.user.role.index'))
                : redirect()->back()->with('error', 'Some thing wrong!');
        }
    }

    public function update(RoleManager $roleManager, RoleUpdateRequest $request)
    {
        $validated = $request->validated();

        if ($validated) {
            $response = $roleManager->update($request);

            return $response ? redirect(route('module.user.role.index'))
                : redirect()->back()->with('error', 'Some thing wrong!');
        }
    }

    public function delete(RoleManager $roleManager, $id)
    {
        $response = $roleManager->delete($id);
        return $response ? redirect(route('module.user.role.index'))
            : redirect()->back()->with('error', 'Something Wrong');
    }
}
