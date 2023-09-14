@include('partials.admin.admin_header')
<style>
    .nav.nav-pills .nav-link.active {
        animation: .2s ease;
        background-color: #143cb4;
        color: white;
    }

    /*//Form for card modal*/

    /*body {*/
    /*    overflow-x: hidden;*/
    /*    background: #000000*/
    /*}*/

    /*End of form payment modal */

    .payment-title {
        width: 100%;
        text-align: center;
    }

    .form-container .field-container:first-of-type {
        grid-area: name;
    }

    .form-container .field-container:nth-of-type(2) {
        grid-area: number;
    }

    .form-container .field-container:nth-of-type(3) {
        grid-area: expiration;
    }

    .form-container .field-container:nth-of-type(4) {
        grid-area: security;
    }

    .field-container input {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    .field-container {
        position: relative;
    }

    .form-container {
        display: grid;
        grid-column-gap: 10px;
        grid-template-columns: auto auto;
        grid-template-rows: 90px 90px 90px;
        grid-template-areas: "name name""number number""expiration security";
        max-width: 400px;
        padding: 20px;
        color: #707070;
    }


    .ccicon {
        height: 38px;
        position: absolute;
        right: 1px;
        top: calc(50% - 4px);
        width: 50px;
    }

    /* CREDIT CARD IMAGE STYLING */
    .preload * {
        -webkit-transition: none !important;
        -moz-transition: none !important;
        -ms-transition: none !important;
        -o-transition: none !important;
    }


    #ccsingle {
        position: absolute;
        right: 15px;
        top: 20px;
    }

    #ccsingle svg {
        width: 100px;
        max-height: 60px;
    }

    .creditcard svg#cardfront,
    .creditcard svg#cardback {
        width: 100%;
        -webkit-box-shadow: 1px 5px 6px 0px black;
        box-shadow: 1px 5px 6px 0px black;
        border-radius: 22px;
    }

    #generatecard {
        cursor: pointer;
        float: right;
        font-size: 12px;
        color: #fff;
        padding: 2px 4px;
        background-color: #909090;
        border-radius: 4px;
    }

    /* CHANGEABLE CARD ELEMENTS */
    .creditcard .lightcolor,
    .creditcard .darkcolor {
        -webkit-transition: fill .5s;
        transition: fill .5s;
    }

    .creditcard .lightblue {
        fill: #03A9F4;
    }

    .creditcard .lightbluedark {
        fill: #0288D1;
    }

    .creditcard .red {
        fill: #ef5350;
    }

    .creditcard .reddark {
        fill: #d32f2f;
    }

    .creditcard .purple {
        fill: #ab47bc;
    }

    .creditcard .purpledark {
        fill: #7b1fa2;
    }

    .creditcard .cyan {
        fill: #26c6da;
    }

    .creditcard .cyandark {
        fill: #0097a7;
    }

    .creditcard .green {
        fill: #66bb6a;
    }

    .creditcard .greendark {
        fill: #388e3c;
    }

    .creditcard .lime {
        fill: #d4e157;
    }

    .creditcard .limedark {
        fill: #afb42b;
    }

    .creditcard .yellow {
        fill: #ffeb3b;
    }

    .creditcard .yellowdark {
        fill: #f9a825;
    }

    .creditcard .orange {
        fill: #ff9800;
    }

    .creditcard .orangedark {
        fill: #ef6c00;
    }

    .creditcard .grey {
        fill: #bdbdbd;
    }

    .creditcard .greydark {
        fill: #616161;
    }

    /* FRONT OF CARD */
    #svgname {
        text-transform: uppercase;
    }

    #cardfront .st2 {
        fill: #FFFFFF;
    }

    #cardfront .st3 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 600;
    }

    #cardfront .st4 {
        font-size: 52.7817px;
    }

    #cardfront .st5 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 400;
    }

    #cardfront .st6 {
        font-size: 33.1112px;
    }

    #cardfront .st7 {
        opacity: 0.6;
        fill: #FFFFFF;
    }

    #cardfront .st8 {
        font-size: 24px;
    }

    #cardfront .st9 {
        font-size: 36.5498px;
    }

    #cardfront .st10 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 300;
    }

    #cardfront .st11 {
        font-size: 16.1716px;
    }

    #cardfront .st12 {
        fill: #4C4C4C;
    }

    /* BACK OF CARD */
    #cardback .st0 {
        fill: none;
        stroke: #0F0F0F;
        stroke-miterlimit: 10;
    }

    #cardback .st2 {
        fill: #111111;
    }

    #cardback .st3 {
        fill: #F2F2F2;
    }

    #cardback .st4 {
        fill: #D8D2DB;
    }

    #cardback .st5 {
        fill: #C4C4C4;
    }

    #cardback .st6 {
        font-family: 'Source Code Pro', monospace;
        font-weight: 400;
    }

    #cardback .st7 {
        font-size: 27px;
    }

    #cardback .st8 {
        opacity: 0.6;
    }

    #cardback .st9 {
        fill: #FFFFFF;
    }

    #cardback .st10 {
        font-size: 24px;
    }

    #cardback .st11 {
        fill: #EAEAEA;
    }

    #cardback .st12 {
        font-family: 'Rock Salt', cursive;
    }

    #cardback .st13 {
        font-size: 37.769px;
    }


    .creditcard {
        width: 95%;
        -webkit-transform-style: preserve-3d;
        transform-style: preserve-3d;
        transition: -webkit-transform 0.6s;
        -webkit-transition: -webkit-transform 0.6s;
        transition: transform 0.6s, -webkit-transform 0.6s;
        cursor: pointer;
    }

    .creditcard .front,
    .creditcard .back {
        position: absolute;
        width: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-font-smoothing: antialiased;
        color: #47525d;
    }

    .creditcard .back {
        -webkit-transform: rotateY(180deg);
        transform: rotateY(180deg);
    }

    .creditcard.flipped {
        -webkit-transform: rotateY(180deg);
        transform: rotateY(180deg);
    }

</style>

<body>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">


    <div class="container-fluid py-3 col-lg-9 mx-auto">
        <div class="card">
            @livewire('payment-page', ['invoice' => $invoice, 'merchantGateways' => $merchantGateways, 'activeTab' => $activeTab])
        </div>

    </div>


    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')
    @if(isset($merchantGateways['card']))
        @include('partials.card_gateway')
    @endif

</main>
<script>
    Swal.fire({
        title: 'Loading Payment Gateways ! Please wait!',
        html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
        allowEscapeKey: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEnterKey: false,
    });
</script>
</body>



