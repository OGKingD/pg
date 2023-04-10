<?php

namespace App\Http\Livewire;

use App\Jobs\GenerateCsvReport;
use App\Models\Gateway;
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
    public $searchParameters;
    public $emailToSearch;
    public $searchQuery = [];
    public $merchant_transaction_ref;
    public $payment_status;

    public $payment_flag;

    public $payment_channel;

    public $payment_created_at;

    public $payment_end_date;
    public $gateways;

    protected $listeners = ["exportCsv", "downloadReport", "searchTransactions"];

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->gateways = Gateway::select(['name','id'])->get();

    }

    public function render()
    {
        //check if is admin return admin layout else return default;
        $this->user = auth()->user();
        $userId = $this->user->id;
        $isAdmin = $this->user->type < 5;
        $data['isAdmin'] = $isAdmin;

        /** @var Transaction $builder */
        //check if report exists for download;
        $filename = "{$this->user->first_name}_{$this->user->id}_Transaction Report.csv";
        $reportExists = file_exists(storage_path("logs/$filename"));
        $data['reportDownloadLink'] = "download/$filename/logs";
        if ($reportExists) {
            $data['filename'] = $filename;
            $data['reportExists'] = true;
        }


        //get all transaction pagingated with simplePaginate;
        $perPage = 10;


        //check if it is from summary report;
        if (array_key_exists("group_by", $this->searchQuery)) {
            $this->searchQuery = [];
        }
        if (!$isAdmin) {
            //add user_id;
            $this->searchQuery['user_id'] = $userId;
            $builder = Transaction::reportQuery($this->searchQuery);
            $layout = 'layouts.merchant_dashboardapp';

        }
        if ($isAdmin) {
            $builder = Transaction::reportQuery($this->searchQuery);
            $layout = 'layouts.admin.admin_dashboardapp';
        }

        $data['transactionsCollection'] = $builder->latest()->paginate($perPage);
        $data['transactionCount'] = $data['transactionsCollection']->total();
        $this->dispatchBrowserEvent("searchComplete");

        $this->transactions = $data['transactionsCollection']->items();

        $view = view('livewire.transactions-page', $data)->extends($layout, ["title" => "Transactions "]);
        return $view;
    }

    /**
     * @throws \JsonException
     */
    public function searchTransactions()
    {
        $searchParams = json_decode($this->searchParameters, true, 512, JSON_THROW_ON_ERROR);
        //check if key exists group_by;
        if (!array_key_exists("group_by", $searchParams)) {
            //build array for search;
            $query = array_filter($searchParams);
            unset($query['_token'], $query['customer_email']);
            $this->merchant_transaction_ref = $query['merchant_transaction_ref'] ?? null;
            $this->payment_status = $query['status'] ?? null;
            $this->payment_flag = $query['flag'] ?? null;
            $this->payment_channel = $query['gateway_id'] ?? null;
            $this->payment_created_at = $query['created_at'] ?? null;
            $this->payment_end_date = $query['end_date'] ?? null;
            $this->searchQuery = $query;
        }

        // Transaction Summary;
        if (array_key_exists("group_by", $searchParams)) {
            $this->summaryTransactions();
        }


    }

    /**
     * @throws \JsonException
     */
    public function summaryTransactions(): void
    {
        //build array for search;
        $query = array_filter(json_decode($this->searchParameters, true, 512, JSON_THROW_ON_ERROR));
        unset($query['_token']);
        if (!isset($query['group_by'])) {
            $query['group_by'] = true;
        }

        //group by user_id, status, flag,gateway_id
        $this->searchQuery = $query;

        $this->exportCsv($query);


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
