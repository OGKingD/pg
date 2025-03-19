<?php

namespace App\Traits;

trait AuthorizeApi
{
    public function authorize(): bool
    {
        return (bool)$this->user();
    }



}
