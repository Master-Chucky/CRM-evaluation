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

        @if(session('import_message'))
            <div class="alert alert-info">
                {{ session('import_message') }}
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

        <form action="{{ route('database.importCsv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="csvFile">Importer le fichier CSV</label>
                <input type="file" name="csv_file" id="csvFile" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Importer CSV</button>
        </form>

    </div>
@endsection
