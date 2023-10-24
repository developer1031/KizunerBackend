<?php

namespace Modules\Admin\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;

class AdminController
{
    public function index()
    {
        return view("user::admin");
    }

    public function data()
    {
        $userQuery = User::where('admin', true);
        return DataTables::of($userQuery)->make(true);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ], Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $type = $request->input('type');

        if ($type == 'add') {
            $user = new User();
            $user->name = $request->name;

            $emailCheck = User::where('email', $request->email)->first();
            if ($emailCheck) {
                return redirect()->back()->withError(
                    "Email already exist"
                );
            }
            $user->email = $request->email;
            $user->admin = true;

            if ($request->password === "") {
                return redirect()->back()->withError(
                    "Password must not empty"
                );
            }

            if ($request->password != $request->password_confirm) {
                return redirect()->back()->withError(
                    "Password does not match"
                );
            }
            $user->password = bcrypt($request->password);
            $user->save();
            return redirect()->back()->withSuccess("Add new user successful!");
        }

        if ($type == 'edit') {
            $user = User::find($request->id);
            $user->name = $request->name;
            if ($user->email != $request->email) {
                $emailCheck = User::where('email', $request->email)->first();
                if ($emailCheck) {
                    return redirect()->back()->withError(
                        "Email already exist"
                    );
                }
                $user->email = $request->email;
            }

            if ($request->has('password')) {
                if ($request->password === "") {
                    return redirect()->back()->withError(
                        "Password must not empty"
                    );
                }
                if ($request->password !== $request->password_confirm) {
                    return redirect()->back()->withError(
                        "Password does not match"
                    );
                }
                $user->password = bcrypt($request->password);
            }

            $user->save();
            return redirect()->back()->withSuccess("Update admin user successful!");
        }
    }

    public function destroy(string $id)
    {
        User::find($id)->delete();
        return redirect()->back()->withSuccess("Delete user successful!");
    }
}
