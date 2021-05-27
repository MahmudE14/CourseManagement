@extends('layouts.dashboard')
@section('content')
    <h3 class="m-3 mx-4">All Units</h3>

    @auth
        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
        <div class="text-right mt-4 mb-5 mx-5">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#create_unit_modal">Create Unit</button>
        </div>
        @endif
    @endauth

    <div class="table table-bordered">
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Unit Name</th>
                    <th>Unit Code</th>
                    <th>Course Code</th>
                    <th>Details</th>
                    <th>Files</th>
                    @auth
                        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
                        {{-- <th>Action</th> --}}
                        @endif
                    @endauth
                </tr>
            </thead>
            <tbody id="courses">
                @foreach ( $units as $unit )
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $unit->title }}</td>
                    <td>{{ $unit->code }}</td>
                    <td>{{ $unit->course->code ? $unit->course->code : '' }}</td>
                    <td>{{ $unit->description }}</td>
                    <td>
                        @if($unit->files && count($unit->files) > 0)
                            @foreach ($unit->files as $file)
                                <a href="{{ asset($file->location) }}" download="{{ $file->title }}.pdf">{{ $file->title }}</a>
                            @endforeach
                        @endif
                    </td>
                    @auth
                        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
                        <td>
                            {{-- <button class="btn btn-sm btn-primary" onclick="unitDetails({{ $unit->id }})">Details</button> --}}
                        </td>
                        @else
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="unitDetails({{ $unit->id }})">Details</button>
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
        {{-- create unit modal --}}
        <div class="modal fade bd-example-modal-lg" id="create_unit_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="card">
                        <form action="" id="unit_insert_form" enctype="multipart/form-data">
                            <div class="card-body p-5">
                                <h4 class="mb-3">Add Unit</h4>
                                <div class="form-group my-3">
                                    <label for="">Unit Title</label>
                                    <input id="unit_title" type="text" class="form-control" required>
                                </div>
                                <div class="form-group my-3">
                                    <label for="">Course</label>
                                    <select id="unit_course_id" class="form-control" required>
                                        <option>Select Course</option>
                                        @if ($courses)
                                        @foreach ($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                        @endif
                                    </select>
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
@endsection

@section('scripts')

@auth
@if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
<script>
    $(document).ready(function () {
    $('#unit_insert_form').on('submit', insertUnit);
});

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
