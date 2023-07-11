<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        if ($request->get('paymentRequest')) {
            return $this->paymentRequest($request);
        }
        return [
            "merchant_transaction_ref" => $this->resource->transaction->merchant_transaction_ref,
            "invoice_no" => $this->invoice_no,
            "amount" => $this->amount,
            "fee" => $this->resource->transaction->fee,
            "total" => $this->resource->transaction->total,
            "description" => $this->resource->transaction->description,
            "status" => $this->status,
            "channel" => $this->resource->transaction->gateway->name ?? "N/A",
            "flag" => $this->resource->transaction->flag,
            "currency" => $this->resource->transaction->currency,
            "date" => $this->updated_at,
        ];

    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function paymentRequest(Request $request): array
    {
        $url = config("app.url") . "/payment/process/$this->invoice_no";

        return [

            "invoice_no" => $this->invoice_no,
            "quantity" => $this->quantity,
            "customer_email" => $this->customer_email,
            "amount" => $this->amount,
            "name" => $this->name,
            "created_at" => $this->created_at,
            "url" => $url,

        ];

    }
}
