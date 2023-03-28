<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use Illuminate\Http\Request;

class RequestLogsController extends Controller
{
    public function index()
    {
        $perPage = \request('perpage');
        $perPage = ($perPage > 7000 ? 100: $perPage) ?? 20;
        $criteria = array_filter(\request()->query());

        $result = RequestLog::criteria($criteria);
        $requestlogs = $result->paginate($perPage);
        $transactionCount = $requestlogs->total();

        return view('admin.requestlogs', compact("requestlogs",'perPage','transactionCount'));
    }



}
