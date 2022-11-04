<?php

namespace App\Http\Livewire;

use App\Models\Transaction;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsPage extends Component
{
    public $user;

    /**
     * @var object|null
     */
    public $transactions;
    /**
     * @var mixed
     */
    public $searchTransactions;

    use WithPagination;

    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        //check if is admin return admin layout else return default;
        $this->user = auth()->user();
        $userId = $this->user->id;
        $isAdmin = $this->user->type < 5;
        $columns_to_select = [
            "transaction_ref",
            'merchant_transaction_ref',
            'gateway_id',
            'amount',
            'fee',
            'total',
            'description',
            'status',
            'flag',
            'created_at'];

        /** @var Transaction $builder */
        $builder = "";
        $table = Transaction::with(['invoice', 'gateway']);

        if (!$isAdmin) {
            $builder = $table->where('user_id', '=', $userId)->select($columns_to_select);
            $layout = 'layouts.merchant_dashboardapp';

        }
        if ($isAdmin) {
            $builder = $table->select($columns_to_select);
            $layout = 'layouts.admin.admin_dashboardapp';
        }


        //get all transaction pagingated with simplePaginate;
        $perPage = 10;

        /** @var Paginator $transaction */
        $data['transactionsCollection'] = !$this->searchTransactions ? $builder->paginate($perPage) : $builder->where('email', 'like', "{$this->searchTransactions}%")->paginate($perPage);

        $this->transactions = $data['transactionsCollection']->items();

        $view = view('livewire.transactions-page', $data)->extends($layout, ["title" => "Transactions "]);
        return $view;
    }
}
