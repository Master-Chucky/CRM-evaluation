<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\Invoice\InvoiceCalculator;

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

    public function sumpayment()
    {
        return response()->json([
            "sum_payments" =>doubleval( Payment::sum("amount"))/100
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

    public function updateAmount(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0', 
        ]);
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found'
            ], 404);
        }
        $invoice=Invoice::find($payment->invoice_id);
        $invoiceCalculator = new InvoiceCalculator($invoice);
        $totalPrice = $invoiceCalculator->getTotalPrice();
        $subPrice = $invoiceCalculator->getSubTotal();
        $vatPrice = $invoiceCalculator->getVatTotal();
        $amountDue = $invoiceCalculator->getAmountDue();
        

        $payment->amount = $validated['amount'] * 100; 
        // $payment->updated_at=Carbon::now();
        $payment->update();

        return response()->json([
            'message' => 'Payment amount updated successfully',
            'payment' => $payment
        ]);
    }


    public function deletePayment(Request $request, $id)
    {
        
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'message' => 'Payment not found'
            ], 404);
        }
        
        $payment->delete();
        

        return response()->json([
            'message' => 'Payment amount deleted successfully'
        ]);
    }
    

}
