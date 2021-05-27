<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Unit;
use App\Models\UnitFile;
use App\Models\UsersCourse;
use App\Models\UsersCourseUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (Auth::user() !== null) {
            $courses = Course::all();
            if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin') {
                $units = Unit::select('*')
                            ->with('course')
                            ->get();
            } else {
                $user_id = Auth::user()->id;
                $units = Unit::select('*')->whereHas('user', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                })->get();
            }

            return view('unit.all', compact('units', 'courses'));
        }
    }

    /**
     * Send a JSON data of the resource.
     *
     * @return \Illuminate\Http\Response JSON
     */
    public function getAllUnits()
    {
        if (Auth::user() !== null) {
            if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin') {
                $units = Unit::select('*')
                            ->with('course')
                            ->toArray();
            } else {
                $units = UsersCourse::where('user_id', Auth::user()->id)
                                        ->with('courses.units')
                                        ->toArray();
            }

            return response()->json(['status' => 'success', 'message' => $units], 200);
        }

        return response()->json(['status' => 'error', 'messege' => 'Unauthorized'], 401);
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
            'course_id' => 'required',
            'unit_file' => 'required|mimes:pdf'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->getMessageBag(), 400);
        }

        if ($request->hasFile('unit_file')) {
            $upload_folder = date("Ymd") . '_' . str_replace(' ', '_', $request->title);
            $file_location = $this->fileUpload($request, $upload_folder);
        }

        DB::beginTransaction();

        try {
            $unit = Unit::create([
                'code' => $request->code,
                'title' => $request->title,
                'description' => $request->description,
                'course_id' => $request->course_id,
            ]);

            $unit_file = UnitFile::create([
                'unit_id' => $unit->id,
                'title' => $unit->title,
                'location' => $file_location,
                'status' => 1,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'An error occurred while creating unit!'], 500);
        }

        if ($unit->id && $unit_file->id) {
            return response()->json(['status' => 'success', 'message' => 'Unit created successfully!'], 200);
        }

        return response()->json(['status' => 'error', 'message' => 'An error occurred while creating unit!'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response JSON
     */
    public function show(Unit $unit)
    {
        return response()->json([
            'status' => 'success',
            'data' => Unit::select('*')->with(['course', 'files'])->toArray()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit)
    {
        return $unit->delete();
    }

    /**
     * Set the unit as complete
     *
     * @return \Illuminate\Http\Response JSON
     */
    public function completeUnit(Request $request)
    {
        $user_course_unit = UsersCourseUnit::create([
            'user_id' => Auth::user()->id,
            'course_id' => $request->course_id,
            'present_unit_id' => $request->unit_id,
        ]);

        if ($user_course_unit->id) {
            return response()->json([
                'status' => 'success',
                'messege' => 'Unit completed successfully!'
            ], 200);
        }

        return response()->json(['status' => 'error', 'messege' => 'An error occurred!'], 500);
    }

    /**
     * Upload course thumbnail image
     */
    public function fileUpload(Request $request, String $upload_folder) {
        $this->validate($request, [
            'unit_file' => 'required|mimes:pdf',
        ]);

        if ($request->hasFile('unit_file')) {
            $image = $request->file('unit_file');
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = '/unit/' . $upload_folder;
            $destinationPath = public_path($path);
            $image->move($destinationPath, $name);
            // $this->save();

            $file_location = $path . '/' . $name;

            return $file_location;
        }
    }
}
