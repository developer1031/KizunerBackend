<?php

namespace Modules\User\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Modules\User\Http\Requests\UserCreateRequest;
use Modules\User\Http\Requests\UserUpdateRequest;
use Modules\User\Services\UserManager;

class UserController
{
    public function index()
    {
        return view('user::user.index');
    }

    public function data(UserManager $userManager)
    {
        return $userManager->getDatatable();
    }

    public function create()
    {
        $user = new User();
        return view('module.user::admin.user.create', compact('user'));
    }

    public function edit(UserManager $userManager, $id)
    {
        $user = $userManager->get($id);
        return view('module.user::admin.user.edit', compact('user'));
    }

    public function delete(UserManager $userManager, $id)
    {
        $response = $userManager->delete($id);
        return $response ? redirect(route('module.user.user.index'))
            : redirect()->back()->with('error', 'You can not delete yourself');
    }

    public function store(UserManager $userManager, UserCreateRequest $request)
    {
        $validated = $request->validated();

        if ($validated) {
            $response = $userManager->create($request);
            return $response ? redirect(route('module.user.user.index'))
                : redirect()->back();
        }
    }

    public function update(UserManager $userManager, UserUpdateRequest $request)
    {

        $validated = $request->validated();

        if ($validated) {
            $response = $userManager->update($request);

            return $response ? redirect(route('module.user.user.index')) :
                redirect()->back()->with('error', 'Old password wrong');
        }
    }
}
