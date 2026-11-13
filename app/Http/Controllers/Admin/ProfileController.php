<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Uploader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Every admin can update their own name, photo and password. */
class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.profile', ['admin' => $request->user('admin')]);
    }

    public function update(Request $request)
    {
        $admin = $request->user('admin');
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => ['required', 'email', 'max:150', Rule::unique('admins', 'email')->ignore($admin->id)],
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'current_password' => 'nullable|required_with:password|current_password:admin',
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        if ($request->hasFile('avatar')) {
            Uploader::delete($admin->avatar);
            $data['avatar'] = Uploader::store($request->file('avatar'), 'admins');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        unset($data['current_password']);
        $admin->update($data);

        return back()->with('success', 'Profile updated.');
    }
}
