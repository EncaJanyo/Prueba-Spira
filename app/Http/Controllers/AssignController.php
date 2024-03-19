<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignController extends Controller
{
    public function index()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
    
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $user = User::find($request->user_id);

        foreach ($request->course_ids as $courseId) {
            $user->courses()->attach($courseId);
        }
        return response()->json($user, 201);
    }

    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->courses()->sync($request->course_ids);

        return response()->json($user, 200);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->courses()->detach();

        return response()->json(null, 204);
    }

    public function assign(string $id)
    {
        $user = User::findOrFail($id);
        $assignedCourses = $user->courses()->get();
        return response()->json($assignedCourses);
    }

    public function unassign(string $id)
    {
        $user = User::findOrFail($id);
        $assignedCourses = $user->courses;
        $allCourses = Course::all();
        $unassignedCourses = $allCourses->diff($assignedCourses);
        return response()->json($unassignedCourses);
    }
}
