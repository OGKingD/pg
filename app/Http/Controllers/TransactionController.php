<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionsReportsRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return view('livewire.transactions-page');

    }
    //

    public function fetchReports(TransactionsReportsRequest $request): array
    {
        $startDate  = $request->get('start_date');
        $endDate    = $request->get('end_date');
        $userId = $request->user()->id;

        $perPage = min((int) $request->get('per_page', 30), 50); // default 30, max 50

        $transactions = Transaction::select([
            'invoice_no',
            'merchant_transaction_ref',
            'type',
            'amount',
            'status',
            'flag',
            'redirect_url',
            'created_at',
            'updated_at'
        ])
            ->whereNotNull('invoice_no')
            ->where('user_id', $userId)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('id')
            ->paginate($perPage);

        return [
            'success' => true,
            'transactions' => TransactionResource::collection($transactions->items()),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ];
    }
}
