<?php

namespace App\Http\Livewire;

use App\Models\Gateway;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;


class TransactionSummary extends Component
{

    public $builder;
    public $startDate;
    public $pollingTime = "300s";
    /**
     * @var mixed
     */
    private $data;
    /**
     * @var mixed
     */
    private $transactions = [];
    /**
     * @var int|mixed
     */
    private $transactionCount;
    protected $listeners = ['summarizeTransactions'];

    public function render()
    {


        return view('livewire.transaction-summary', ['transactions' => $this->transactions,'transactionCount' => $this->transactionCount ])->extends('layouts.app', ["title" => "Transaction Summary "]);
    }


    /**
     * @param  $gateways
     * @param $builder
     * @return array
     */
    private function getSummaryQuery($gateways, $builder)
    {
        $csvHeaders = [];
        return (new Transaction)->summaryQueryWithGateways($gateways,$csvHeaders,$builder);
    }

    public function mount()
    {
        $this->summarizeTransactions();

    }

    public function summarizeTransactions()
    {
        $this->dispatchBrowserEvent('generatingReport');
        $this->getData();
        $this->dispatchBrowserEvent('searchComplete');

    }



    protected function getData(): void
    {
        if (empty($this->startDate)) {
            $this->startDate = Carbon::now()->format('Y-m-d');
        }
        $queryBlock = Transaction::where("type", "!=", "Wallet")->whereNotNull('gateway_id')->
        whereBetween("updated_at", [$this->startDate, $this->startDate . " 23:59:59.999",]);
        $this->transactionCount = $queryBlock->count('id');

        $sql = $queryBlock->with(['user', 'gateway'])->select(['user_id', 'type', 'gateway_id']);

        /** @var Gateway $gateways */
        $gateways = Gateway::select(['name', 'id'])->get();


        /** @var Builder $builder */
        $builder = $this->getSummaryQuery($gateways, $sql)[1];
        $this->transactions = $builder->groupBy('user_id')->groupBy('type')->groupBy('gateway_id')->orderBy('user_id')->paginate(50);
    }


}
