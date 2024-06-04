<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function get_all_invoice(){
        $invoices = Invoice::with('customer')->orderBy('id', 'DESC')->get();
        return $invoices;
    }
    public function search_invoice(Request $request){
        $search_invoice = $request->get('s');
        if ($search_invoice != null){
            $invoice = Invoice::with('customer')
                ->where('id', 'like', "%{$search_invoice}%")
                ->get();
            return $invoice;
        }
        else {
            return $this->get_all_invoice();
        }

    }
}
