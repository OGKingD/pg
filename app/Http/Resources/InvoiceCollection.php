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
            "merchant_transaction_ref" => $this->merchant_transaction_ref,
            "invoice_no" => $this->invoice_no,
            "amount" => $this->amount,
            "fee" => $this->fee,
            "total" => $this->total,
            "description" => $this->description,
            "status" => $this->status,
            "channel" => $this->gateway->name,
            "flag" => $this->flag,
            "currency" => $this->currency,
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

        $redirect_url = $request->input("redirect_url");
        if ($redirect_url) {
            $queryString = http_build_query(["redirect_url" => $redirect_url]);
            $url = config("app.url") . "/payment/process/$this->invoice_no?$queryString";
        }
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
