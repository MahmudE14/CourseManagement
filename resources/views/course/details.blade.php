@extends('layouts.dashboard')
@section('content')

@if($course)
<div class="mx-5 mt-3">
    <div class="row">
        <div class="col-sm-4 py-4">
            <img id="course_details_thumbnail" src="{{ $course->thumbnail }}" style="min-width: 250px; max-width: 40%">
        </div>
        <div class="col-sm-8 pt-5">
            <h3 id="course_details_title" class="mt-2">{{ $course->title }}</h3>
            <h6 class="text-disabled mt-0 mb-4">Course Code: <strong id="course_details_code">{{ $course->code }}</strong></h6>
            <div class="my-3">
                <strong class="mb-3">Progress: <span id="course_progress">0%</span></strong>
                <div class="progress">
                    <div class="progress-bar" id="course_progress_bar" role="progressbar" style="width: 1%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                  </div>
            </div>
            <strong class="mb-3">Details:</strong>
            <p id="course_details_description" class="pl-2">{{ $course->description }}</p>
        </div>
    </div>
    <div class="row">
        <div class="h3 pl-3 pt-3">Units</div>
        @if ($course->units)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Files</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($course->units as $unit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $unit->title }}</td>
                    <td>{{ $unit->code }}</td>
                    <td>{{ $unit->description }}</td>
                    <td>
                        @if ($unit->files && count($unit->files))
                            @foreach ($unit->files as $file)
                            <a href="{{ asset($file->location) }}" download="{{ $file->title }}.pdf">{{ $file->title  }}</a>
                            @endforeach
                        @else
                        <span>No File</span>
                        @endif
                    </td>
                    @if(!in_array($unit->id, $completedUnits))
                    <td>
                        <button class="btn btn-success btn-sm" onclick="completeUnit({{ $course->id }}, {{ $unit->id }})">Complete</button>
                    </td>
                    @else
                    <td>
                        Completed
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        getCourseProgress();
    });

    function getCourseProgress() {
        $.get('{{ URL("getCourseProgress") }}', {
            user_id: '{{ Auth::user()->id }}',
            course_id: '{{ $course->id }}'
        }).then(res => {
            if (res.status !== 'success') {
                return alertify.error('An error occurred!');
            } else {
                let total = res.data.course_units;
                let completed = res.data.completed_units;
                let percentage = completed / total * 100;

                if (total == 0) {
                    setCourseProgress(0);
                } else {
                    setCourseProgress(percentage);
                }
            }
        });
    }

    function setCourseProgress(percentage) {
        if (percentage < 0) {
            percentage = 0;
        } else if (percentage > 100) {
            percentage = 100;
        }
        $('#course_progress').text(`${percentage}%`);
        $('#course_progress_bar').attr('style', `width:${percentage}%;`);
        $('#course_progress_bar').attr('aria-valuenow', percentage);
        $('#course_progress_bar').html(`${percentage}%`);
    }

    function completeUnit(course_id, unit_id) {
        alertify.confirm("This is a confirm dialog.",
            function(){
                completeUnitAjax(course_id, unit_id);
            },
            function(){
                alertify.error('Cancel');
            }
        );
    }

    function completeUnitAjax(course_id, unit_id) {
        $.ajax({
            url: '{{ URL("completeUnit") }}',
            method: 'GET',
            data: {
                course_id: course_id,
                unit_id: unit_id
            },
            success: data => {
                if (data.status === 'success') {
                    alertify.success('Success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alertify.error('An error occurred!');
                }
            },
            error: err => {
                alertify.error('An error occurred!');
            }
        });
    }
</script>
@endsection
