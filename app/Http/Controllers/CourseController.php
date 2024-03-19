<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\Course\CourseRequest;

class CourseController extends Controller
{

    public function index()
    {
        $courses = Course::all();
        return response()->json($courses);
    }

    public function store(CourseRequest $request)
    {
        $courses = Course::create($request->all());
        return response()->json($courses, 201);
    }

    public function update(CourseRequest $request, string $id)
    {
        $courses = Course::findOrFail($id);
        $courses->update($request->all());

        return response()->json($courses, 200);
    }

    public function destroy(string $id)
    {
        $courses = Course::findOrFail($id);
        $courses->delete();

        return response()->json(null, 204);
    }
}
