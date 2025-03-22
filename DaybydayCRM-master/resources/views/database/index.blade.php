@extends('layouts.master')

@section('heading')
    {{ __('Database') }}
@stop

@php
    $tables = [
        'leads', 'comments', 'mails', 'tasks', 'projects', 
        'absences', 'contacts', 'invoice_lines', 'appointments', 
        'payements', 'invoices', 'offers', 'clients'
    ];
@endphp

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

        <form action="{{ route('database.importCsv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="csvFile">Importer le fichier CSV</label>
                <input type="file" name="csv_file" id="csvFile" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Importer CSV</button>
        </form>

        <div class="cards">
            @foreach($tables as $table)
                <div class="card-body">
                    <form action="{{ route('database.generate') }}" method="POST">
                        @csrf

                        <h4 class="card-title">{{ ucfirst(str_replace('_', ' ', $table)) }}</h4>

                        <div class="form-group">
                            <label for="{{ $table }}" class="form-label text-capitalize">
                                Nombre :
                            </label>
                            <input type="number" class="form-control" id="{{ $table }}" name="tables[{{ $table }}]" min="0" value="10">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="generate_{{ $table }}" class="btn btn-success mt-3">
                                <i class="fa fa-database"></i> Générer
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>

        <form action="{{ route('database.reset') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">reset</button>
        </form>

    </div>
@endsection
