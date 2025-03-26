<?php

namespace App\Http\Controllers;

use App\Services\Upload\UploadService;
use App\Services\Import\ImportService;
use App\Services\Import\RepartitionService;
use App\Services\Reset\ResetService;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends Controller {

    private $importService;
    private $repartitionSevice;

    public function __construct(ImportService $importService,RepartitionService $repartitionSevice)
    {
        $this->importService = $importService;
        $this->repartitionSevice = $repartitionSevice;
    }

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

    public function generate(Request $request)
    {
         
        $data = $request->input('tables', []);
 
        foreach ($data as $table => $count) {
             
            if ($count > 0) { 
                for ($i = 0; $i < $count; $i++) {
                    Artisan::call('db:seed', [
                        '--class' => ucfirst(camel_case($table)) . 'DummyTableSeeder'
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Les données ont été générées avec succès !');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'file2' => 'required|file|mimes:csv,txt',
            'file3' => 'required|file|mimes:csv,txt'
        ]);

        $fileNames = [
            'file' => "CSV 1 : ".$request->file('file')->getClientOriginalName(),
            'file2' => "CSV 2 : ".$request->file('file2')->getClientOriginalName(),
            'file3' => "CSV 3 : ".$request->file('file3')->getClientOriginalName()
        ];

        // Démarrer une transaction pour toutes les opérations
        $this->importService->clearAllTempData();
        $allErrors = [];
        $results = [];

        // Importer les projets
        $projectImportResult = $this->importService->importProjects($request->file('file'));
        if ($projectImportResult['error']) {
            $this->addFileSourceToErrors($projectImportResult['errors'], $fileNames['file']);
            $allErrors = array_merge($allErrors, $projectImportResult['errors']);
        } else {
            $results['projects'] = $projectImportResult['data'];
            $results['imported_projects_rows'] = $projectImportResult['imported_rows'];
        }

        // Importer les tâches
        $taskImportResult = $this->importService->importProjectTasks($request->file('file2'));
        if ($taskImportResult['error']) {
            $this->addFileSourceToErrors($taskImportResult['errors'], $fileNames['file2']);
            $allErrors = array_merge($allErrors, $taskImportResult['errors']);
        } else {
            $results['project_tasks'] = $taskImportResult['data'];
            $results['imported_project_tasks_rows'] = $taskImportResult['imported_rows'];
        }

        // Importer les offres
        $offerImportResult = $this->importService->importOffers($request->file('file3'));
        if ($offerImportResult['error']) {
            $this->addFileSourceToErrors($offerImportResult['errors'], $fileNames['file3']);
            $allErrors = array_merge($allErrors, $offerImportResult['errors']);
        } else {
            $results['offers'] = $offerImportResult['data'];
            $results['imported_offers_rows'] = $offerImportResult['imported_rows'];
        }

        // Si erreurs, tout supprimer
        if (!empty($allErrors)) {
            $this->importService->clearAllTempData();
            return back()->with([
                'error' => 'Des erreurs sont survenues lors de l\'importation',
                'import_errors' => $allErrors,
                'file_name' => $fileNames['file'],
                'file_name2' => $fileNames['file2'],
                'file_name3' => $fileNames['file3'],
                'skipped_rows' => count($allErrors)
            ]);
        }

        $this->repartitionSevice->repartitionTempProject();
        $this->repartitionSevice->repartitionTempProjectTask();
        $this->repartitionSevice->repartitionTempOffer();
        
        return back()->with(array_merge([
            'success' => 'Importation réussie',
            'file_name' => $fileNames['file'],
            'file_name2' => $fileNames['file2'],
            'file_name3' => $fileNames['file3']
        ], $results));
    }

    private function addFileSourceToErrors(&$errors, $fileName)
    {
        foreach ($errors as &$error) {
            $error['source_file'] = $fileName;
        }
    }

}