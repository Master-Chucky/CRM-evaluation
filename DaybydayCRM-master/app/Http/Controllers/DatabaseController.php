<?php

namespace App\Http\Controllers;

use App\Services\Reset\ResetService;
use Illuminate\Support\Facades\Session;

class DatabaseController extends Controller {

    public function index()
    {
        return view('database.index');
    }

    public function resetWithTruncate()
    {
        ResetService::resetDatabase();
        Session::flash('flash_message', __('Base de données réinitialisée avec succès (truncate)'));
        return redirect()->route('database.index');
    }

}