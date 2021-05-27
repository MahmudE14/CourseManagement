@extends('layouts.dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card my-5">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    @if (Auth::user() !== null)
                        @if (Auth::user()->role->count() && Auth::user()->role[0]->role == 'admin')
                            <h2>Welcome Admin!</h2>
                        @else
                            <h2>Welcome {{ ucwords(Auth::user()->name) }}!</h2>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
