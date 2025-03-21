@extends('layouts.master')

@section('heading')
    {{ __('Database') }}
@stop

@section('content')
    <div class="container">
        @if(session('flash_message'))
            <div class="alert alert-success">
                {{ session('flash_message') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('database.reset') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">reset</button>
        </form>

        <br>

    </div>
@endsection
