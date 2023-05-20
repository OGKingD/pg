<?php

namespace App\Http\Livewire;

use App\Models\RequestLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class RequestLogs extends Component
{

    use WithPagination;
    public $request_id;
    public $startdate;
    public $perPage;
    public $requestlogs;

    public $total;
    public $currentPage;
    public $transactionCount;
    private $enddate;



    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        if (isset($this->request_id)){
            if (strpos($this->request_id, "INV") === 0){
                $this->request_id = substr($this->request_id,3,);
            }
        }

        $this->perPage = ($this->perPage > 7000 ? 100: $this->perPage) ?? 20;
        $criteria = [
            "start_date" => $this->startdate,
            'end_date' => $this->enddate,
            'request_id' => $this->request_id
        ];
        $collection = RequestLog::criteria($criteria)->latest()->paginate($this->perPage);
        $this->requestlogs = $collection->items();
        $this->transactionCount = $collection->total();

        return view('livewire.request-logs',[
            'transactionCount' => $this->transactionCount,
            'requestlogs' => $this->requestlogs,
            'requestlogsCollection' => $collection,
            'enddate' => $this->enddate,
            'startdate' => $this->startdate,
        ])->extends('layouts.admin.admin_dashboardapp', ['title' => 'Request Logs']);

    }

}
