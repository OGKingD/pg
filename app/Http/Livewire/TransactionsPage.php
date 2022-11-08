<?php

namespace App\Http\Livewire;

use App\Jobs\GenerateCsvReport;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsPage extends Component
{
    public $user;
    /**
     * @var object|null
     */
    public $transactions;
    /**
     * @var mixed
     */
    public $searchTransactions;
    protected $listeners = ["exportCsv", "downloadReport"];

    use WithPagination;

    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        //check if is admin return admin layout else return default;
        $this->user = auth()->user();
        $userId = $this->user->id;
        $isAdmin = $this->user->type < 5;
        $columns_to_select = [
            "transaction_ref",
            'merchant_transaction_ref',
            'gateway_id',
            'amount',
            'fee',
            'total',
            'description',
            'status',
            'flag',
            'created_at'];

        /** @var Transaction $builder */
        $builder = "";
        $table = Transaction::with(['invoice', 'gateway', 'user']);
        //check if report exists for download;
        $filename = "{$this->user->first_name}_{$this->user->id}_Transaction Report.csv";
        $reportExists = file_exists(storage_path("logs/$filename"));
        if ($reportExists) {
            $data['filename'] = $filename;
            $data['reportExists'] = true;
        }

        if (!$isAdmin) {
            $builder = $table->where('user_id', '=', $userId)->select($columns_to_select);
            $layout = 'layouts.merchant_dashboardapp';

        }
        if ($isAdmin) {
            $builder = $table->select($columns_to_select);
            $layout = 'layouts.admin.admin_dashboardapp';
        }


        //get all transaction pagingated with simplePaginate;
        $perPage = 10;

        /** @var Paginator $transaction */
        $data['transactionsCollection'] = !$this->searchTransactions ? $builder->paginate($perPage) : $builder->where('email', 'like', "{$this->searchTransactions}%")->paginate($perPage);

        $this->transactions = $data['transactionsCollection']->items();

        $view = view('livewire.transactions-page', $data)->extends($layout, ["title" => "Transactions "]);
        return $view;
    }

    /**
     * @throws \JsonException
     */
    public function exportCsv($queryString): void
    {
        $status = false;
        /** @var User $user */
        $user = auth()->user();

        if (isset($this->transactions)) {
            //collect Query and pass to Job to generate CSV Report;
            $csvHeader = ["Merchant Ref", "Gateway", 'Amount', 'Fee', 'Total', 'Description', 'Status', 'Flag', 'Date'];
            info("search parameters for report generation :", $queryString);
            GenerateCsvReport::dispatch(Transaction::class, $queryString, $csvHeader, $user);
            $status = true;
        }
        $this->dispatchBrowserEvent("generatingReport", ["status" => $status]);

    }

    public function downloadReport($filename, $path)
    {
        if (is_null($path)) {
            $path = "logs";
        }

        $file = storage_path("/$path/$filename");
        if (file_exists($file)) {
            return response()->download($file)->deleteFileAfterSend(true);
        }
        return redirect()->back()->with("status", "Oops! Could not Download!");

    }
}
