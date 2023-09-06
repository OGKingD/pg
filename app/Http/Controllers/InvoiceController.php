<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $data['title'] = "Invoice";
        return view('invoice.index', $data);

    }

    public function notAvailable(Request $request)
    {
        return view('invoice.notavailable');

    }

    //
}
