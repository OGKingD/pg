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
        $transaction = $this->model;
        $filename = "{$this->user->first_name}_{$this->user->id}_Transaction Report.csv";
        $this->payload["filename"] = $filename;
        if (!$this->user->isAdmin()) {
            $this->payload['user_id'] = $this->user->id;
        }

        $transaction::generateCsvReport($this->payload, $this->csvHeaders);
        //send Mail insert to Notification DB;

    }
}
