@extends('layouts.dashboard')
@section('content')
    <h3 class="m-3 mx-4">All Courses</h3>

    @auth
        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
        <div class="text-right mt-4 mb-5 mx-5">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#create_course_modal">Create Course</button>
        </div>
        @endif
    @endauth

    <div class="table table-bordered">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Course Name</th>
                    <th>Course Code</th>
                    <th>Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="courses">
                @foreach ( $courses as $course )
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><img class="img-responsive img-thumbnail" src="{{ $course->thumbnail }}" width="80" /></td>
                    <td>{{ $course->title }}</td>
                    <td>{{ $course->code }}</td>
                    <td>{{ $course->description }}</td>
                    @auth
                        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="courseDetails({{ $course->id }})">Details</button>
                            <button class="btn btn-sm btn-info" onclick="addUnit({{ $course->id }})">Add Unit</button>
                        </td>
                        @else
                        <td>
                            @if ($course->enrolledCourse)
                            <a class="btn btn-sm btn-info" href='{{ URL("courses/$course->id/details") }}'>Go to Course</a>
                            @else
                            <button class="btn btn-sm btn-primary" onclick="courseDetails({{ $course->id }})">Details</button>
                            <button class="btn btn-sm btn-success" onclick="registerCourse({{ $course->id }})">Register</button>
                            @endif
                        </td>
                        @endif
                    @endauth
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @auth
        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
        {{-- create course modal --}}
        <div class="modal fade bd-example-modal-lg" id="create_course_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="card">
                        <form action="" id="course_insert_form" enctype="multipart/form-data">
                            <div class="card-body p-5">
                                <h4 class="mb-3">Create Course</h4>
                                <div class="form-group my-3">
                                    <label for="">Course Title</label>
                                    <input id="title" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Course Code</label>
                                    <input id="code" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Course Description</label>
                                    <input id="description" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Course Thumbanail</label>
                                    <input id="thumbnail_img" type="file" class="form-control" required>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- create unit modal --}}
        <div class="modal fade bd-example-modal-lg" id="create_unit_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="card">
                        <form action="" id="unit_insert_form" enctype="multipart/form-data">
                            <div class="card-body p-5">
                                <h4 class="mb-3">Add Unit</h4>
                                <input type="hidden" id="unit_course_id">
                                <div class="form-group my-3">
                                    <label for="">Unit Title</label>
                                    <input id="unit_title" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Unit Code</label>
                                    <input id="unit_code" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Unit Description</label>
                                    <input id="unit_description" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Unit File</label>
                                    <input id="unit_file" type="file" class="form-control" required>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endauth

    {{-- course details modal --}}
    <div class="modal fade bd-example-modal-lg" id="course_details_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="course_details_modal_title" class="modal-title">Course Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4 p-3">
                                    <img id="course_details_thumbnail" src="" style="max-width: 250px">
                                </div>
                                <div class="col-sm-8 p-5">
                                    <h3 id="course_details_title" class="mt-2"></h3>
                                    <h6 class="text-disabled mt-0 mb-4">Course Code: <strong id="course_details_code"></strong></h6>
                                    <strong class="mb-3">Details:</strong>
                                    <p id="course_details_description" class="pl-2"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>
function courseDetails(id) {
    // empty the fields first
    $('#course_details_thumbnail').attr('src', '');
    $('#course_details_title').text('');
    $('#course_details_code').text('');
    $('#course_details_description').text('');
    $('#course_details_modal_title').text('');

    $.ajax({
        url: `{{ URL("courses") }}/${id}`,
        method: 'GET',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: data => {
            $('#course_details_thumbnail').attr('src', data.thumbnail);
            $('#course_details_title').text(data.title);
            $('#course_details_code').text(data.code);
            $('#course_details_description').text(data.description);
            $('#course_details_modal_title').text(data.title);

            $('#course_details_modal').modal('show');
        },
        error: err => {
            alertify.error('An error occurred!');
        }
    })
}

function registerCourse(id) {
    alertify.confirm("Are you sure to enroll in this course?",
        function(){
            registerCourseAjax(id);
        },
        function(){
            alertify.error('Cancel');
        }
    );
}

function registerCourseAjax(id) {
    $.ajax({
        url: '{{ URL("registerInCourse") }}',
        method: 'GET',
        data: { course_id: id },
        success: data => {
            if (data.status === 'success') {
                alertify.success('Success');
                setTimeout(() => location.href = '{{ URL("myCourses") }}', 1000);
            } else if (data.status === 'error' && data.type == 'NO_UNIT') {
                alertify.alert("Empty Course!", function(){
                    alertify.message('The course has no units associated with it. Please try again later.');
                });
            } else {
                alertify.error('An error occurred!');
            }
        },
        error: err => {
            if (err.responseJSON.status === 'error' && err.responseJSON.type == 'NO_UNIT') {
                alertify.alert("Empty Course!", 'The course has no units associated with it. Please try again later.', function(){
                    alertify.message('The course has no units associated with it. Please try again later.');
                });
            } else {
                alertify.error('An error occurred!');
            }
        }
    });
}
</script>

@auth
@if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
<script>
$(document).ready(function () {
    $('#course_insert_form').on('submit', insertCourse);
    $('#create_unit_modal').on('submit', insertUnit);
});

function insertCourse(e) {
    e.preventDefault();
    e.stopPropagation();

    let form_data = new FormData($('#course_insert_form')[0]);
    form_data.append('_token', '{{ csrf_token() }}');
    form_data.append('title', $('#title').val());
    form_data.append('code', $('#code').val());
    form_data.append('description', $('#description').val());
    form_data.append('thumbnail_img', $('#thumbnail_img')[0].files[0]);

    $.ajax({
        method: 'POST',
        url: "{{ URL('courses') }}",
        data: form_data,
        processData: false,
        contentType: false,
        success: data => {
            if (data.status !== 'success') {
                alertify.error('An error occurred!');
            } else {
                alertify.success(data.message);
                setTimeout(() => location.reload(), 1000);
            }
        },
        error: err => {
            if (err.responseJSON.message) {
                alertify.error('An error occurred!');
            }
        }
    });
}

function courseDetails(id) {
    // empty the fields first
    $('#course_details_thumbnail').attr('src', '');
    $('#course_details_title').text('');
    $('#course_details_code').text('');
    $('#course_details_description').text('');
    $('#course_details_modal_title').text('');

    $.ajax({
        url: `{{ URL("courses") }}/${id}`,
        method: 'GET',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: data => {
            $('#course_details_thumbnail').attr('src', data.thumbnail);
            $('#course_details_title').text(data.title);
            $('#course_details_code').text(data.code);
            $('#course_details_description').text(data.description);
            $('#course_details_modal_title').text(data.title);

            $('#course_details_modal').modal('show');
        },
        error: err => {
            alertify.error('An error occurred!');
        }
    })
}

function addUnit(id) {
    $('#unit_course_id').val(id);
    $('#create_unit_modal').modal('show');
}

function insertUnit(e) {
    e.preventDefault();
    e.stopPropagation();

    let form_data = new FormData($('#unit_insert_form')[0]);
    form_data.append('_token', '{{ csrf_token() }}');
    form_data.append('course_id', $('#unit_course_id').val());
    form_data.append('title', $('#unit_title').val());
    form_data.append('code', $('#unit_code').val());
    form_data.append('description', $('#unit_description').val());
    form_data.append('unit_file', $('#unit_file')[0].files[0]);

    if ($('#unit_file')[0].files[0].type !== 'application/pdf') {
        return alertify.error('Only PDF files are allowed!');
    }

    $.ajax({
        method: 'POST',
        url: "{{ URL('units') }}",
        data: form_data,
        processData: false,
        contentType: false,
        success: data => {
            if (data.status !== 'success') {
                alertify.error('An error occurred!');
            } else {
                alertify.success(data.message);
                setTimeout(() => location.reload(), 1000);
            }
        },
        error: err => {
            console.log(err);
            if (err.status === 422) {
                alertify.error('An error occurred! Please check input!');
            } else if (err.responseJSON.message) {
                alertify.error('An error occurred!');
            }
        }
    });
}

</script>
@endif
@endauth

@endsection
