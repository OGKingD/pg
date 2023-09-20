<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCsvReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;
    private $payload;
    private $csvHeaders;
    private $user;

    /**
     * Create a new job instance.
     *
     * @param $model
     * @param $payload
     * @param $csvHeaders
     * @param User $user
     */
    public function __construct($model, $payload, $csvHeaders, $user)
    {
        //
        $this->model = $model;
        $this->payload = $payload;
        $this->csvHeaders = $csvHeaders;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //call the model Report Method;
        /** @var Transaction $transaction */
        $transaction = new $this->model;
        $filename = "{$this->user->first_name}_{$this->user->id}_Transaction Report.csv";
        $this->payload["filename"] = $filename;
        if (!$this->user->isAdmin()) {
            $this->payload['user_id'] = $this->user->id;
        }
        //check if group_by is set and call summary report;
        if (isset($this->payload['group_by'])) {
            $this->payload["filename"] = "Summary_Report_{$this->user->id}.csv";
            $transaction->summaryReport($this->payload);
        }

        if (!isset($this->payload['group_by'])) {
            //when it's a detailed report
            $headers = ["Merchant Name","Merchant Ref","Status","Channel","Currency","Provider","Type","Fee","Amount","Total","Customer Name", "Customer Email","Flag", "Date" ];
            $transaction->generateCsvReport($this->payload, $headers);
        }
        //send Mail insert to Notification DB;

    }
}
