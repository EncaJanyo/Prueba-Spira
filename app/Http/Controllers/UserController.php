<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\UserRequest;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users);
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        $user->save();
        $user->assignRole($request->role);
        return response()->json($user, 201);
    }

    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        if ($request->has('password') && $request->filled('password')) {
            $user->update($request->all());
        } else {
            $data = $request->except('password');
            $user->update($data);
        }
        $user->roles()->detach();
        $user->assignRole($request->role);

        return response()->json($user, 200);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }

    public function list()
    {
        $users = User::has('courses')->with('courses')->get();
        return response()->json($users);
    }
}
