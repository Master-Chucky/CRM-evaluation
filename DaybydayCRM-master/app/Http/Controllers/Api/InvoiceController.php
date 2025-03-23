<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function data(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        return response()->json(Invoice::paginate($perPage));
    }

    public function nbdata()
    {
        return response()->json([
            "nb_invoices" => Invoice::count()
        ]);
    }
}
