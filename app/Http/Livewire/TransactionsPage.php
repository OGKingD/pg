<?php

namespace App\Http\Livewire;

use App\Jobs\GenerateCsvReport;
use App\Models\Gateway;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsPage extends Component
{
    public $user;
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
    public $data;
    public $layout;
    private $builder;
    public $isAdmin;
    public $userId;

    protected $listeners = ["exportCsv", "downloadReport", "searchTransactions"];

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {

        if (!$this->isAdmin) {
            //add user_id;
            $this->searchQuery['user_id'] = $this->userId;
            $this->builder = Transaction::reportQuery($this->searchQuery);
            $this->layout = 'layouts.merchant_dashboardapp';

        }
        if ($this->isAdmin) {
            $this->builder = Transaction::reportQuery($this->searchQuery);
            $this->layout = 'layouts.admin.admin_dashboardapp';
        }
        $this->dispatchBrowserEvent("searchComplete");
        $this->data['transactionCount'] = $this->builder->count();
        $this->data['transactions'] =$this->builder->paginate(10);

        return view('livewire.transactions-page', $this->data)->extends($this->layout, ["title" => "Transactions "]);


    }

    public function mount()
    {
        //check if is admin return admin layout else return default;
        $this->gateways = Gateway::select(['name','id'])->get();
        $this->user = auth()->user();
        $this->userId = $this->user->id;
        $this->isAdmin = $this->user->type < 5;
        $data['isAdmin'] = $this->isAdmin;

        /** @var Transaction $builder */
        //check if report exists for download;
        $filename = "{$this->user->first_name}_{$this->user->id}_Transaction Report.csv";
        $filename2 = "Summary_Report_{$this->user->id}.csv";
        $reportExists = file_exists(storage_path("logs/$filename"));
        $summaryReportExists = file_exists(storage_path("logs/$filename2"));
        if ($reportExists) {
            $data['filename'] = $filename;
            $data['reportExists'] = true;
            $data['reportDownloadLink'] = "download/$filename/logs";

        }
        if ($summaryReportExists) {
            $data['summary_filename'] = $filename2;
            $data['summaryReportExists'] = true;
            $data['summaryReportDownloadLink'] = "download/$filename2/logs";

        }


        //get all transaction pagingated with simplePaginate;


        //check if it is from summary report;
        if (array_key_exists("group_by", $this->searchQuery)) {
            $this->searchQuery = [];
        }
        $this->data = $data;

    }

    /**
     * @throws \JsonException
     */
    public function searchTransactions()
    {
        $searchParams = json_decode($this->searchParameters, true, 512, JSON_THROW_ON_ERROR);
        //check if key exists group_by;
        if (empty($searchParams['group_by'])) {
            //build array for search;
            $query = array_filter($searchParams);
            if (isset($query['merchant_transaction_ref'])){
                if (strpos($query['merchant_transaction_ref'], "INV") === 0){
                    $query['merchant_transaction_ref'] = substr($query['merchant_transaction_ref'],3,);
                }
            }
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
        if (!empty($searchParams['group_by'])) {
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

        //group by user_id, status, flag,gateway_id
        $this->exportCsv($query);


    }

    /**
     * @throws \JsonException
     */
    public function exportCsv($queryString): void
    {

        //collect Query and pass to Job to generate CSV Report;
        $csvHeader = ["Merchant Ref", "Gateway", 'Amount', 'Fee', 'Total', 'Description', 'Status', 'Flag', 'Date'];

        GenerateCsvReport::dispatch(Transaction::class, $queryString, $csvHeader, $this->user);

        $this->dispatchBrowserEvent("generatingReport", ["status" => true]);

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
