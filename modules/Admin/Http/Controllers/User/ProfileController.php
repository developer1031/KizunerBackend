<?php

namespace Modules\Admin\Http\Controllers\User;

use Illuminate\Http\Request;

class ProfileController
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password != "") {
            if ($request->has('password_confirm')) {
                if ($request->password == $request->password_confirm) {
                    $user->password = bcrypt($request->password);
                } else {
                    return redirect()->back()->withError('Password confirmation to not match');
                }
            } else {
                return redirect()->back()->withError('Please enter password confirmation');
            }
        }
        $user->save();
        return redirect()->back()->withSuccess('Update user info successful');
    }
}
