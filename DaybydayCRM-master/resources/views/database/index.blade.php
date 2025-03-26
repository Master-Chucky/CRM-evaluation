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

        <!-- @if(session('errors'))
            <div class="alert alert-danger">
                <strong>Erreurs rencontrées :</strong>
                <ul>
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('flash_message'))
            <div class="alert alert-success">
                {{ session('flash_message') }}
            </div>
        @endif -->

        @if(session('success') || session('error'))
            <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} mt-4">
                @if(session('success'))
                    <h5 class="font-weight-bold text-center">
                        <i class="fas fa-check-circle"></i> Importation réussie
                    </h5>
                @else
                    <h5 class="font-weight-bold text-center">
                        <i class="fas fa-exclamation-circle"></i> Erreur lors de l'importation
                    </h5>
                @endif
                
                <div class="text-center mt-3">
                    <p><strong>Fichiers importés :</strong></p>
                    <p>{{ session('file_name') }}</p>
                    <p>{{ session('file_name2') }}</p>
                    <p>{{ session('file_name3') }}</p>
                </div>
                
                @if(session('success'))
                    <div class="text-center mt-3">
                        @if(session('imported_projects_rows'))
                            <span class="badge badge-success mr-2">
                                Projets: {{ session('imported_projects_rows') }} lignes
                            </span>
                        @endif
                        @if(session('imported_project_tasks_rows'))
                            <span class="badge badge-success mr-2">
                                Tâches: {{ session('imported_project_tasks_rows') }} lignes
                            </span>
                        @endif
                        @if(session('imported_offers_rows'))
                            <span class="badge badge-success">
                                Offres: {{ session('imported_offers_rows') }} lignes
                            </span>
                        @endif
                    </div>
                @endif
                
                @if(session('skipped_rows'))
                    <div class="text-center mt-3">
                        <span class="badge badge-danger">
                            Lignes en erreur: {{ session('skipped_rows') }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        @if(session('import_errors'))
            <div class="mt-4">
                <h4 class="text-danger text-center">
                    <i class="fas fa-exclamation-triangle"></i> Erreurs d'import
                </h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Fichier</th>
                                <th>Ligne</th>
                                <th>Champ</th>
                                <th>Erreur</th>
                                <th>Valeur incorrecte</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('import_errors') as $error)
                                <tr class="table-danger">
                                    <td>{{ $error['source_file'] ?? 'N/A' }}</td>
                                    <td>{{ $error['row']-1 }}</td>
                                    <td>{{ $error['attribute'] }}</td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach($error['errors'] as $message)
                                                <li>{{ $message }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ $error['values'][$error['attribute']] ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(session('projects'))
            <div class="mt-4">
                <h4 class="text-success text-center">
                    <i class="fas fa-check-circle"></i> Projets importés
                </h4>
                <div class="table-responsive">
                    <table id="tableProjects" class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Ligne originale</th>
                                <th>Nom du projet</th>
                                <th>Client</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('projects') as $project)
                                <tr>
                                    <td>{{ $project->import_row }}</td>
                                    <td>{{ $project->project_title }}</td>
                                    <td>{{ $project->client_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="paginationProjects" class="pagination"></div>
                </div>
            </div>
        @endif

        @if(session('project_tasks'))
            <div class="mt-4">
                <h4 class="text-success text-center">
                    <i class="fas fa-check-circle"></i> Tâches importées
                </h4>
                <div class="table-responsive">
                    <table id="tableProjectTasks" class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Ligne originale</th>
                                <th>Nom du projet</th>
                                <th>Titre de la tâche</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('project_tasks') as $project_task)
                                <tr>
                                    <td>{{ $project_task->import_row }}</td>
                                    <td>{{ $project_task->project_title }}</td>
                                    <td>{{ $project_task->task_title }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="paginationProjectTasks" class="pagination"></div>
                </div>
            </div>
        @endif

        @if(session('offers'))
            <div class="mt-4">
                <h4 class="text-success text-center">
                    <i class="fas fa-check-circle"></i> Offres importées
                </h4>
                <div class="table-responsive">
                    <table id="tableOffers" class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Ligne originale</th>
                                <th>Nom du client</th>
                                <th>Titre du lead</th>
                                <th>Type</th>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('offers') as $offer)
                                <tr>
                                    <td>{{ $offer->import_row }}</td>
                                    <td>{{ $offer->client_name }}</td>
                                    <td>{{ $offer->lead_title }}</td>
                                    <td>{{ $offer->type }}</td>
                                    <td>{{ $offer->produit }}</td>
                                    <td>{{ $offer->prix }}</td>
                                    <td>{{ $offer->quantite }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="paginationOffers" class="pagination"></div>
                </div>
            </div>
        @endif

        <form action="{{ route('database.importCsv') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="csvFile">Project Client CSV</label>
                <input type="file" name="file" id="csvFile" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="csvFile">Project Task CSV</label>
                <input type="file" name="file2" id="csvFile" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="csvFile">Lead CSV</label>
                <input type="file" name="file3" id="csvFile" class="form-control" required>
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
