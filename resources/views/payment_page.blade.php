@include('partials.admin.admin_header')

<body>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">


    <div class="container-fluid py-3 col-lg-8 mx-auto">
        <div class="card">
            @livewire('payment-page', ['invoice' => $invoice, 'merchantGateways' => $merchantGateways, 'activeTab' => $activeTab])
        </div>

    </div>


    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')
    @include('partials.card_gateway')

</main>

</body>



