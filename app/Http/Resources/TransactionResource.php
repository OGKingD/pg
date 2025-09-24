<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Transaction */
class TransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'invoice_no' => $this->invoice_no,
            'merchant_transaction_ref' => $this->merchant_transaction_ref,
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status,
            'flag' => $this->flag,
            'redirect_url' => $this->redirect_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
