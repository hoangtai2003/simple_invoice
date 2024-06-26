<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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

    public function create_invoice(){
        $counter = Counter::where('key', 'invoice')->first();
        $invoice = Invoice::orderBy('id', 'DESC')->first();

        if ($invoice){
            $invoice = $invoice->id + 1;
            $counters = $counter->value + $invoice;
        } else {
            $counters = $counter->value;
        }

        $formData = [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'term_and_conditions' => 'Default Terms and Conditions',
            'items' => [
                'product_id' => null,
                'product' => null,
                'unit_price' => 0,
                'quantity' => 1
            ]
        ];
        return $formData;
    }

    public function add_invoice(Request $request){
        $invoiceItem = $request->input("invoice_item");

        $invoice = Invoice::create([
            'sub_total' => $request->input("subtotal"),
            'total' => $request->input('total'),
            'customer_id' => $request->input("customer_id"),
            'number' => $request->input("number"),
            'date' =>  $request->input("date"),
            'due_date' => $request->input("due_date"),
            'discount' => $request->input("discount"),
            'reference' => $request->input('reference'),
            'terms_and_conditions' => $request->input("terms_and_conditions")
        ]);
        foreach ($invoiceItem as $item){
            $itemData = [
                'product_id' => $item['id'],
                'invoice_id' => $invoice->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ];

            InvoiceItem::create($itemData);
        }
    }

    public function show_invoice($id)
    {
        $invoice = Invoice::with(['customer', 'invoice_items.product'])->find($id);
        return response()->json($invoice);
    }

    public function delete_invoice_items(InvoiceItem $invoiceitem){
        $invoiceitem->delete();
    }

    public function update_invoice(Request $request, $id) {
        $invoice = Invoice::where('id', $id)->first();

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $invoice->sub_total = $request->input("subtotal");
        $invoice->total = $request->input("total");
        $invoice->customer_id = $request->input("customer_id");
        $invoice->number = $request->input("number");
        $invoice->date = $request->input("date");
        $invoice->due_date = $request->input("due_date");
        $invoice->discount = $request->input("discount");
        $invoice->terms_and_conditions = $request->input("terms_and_conditions");
        $invoice->update($request->all());

        $invoiceItemsData = $request->input("invoice_item");
        $invoice->invoice_items()->delete();
        foreach ($invoiceItemsData as $item) {
            $itemData = [
                'invoice_id' => $invoice->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price']
            ];
            InvoiceItem::create($itemData);
        }
        return response()->json(['message' => 'Invoice updated successfully']);
    }

}
