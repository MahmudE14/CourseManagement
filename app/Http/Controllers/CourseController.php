<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Unit;
use App\Models\UsersCourse;
use App\Models\UsersCourseUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (Auth::user() !== null) {
            if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin') {
                $courses = Course::all();
            } else {
                $user_id = Auth::user()->id;
                $courses = Course::select('*')
                                ->with('enrolledCourse.units')
                                ->withCount('units')
                                ->get();
            }

            return view('course.all', compact('courses'));
        }

        return response()->json(['status' => 'error', 'messege' => 'Unauthorized'], 401);
    }

    /**
     * Display a listing of the enrolled courses
     *
     * @return View
     */
    public function myCourses()
    {
        $user_id = Auth::user()->id;
        $courses = Course::select('*')->whereHas('userCourse', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })->get();

        return view('course.all', compact('courses'));

        return response()->json(['status' => 'error', 'messege' => 'Unauthorized'], 401);
    }

    /**
     * Display a single course detils
     *
     * @return View
     */
    public function courseDetails(Course $course)
    {
        $completedUnits = UsersCourseUnit::where('course_id', $course->id)
                                        ->where('user_id', Auth::user()->id)
                                        ->get()
                                        ->pluck('present_unit_id')
                                        ->toArray();

        // dd(
        //     UsersCourseUnit::where('course_id', $course->id)
        //                     ->where('user_id', Auth::user()->id)
        //                     ->get()->pluck('present_unit_id')->toArray()
        // );


        return view('course.details', compact('course', 'completedUnits'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file_location = '';

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255',
            'code' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:1000',
            'thumbnail_img' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        if ($request->hasFile('thumbnail_img')) {
            $upload_folder = date("Ymd") . '_' . str_replace(' ', '_', $request->title);
            $file_location = $this->fileUpload($request, $upload_folder);
        }

        $course = Course::create([
            'code' => $request->code,
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $file_location ? $file_location : '',
        ]);

        if ($course->id) {
            return response()->json(['status' => 'success', 'message' => 'Course created successfully!'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'An error occurred while creating course!'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return response()->json($course, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     *
     * @todo add image upload
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        $filter_letters = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', ' '];
        $code = str_replace($filter_letters, '', $request->title);

        $course = Course::create([
            'code' => $code,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($course->id) {
            return response()->json(['status' => 'success', 'message' => 'Course created successfully!'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'An error occurred while creating course!'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     *
     * @todo find the delete's value and set the return value of the function accordingly
     */
    public function destroy(Course $course)
    {
        $course = $course->delete();
        return response()->json($course, 200);
    }

    /**
     * Display a single course detils
     *
     * @return View
     */
    public function getCourseProgress(Request $request)
    {
        $course_units = Unit::where('course_id', $request->course_id)->get()->count();
        $completed_units = UsersCourseUnit::where('course_id', $request->course_id)
                                            ->where('user_id', Auth::user()->id)
                                            ->get()
                                            ->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'course_units' => $course_units,
                'completed_units' => $completed_units,
            ]
        ]);
    }

    /**
     * Register in a course
     *
     * @return View
     */
    public function registerInCourse(Request $request)
    {
        DB::beginTransaction();
        try {
            $user_course = UsersCourse::create([
                'user_id' => Auth::user()->id,
                'course_id' => $request->course_id,
            ]);

            $unit = Unit::where('course_id', $request->course_id)->first();
            $unit_id = $unit ? $unit->id : 1;

            $user_course_unit = UsersCourseUnit::create([
                'user_id' => Auth::user()->id,
                'course_id' => $request->course_id,
                'present_unit_id' => $unit_id,
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'messege' => 'Course registered!'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
            return response()->json([
                'status' => 'error',
                'messege' => 'Server error!'
            ], 500);
        }
    }

    /**
     * Upload course thumbnail image
     */
    public function fileUpload(Request $request, String $code) {
        $this->validate($request, [
            'thumbnail_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($request->hasFile('thumbnail_img')) {
            $image = $request->file('thumbnail_img');
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = '/course/' . $code;
            $destinationPath = public_path($path);
            $image->move($destinationPath, $name);
            // $this->save();

            $file_location = $path . '/' . $name;

            return $file_location;
        }
    }
}
