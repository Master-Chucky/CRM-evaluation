<?php

namespace App\Http\Controllers;

use App\Services\Import\UploadService;
use App\Services\Reset\ResetService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class DatabaseController extends Controller {

    public function index()
    {
        return view('database.index');
    }

    public function reset()
    {
        ResetService::resetDatabase();
        Session::flash('flash_message', __('Base de données réinitialisée avec succès (truncate)'));
        return redirect()->route('database.index');
    }

    public function importFromCsv(Request $request)
    {
        if ($request->hasFile('csv_file') && $request->file('csv_file')->isValid()) {
            $file = $request->file('csv_file');
            $filePath = $file->storeAs('csv', 'data.csv', 'local');
    
            $result = UploadService::importFromCsv(storage_path("app/csv/data.csv"));
    
            Session::flash('success', 'Importation réussie');
            Session::flash('import_message', $result);
        } else {
            Session::flash('error', 'Erreur lors de l\'importation du fichier CSV.');
        }
    
        return redirect()->route('database.index');
    }

}