<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function data(Request $request)
    {
        
        $perPage = $request->query('per_page', 10);

        return response()->json(Payment::paginate($perPage));
    }

    public function nbdata()
    {
        return response()->json([
            "nb_payments" => Payment::count()
        ]);
    }



    public function monthlyRevenueChart()
    {
        $currentDate = Carbon::now();
    
        $revenueData = [];   
        for ($i = 0; $i < 12; $i++) {
            
            $monthStart = $currentDate->copy()->startOfMonth()->toDateString();  
            $monthEnd = $currentDate->copy()->endOfMonth()->toDateString();

    
            // return response()->json($monthStart);
            $monthKey =$currentDate->copy()->startOfMonth()->format('F Y');  
    
            
            $revenue = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
                            ->sum('amount');   
    
             
            $revenueData[$monthKey] = $revenue;
    
             
            $currentDate->subMonth();
        }
    
        $revenueData = array_reverse($revenueData);
        return response()->json($revenueData);
    }
    

}
