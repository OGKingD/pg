<?php

namespace App\Http\Controllers;

use App\Models\Gateway;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        //
        $data['title'] = "Payment Gateways";

        return view('admin.gateways.index', $data);
    }

    public function paymentPage($id)
    {
        list($data, $invoice) = $this->checkifInvoiceExist($id);

        $data['title']  = "Payment Page";

        //only show payment page when invoice is pending
        if ($invoice->status !== "pending"){
            //redirect to payment page;
            return redirect()->route('receipt',['id' => $id])->with('status','Invoice Paid!');
        }


        return view('payment_page',$data);

    }

    public function receipt($id)
    {
        list($data, $invoice) = $this->checkifInvoiceExist($id);

        $data['title']  = "Payment Receipt";

        if ($invoice->status === 'pending'){
            abort(403);
        }
        $data['invoice'] = $invoice;


        return view('payment_receipt',$data);



    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param $id
     * @return array
     */
    public function checkifInvoiceExist($id): array
    {
        //check if Invoice exists;
        $invoice = Invoice::where('invoice_no', $id)->first();
        $data['invoice'] = $invoice;

        if (!$invoice) {
            abort(404);
        }
        return array($data, $invoice);
    }


}
