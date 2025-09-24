<?php

namespace App\Http\Requests;

use App\Traits\AuthorizeApi;
use Illuminate\Foundation\Http\FormRequest;

class TransactionsReportsRequest extends FormRequest
{
    use AuthorizeApi;
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after:start_date'],
        ];
    }


}
