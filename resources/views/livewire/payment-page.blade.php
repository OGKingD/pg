@php
    if ($invoice->transaction->currency === "NGN"){
             $invoice->currency_symbol = '8358';
         }
         if ($invoice->transaction->currency === "USD"){
             $invoice->currency_symbol = '36';
         }
         if ($invoice->transaction->currency === "GBP"){
             $invoice->currency_symbol = '163';
         }
         if ($invoice->transaction->currency === "EUR"){
             $invoice->currency_symbol = 'euro';
         }
@endphp
@if($merchantGateways)
    <div class="row">
        <div class="col-md-3">
            <div class="border-right">

                <div class="d-lg-block d-none text-center ">
                    <a href="/">
                        <img src="{{asset('assets/img/saanapay.png')}} "
                             alt="SAANAPAY BRAND IMAGE" style="max-width: 110px!important;">
                    </a>
                    <h4 class="mt-1">PAY WITH</h4>
                </div>
                <hr>

                <!-- Nav pills -->
                <ul class="nav nav-pills flex-column " role="tablist">


                    @if(isset($merchantGateways['remita']))
                        <li class="nav-item" onclick="setActiveTab('remita')">
                            <a class="nav-link mt-1 @if($activeTab === "remita") active @endif" data-bs-toggle="pill"
                               href="#remita">
                                <i class="fas fa-school ">
                                    &nbsp; Remita
                                </i>
                            </a>
                        </li>

                    @endif


                    @if(isset($merchantGateways['card']))
                        <li onclick="setActiveTab('card') " class="nav-item">
                            <a class="nav-link @if($activeTab === "card") active @endif mt-1 " data-bs-toggle="pill"
                               href="#card">
                                <i class="fa-solid fa-credit-card ">
                                    &nbsp; Card
                                </i>
                            </a>
                        </li>
                    @endif


                    @if(isset($merchantGateways['banktransfer']))
                        <li class="nav-item" onclick="setActiveTab('banktransfer')">
                            <a class="nav-link mt-1 @if($activeTab === "banktransfer") active @endif"
                               data-bs-toggle="pill"
                               href="#banktransfer">
                                <i class="fas fa-landmark ">
                                    &nbsp; Bank Transfer
                                </i>
                            </a>
                        </li>
                    @endif

                    @if(isset($merchantGateways['cashatbank']))
                        <li class="nav-item" onclick="setActiveTab('cashatbank')">
                            <a class="nav-link mt-1 @if($activeTab === "cashatbank") active @endif"
                               data-bs-toggle="pill"
                               href="#cashatbank">
                                <i class="fas fa-landmark ">
                                    &nbsp; Cash At Bank
                                </i>
                            </a>
                        </li>
                    @endif


                    @if(isset($merchantGateways['googlepay']))
                        <li class="nav-item" onclick="setActiveTab('googlepay')">
                            <a class="nav-link mt-1 @if($activeTab === "googlepay") active @endif" data-bs-toggle="pill"
                               href="#googlepay">
                                <i class="fa-brands fa-google-pay">
                                    &nbsp; Google Pay
                                </i>
                            </a>
                        </li>
                    @endif

                    @if(isset($merchantGateways['applepay']))
                        <li class="nav-item" onclick="setActiveTab('applepay')">
                            <a class="nav-link mt-1 @if($activeTab === "applepay") active @endif" data-bs-toggle="pill"
                               href="#applepay">
                                <i class="fa-brands fa-cc-apple-pay ">
                                    &nbsp; Apple Pay
                                </i>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

        </div>

        <div class="col-md-9">
            <!-- Tab panes -->
            <div class="container">
                <ul class="nav mt-4 pb-3 border-bottom">
                    <li class="nav-item">

                    </li>
                    <li class="mx-auto">

                    </li>
                    <li class="nav-item mr-3 text-end">
                            <span>
                            <b class="fa fa-envelope-open-text "
                               style="font-size: 11px; line-height: 3px;"> {{$invoice->customer_email}}</b><br>
                            </span>
                        <span>
                            @if($invoice->transaction->type)
                                <b class="fa fa-file-invoice"
                                   style="font-size: 11px; line-height: 3px;"> {{$invoice->transaction->type}}</b><br>
                            @endif
                        </span>
                        <span class="text-success  text-bold">
                                &#{{$invoice->currency_symbol}};{{number_format($merchantGateways[$activeTab]['invoiceTotal'],2)}}
                    </span>
                    </li>
                </ul>
            </div>

            <!-- Tab panes -->

            <div class="tab-content min-vh-55">

                @if(isset($merchantGateways['remita']))
                    <div id="remita" class="container tab-pane  @if($activeTab === "remita") active @endif"><br>
                        <div class="text-center mt-3">
                            <h3 class="text-secondary font-weight-normal">Pay Using Remita.</h3>
                            @if(empty($remitaDetails) &&  !isset($remitaDetails['status']))
                                <div id="genRRRstep1">
                                    <div class="col-sm-3 mx-auto mt-4 ">
                                        <input type="button" class="btn-check" id="generateAcc">
                                        <label class="btn btn-lg btn-outline-secondary border-2 px-6 py-5"
                                               for="btncheck2"
                                               onclick="generateRRR()">
                                            <svg class="text-dark" width="20px" height="20px" viewBox="0 0 42 42"
                                                 version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <title>box-3d-50</title>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <g transform="translate(-2319.000000, -291.000000)" fill="#FFFFFF"
                                                       fill-rule="nonzero">
                                                        <g transform="translate(1716.000000, 291.000000)">
                                                            <g transform="translate(603.000000, 0.000000)">
                                                                <path class="color-background"
                                                                      d="M22.7597136,19.3090182 L38.8987031,11.2395234 C39.3926816,10.9925342 39.592906,10.3918611 39.3459167,9.89788265 C39.249157,9.70436312 39.0922432,9.5474453 38.8987261,9.45068056 L20.2741875,0.1378125 L20.2741875,0.1378125 C19.905375,-0.04725 19.469625,-0.04725 19.0995,0.1378125 L3.1011696,8.13815822 C2.60720568,8.38517662 2.40701679,8.98586148 2.6540352,9.4798254 C2.75080129,9.67332903 2.90771305,9.83023153 3.10122239,9.9269862 L21.8652864,19.3090182 C22.1468139,19.4497819 22.4781861,19.4497819 22.7597136,19.3090182 Z"></path>
                                                                <path class="color-background"
                                                                      d="M23.625,22.429159 L23.625,39.8805372 C23.625,40.4328219 24.0727153,40.8805372 24.625,40.8805372 C24.7802551,40.8805372 24.9333778,40.8443874 25.0722402,40.7749511 L41.2741875,32.673375 L41.2741875,32.673375 C41.719125,32.4515625 42,31.9974375 42,31.5 L42,14.241659 C42,13.6893742 41.5522847,13.241659 41,13.241659 C40.8447549,13.241659 40.6916418,13.2778041 40.5527864,13.3472318 L24.1777864,21.5347318 C23.8390024,21.7041238 23.625,22.0503869 23.625,22.429159 Z"
                                                                      opacity="0.7"></path>
                                                                <path class="color-background"
                                                                      d="M20.4472136,21.5347318 L1.4472136,12.0347318 C0.953235098,11.7877425 0.352562058,11.9879669 0.105572809,12.4819454 C0.0361450918,12.6208008 6.47121774e-16,12.7739139 0,12.929159 L0,30.1875 L0,30.1875 C0,30.6849375 0.280875,31.1390625 0.7258125,31.3621875 L19.5528096,40.7750766 C20.0467945,41.0220531 20.6474623,40.8218132 20.8944388,40.3278283 C20.963859,40.1889789 21,40.0358742 21,39.8806379 L21,22.429159 C21,22.0503869 20.7859976,21.7041238 20.4472136,21.5347318 Z"
                                                                      opacity="0.7"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </label>
                                        <h6>Generate RRR </h6>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($remitaDetails) &&  $remitaDetails['status'])
                                <div id="genRRRstep2" class=" col-lg-6 mx-auto card  ">
                                    <div class="card-body pt-4 text-center">
                                        <h2 class=" mb-0 mt-2 up">RRR DETIALS</h2>
                                        <h1 class=" mb-0 up" id="rrr">{{$remitaDetails['RRR']}}</h1>
                                        <br>

                                        <span class="badge badge-lg d-block bg-gradient-dark mb-2 up" role="button"
                                              onclick="copyTextToClipboard('rrr')">
                                <i class="fas fa-clipboard"></i>
                                Copy RRR
                            </span>

                                        <h6> OR </h6>
                                        <a href="javascript:;" class="btn btn-outline-dark mb-2 px-5 up" id="rrrLink">
                                            <i class="fas fa-rocket"></i>
                                            Go to Remita
                                            <i class="fas fa-rocket"></i>
                                        </a>
                                    </div>

                                </div>
                            @endif


                        </div>
                    </div>
                @endif



                @if(isset($merchantGateways['card']))
                    <script src="{{asset('assets/js/imask.js')}}"></script>

                    <div id="card" class="container tab-pane @if($activeTab === "card") active @endif"><br>
                        <div class=" mt-3">
                            <h3 class="text-secondary font-weight-normal text-center">Pay Using Card.</h3>
                            <form role="form" method="post" name="payWithCard" id="payWithCard"
                                  onsubmit="event.preventDefault(); cardPayment(this); ">

                                <div class="row mt-3">
                                    <div class="col-xl-6 col-lg-5 col-md-6 d-flex flex-column mx-lg-0 mx-auto ">

                                        @if(!$hideCardFields)
                                            <div id="mandatoryFields">

                                                <div class=" mb-3 field-container">
                                                    <label for="card_number">Card Number</label>
                                                    <input class="form-control" required id="card_number"
                                                           name="card_number"
                                                           type="text"
                                                           inputmode="numeric">
                                                    <svg id="ccicon" class="ccicon" width="750" height="471"
                                                         viewBox="0 0 750 471"
                                                         version="1.1" xmlns="http://www.w3.org/2000/svg"
                                                    >

                                                    </svg>
                                                </div>

                                                <div class="col-md-6 mb-3 field-container">
                                                    <label for="expirationdate">Expiration (mm/yy)</label>
                                                    <input required class="form-control" id="expirationdate" type="text"
                                                           pattern="^[0-9]{2}\/[0-9]{2}$"
                                                           name="cc_expiration">
                                                </div>
                                                <div class="col-md-6 mb-3 field-container">
                                                    <label for="cvv">CVV</label>
                                                    <input class="form-control" required id="cvv" type="text"
                                                           pattern="[0-9]*"
                                                           name="cvv">
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    @if(!$hideCardFields )
                                        <div class="col-xl-6 col-lg-7 col-md-6  d-lg-flex d-none  flex-column  preload">
                                            <div class="creditcard">
                                                <div class="front">
                                                    <div id="ccsingle"></div>
                                                    <svg version="1.1" id="cardfront" xmlns="http://www.w3.org/2000/svg"
                                                         x="0px" y="0px" viewBox="0 0 750 471"
                                                         style="enable-background:new 0 0 750 471;"
                                                         xml:space="preserve">
                                                                            <g id="Front">
                                                                                <g id="CardBackground">
                                                                                    <g id="Page-1_1_">
                                                                                        <g id="amex_1_">
                                                                                            <path id="Rectangle-1_1_"
                                                                                                  class="lightcolor grey"
                                                                                                  d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                            C0,17.9,17.9,0,40,0z"/>
                                                                                        </g>
                                                                                    </g>
                                                                                    <path class="darkcolor greydark"
                                                                                          d="M750,431V193.2c-217.6-57.5-556.4-13.5-750,24.9V431c0,22.1,17.9,40,40,40h670C732.1,471,750,453.1,750,431z"/>
                                                                                </g>
                                                                                <text
                                                                                    transform="matrix(1 0 0 1 60.106 295.0121)"
                                                                                    id="svgnumber" class="st2 st3 st4">
                                                                                    0123 4567 8910 1112
                                                                                </text>
                                                                                <text
                                                                                    transform="matrix(1 0 0 1 54.1064 428.1723)"
                                                                                    id="svgname" class="st2 st5 st6">
                                                                                    JOHN DOE
                                                                                </text>
                                                                                <text
                                                                                    transform="matrix(1 0 0 1 54.1074 389.8793)"
                                                                                    class="st7 st5 st8">cardholder name
                                                                                </text>
                                                                                <text
                                                                                    transform="matrix(1 0 0 1 479.7754 388.8793)"
                                                                                    class="st7 st5 st8">expiration
                                                                                </text>
                                                                                <text
                                                                                    transform="matrix(1 0 0 1 65.1054 241.5)"
                                                                                    class="st7 st5 st8">card number
                                                                                </text>
                                                                                <g>
                                                                                    <text
                                                                                        transform="matrix(1 0 0 1 574.4219 433.8095)"
                                                                                        id="svgexpire"
                                                                                        class="st2 st5 st9">01/23
                                                                                    </text>
                                                                                    <text
                                                                                        transform="matrix(1 0 0 1 479.3848 417.0097)"
                                                                                        class="st2 st10 st11">VALID
                                                                                    </text>
                                                                                    <text
                                                                                        transform="matrix(1 0 0 1 479.3848 435.6762)"
                                                                                        class="st2 st10 st11">THRU
                                                                                    </text>
                                                                                    <polygon class="st2"
                                                                                             points="554.5,421 540.4,414.2 540.4,427.9    "/>
                                                                                </g>
                                                                                <g id="cchip">
                                                                                    <g>
                                                                                        <path class="st2" d="M168.1,143.6H82.9c-10.2,0-18.5-8.3-18.5-18.5V74.9c0-10.2,8.3-18.5,18.5-18.5h85.3
                        c10.2,0,18.5,8.3,18.5,18.5v50.2C186.6,135.3,178.3,143.6,168.1,143.6z"/>
                                                                                    </g>
                                                                                    <g>
                                                                                        <g>
                                                                                            <rect x="82" y="70"
                                                                                                  class="st12"
                                                                                                  width="1.5"
                                                                                                  height="60"/>
                                                                                        </g>
                                                                                        <g>
                                                                                            <rect x="167.4" y="70"
                                                                                                  class="st12"
                                                                                                  width="1.5"
                                                                                                  height="60"/>
                                                                                        </g>
                                                                                        <g>
                                                                                            <path class="st12" d="M125.5,130.8c-10.2,0-18.5-8.3-18.5-18.5c0-4.6,1.7-8.9,4.7-12.3c-3-3.4-4.7-7.7-4.7-12.3
                            c0-10.2,8.3-18.5,18.5-18.5s18.5,8.3,18.5,18.5c0,4.6-1.7,8.9-4.7,12.3c3,3.4,4.7,7.7,4.7,12.3
                            C143.9,122.5,135.7,130.8,125.5,130.8z M125.5,70.8c-9.3,0-16.9,7.6-16.9,16.9c0,4.4,1.7,8.6,4.8,11.8l0.5,0.5l-0.5,0.5
                            c-3.1,3.2-4.8,7.4-4.8,11.8c0,9.3,7.6,16.9,16.9,16.9s16.9-7.6,16.9-16.9c0-4.4-1.7-8.6-4.8-11.8l-0.5-0.5l0.5-0.5
                            c3.1-3.2,4.8-7.4,4.8-11.8C142.4,78.4,134.8,70.8,125.5,70.8z"/>
                                                                                        </g>
                                                                                        <g>
                                                                                            <rect x="82.8" y="82.1"
                                                                                                  class="st12"
                                                                                                  width="25.8"
                                                                                                  height="1.5"/>
                                                                                        </g>
                                                                                        <g>
                                                                                            <rect x="82.8" y="117.9"
                                                                                                  class="st12"
                                                                                                  width="26.1"
                                                                                                  height="1.5"/>
                                                                                        </g>
                                                                                        <g>
                                                                                            <rect x="142.4" y="82.1"
                                                                                                  class="st12"
                                                                                                  width="25.8"
                                                                                                  height="1.5"/>
                                                                                        </g>
                                                                                        <g>
                                                                                            <rect x="142" y="117.9"
                                                                                                  class="st12"
                                                                                                  width="26.2"
                                                                                                  height="1.5"/>
                                                                                        </g>
                                                                                    </g>
                                                                                </g>
                                                                            </g>
                                                        <g id="Back">
                                                        </g>
                                                                        </svg>
                                                </div>
                                                <div class="back">
                                                    <svg version="1.1" id="cardback" xmlns="http://www.w3.org/2000/svg"
                                                         x="0px" y="0px" viewBox="0 0 750 471"
                                                         style="enable-background:new 0 0 750 471;"
                                                         xml:space="preserve">
                                                                            <g id="Front">
                                                                                <line class="st0" x1="35.3" y1="10.4"
                                                                                      x2="36.7" y2="11"/>
                                                                            </g>
                                                        <g id="Back">
                                                            <g id="Page-1_2_">
                                                                <g id="amex_2_">
                                                                    <path id="Rectangle-1_2_" class="darkcolor greydark"
                                                                          d="M40,0h670c22.1,0,40,17.9,40,40v391c0,22.1-17.9,40-40,40H40c-22.1,0-40-17.9-40-40V40
                                                                                        C0,17.9,17.9,0,40,0z"/>
                                                                </g>
                                                            </g>
                                                            <rect y="61.6" class="st2" width="750" height="78"/>
                                                            <g>
                                                                <path class="st3" d="M701.1,249.1H48.9c-3.3,0-6-2.7-6-6v-52.5c0-3.3,2.7-6,6-6h652.1c3.3,0,6,2.7,6,6v52.5
                                                                                        C707.1,246.4,704.4,249.1,701.1,249.1z"/>
                                                                <rect x="42.9" y="198.6" class="st4" width="664.1"
                                                                      height="10.5"/>
                                                                <rect x="42.9" y="224.5" class="st4" width="664.1"
                                                                      height="10.5"/>
                                                                <path class="st5"
                                                                      d="M701.1,184.6H618h-8h-10v64.5h10h8h83.1c3.3,0,6-2.7,6-6v-52.5C707.1,187.3,704.4,184.6,701.1,184.6z"/>
                                                            </g>
                                                            <text transform="matrix(1 0 0 1 621.999 227.2734)"
                                                                  id="svgsecurity"
                                                                  class="st6 st7">985
                                                            </text>
                                                            <g class="st8">
                                                                <text transform="matrix(1 0 0 1 518.083 280.0879)"
                                                                      class="st9 st6 st10">security code
                                                                </text>
                                                            </g>
                                                            <rect x="58.1" y="378.6" class="st11" width="375.5"
                                                                  height="13.5"/>
                                                            <rect x="58.1" y="405.6" class="st11" width="421.7"
                                                                  height="13.5"/>
                                                            <text transform="matrix(1 0 0 1 59.5073 228.6099)"
                                                                  id="svgnameback"
                                                                  class="st12 st13">John Doe
                                                            </text>
                                                        </g>
                                                                        </svg>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="mb-4">

                                        <div class="col-12 text-center">
                                            <button class="btn btn-success btn-lg btn-block"
                                                    type="submit">Pay &#{{$invoice->currency_symbol}}; {{number_format($merchantGateways[$activeTab]['invoiceTotal'])}}
                                                ({{$invoice->transaction->currency}})
                                            </button>
                                        </div>
                                    @endif

                                    {{--                            Pin details here--}}
                                    @if($isPinRequired)
                                        <div id="pinField">
                                            <div class="col-md-6 mb-3 field-container">

                                            </div>
                                        </div>

                                        <div class="col-xl-5 col-lg-5 col-md-7 mx-auto">
                                            <div class="card py-lg-3">
                                                <div class="card-body text-center">

                                                    <h4 class="mb-0 font-weight-bolder">PIN REQUIRED</h4>
                                                    <div class="mb-3">
                                                        <label for="pin">PIN</label>
                                                        <input class="form-control" placeholder="****" id="pin"
                                                               type="password"
                                                               pattern="[0-9]*" minlength="4" maxlength="4" name="pin"
                                                               wire:model.lazy="cc_Pin"></div>
                                                    <div class="text-center">
                                                        <button type="button"
                                                                class="btn btn-lg bg-gradient-dark mt-3 mb-0"
                                                                onclick="authorizeWith('Pin')">Process
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($isOtpRequired)
                                        <div id="otpField">
                                            <div class="col-md-6 mb-3 field-container">

                                            </div>
                                        </div>

                                        <div class="col-xl-5 col-lg-5 col-md-7 mx-auto">
                                            <div class="card py-lg-3">
                                                <div class="card-body text-center">

                                                    <h4 class="mb-0 font-weight-bolder">OTP REQUIRED</h4>
                                                    <div class="mb-3">
                                                        <label for="otp">OTP</label>
                                                        <input class="form-control" placeholder="****" id="otp"
                                                               type="password"
                                                               pattern="[0-9]*" name="otp" wire:model.lazy="cc_Otp">
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button"
                                                                class="btn btn-lg bg-gradient-dark mt-3 mb-0"
                                                                onclick="authorizeWith('Otp')">Process
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif


                                </div>
                            </form>


                        </div>
                    </div>
                @endif


                @if(isset($merchantGateways['banktransfer']))
                    <div id="banktransfer"
                         class="container tab-pane  @if($activeTab === "banktransfer") active  @endif">
                        <br>
                        <div class="text-center mt-3">
                            <h3 class="text-secondary font-weight-normal">Pay Using Bank Transfer.</h3>

                            @if(empty($virtualAccDetails) &&  !isset($virtualAccDetails['status']))
                                <div id="genVirtualAccstep1">
                                    <div class="col-sm-3 mx-auto mt-4">
                                        <input type="button" class="btn-check" id="generateAcc">
                                        <label class="btn btn-lg btn-outline-secondary border-2 px-6 py-5"
                                               for="btncheck2"
                                               onclick="generateVirtualACC()">
                                            <svg class="text-dark" width="20px" height="20px" viewBox="0 0 42 42"
                                                 version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <title>box-3d-50</title>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <g transform="translate(-2319.000000, -291.000000)" fill="#FFFFFF"
                                                       fill-rule="nonzero">
                                                        <g transform="translate(1716.000000, 291.000000)">
                                                            <g transform="translate(603.000000, 0.000000)">
                                                                <path class="color-background"
                                                                      d="M22.7597136,19.3090182 L38.8987031,11.2395234 C39.3926816,10.9925342 39.592906,10.3918611 39.3459167,9.89788265 C39.249157,9.70436312 39.0922432,9.5474453 38.8987261,9.45068056 L20.2741875,0.1378125 L20.2741875,0.1378125 C19.905375,-0.04725 19.469625,-0.04725 19.0995,0.1378125 L3.1011696,8.13815822 C2.60720568,8.38517662 2.40701679,8.98586148 2.6540352,9.4798254 C2.75080129,9.67332903 2.90771305,9.83023153 3.10122239,9.9269862 L21.8652864,19.3090182 C22.1468139,19.4497819 22.4781861,19.4497819 22.7597136,19.3090182 Z"></path>
                                                                <path class="color-background"
                                                                      d="M23.625,22.429159 L23.625,39.8805372 C23.625,40.4328219 24.0727153,40.8805372 24.625,40.8805372 C24.7802551,40.8805372 24.9333778,40.8443874 25.0722402,40.7749511 L41.2741875,32.673375 L41.2741875,32.673375 C41.719125,32.4515625 42,31.9974375 42,31.5 L42,14.241659 C42,13.6893742 41.5522847,13.241659 41,13.241659 C40.8447549,13.241659 40.6916418,13.2778041 40.5527864,13.3472318 L24.1777864,21.5347318 C23.8390024,21.7041238 23.625,22.0503869 23.625,22.429159 Z"
                                                                      opacity="0.7"></path>
                                                                <path class="color-background"
                                                                      d="M20.4472136,21.5347318 L1.4472136,12.0347318 C0.953235098,11.7877425 0.352562058,11.9879669 0.105572809,12.4819454 C0.0361450918,12.6208008 6.47121774e-16,12.7739139 0,12.929159 L0,30.1875 L0,30.1875 C0,30.6849375 0.280875,31.1390625 0.7258125,31.3621875 L19.5528096,40.7750766 C20.0467945,41.0220531 20.6474623,40.8218132 20.8944388,40.3278283 C20.963859,40.1889789 21,40.0358742 21,39.8806379 L21,22.429159 C21,22.0503869 20.7859976,21.7041238 20.4472136,21.5347318 Z"
                                                                      opacity="0.7"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </label>
                                        <h6>Generate Account Number</h6>
                                    </div>
                                </div>
                            @endif

                            <div id="genVirtualAccstep2" class=" mt-2" tabindex="0">
                                <div class=" mx-auto card  word-break ">
                                    <div class="card-body pt-4 text-start ">
                                        @if(!empty($virtualAccDetails))
                                            <div class="row" id="bankTransferDisplayContent">
                                                @if(!in_array($invoice->user->id, config('bankTransfer.initiate4merchants')))
                                                    <div
                                                        class="alert alert-warning alert-dismissible fade show  text-white "
                                                        role="alert">
                                                        <span class="alert-icon"><i class="fa fa-info-circle"></i></span>
                                                        <span class="alert-text font-weight-bold">
                                                                This account is valid only for this transaction and expires in  !
                                                                <div id="countdown"
                                                                     class=" text-danger text-center text-bold"
                                                                     style="font-size: 25px"></div>
                                                            </span>

                                                    </div>
                                                @endif
                                                <div  class="card" tabindex="0">
                                                    <ul class="list-group " style="font-size: 14px">
                                                        <li class="list-group-item d-flex align-items-center">
                                                            <i class="fa-bank text-dark fa-2x fa avatar avatar-sm me-2"></i>
                                                            <div
                                                                class="d-flex flex-column justify-content-center">
                                                                <h6 class=" text-xs">Bank Name</h6>
                                                                <p style="word-break: break-word; font-size: 18px" class="text-xl font-weight-bolder text-secondary mb-0"
                                                                   id="bankName">{{$virtualAccDetails['bankName']}}</p>
                                                            </div>

                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center">
                                                            <i class="fa-list-numeric text-dark fa-2x fa  avatar avatar-sm me-2"></i>
                                                            <div
                                                                class="d-flex flex-column justify-content-center">
                                                                <h6 class=" text-xs">Account Number</h6>
                                                                <p style="word-break: break-word; font-size: 18px" class="text-xl font-weight-bolder text-secondary mb-0 " >
                                                                    <span id="bankAccountNumber"> {{$virtualAccDetails['accountNumber']}} </span>
                                                                    @if(!empty($virtualAccDetails) && $virtualAccDetails['status'])
                                                                        <span
                                                                            class="badge badge-sm bg-gradient-dark "
                                                                            role="button"
                                                                            onclick="copyTextToClipboard('bankAccountNumber')"><i
                                                                                class="fas fa-copy"></i>
                                                                                            </span>
                                                                    @endif
                                                                    @if(in_array($invoice->user->id, config('bankTransfer.initiate4merchants')))
                                                                        <br>
                                                                        <span  class=" text-danger text-center text-bold"
                                                                               style="font-size: 15px" >
                                                                            Expires in
                                                                            <span id="countdown" class=" text-danger text-center text-bold" style="font-size: 15px">
                                                                            </span>
                                                                    </span>
                                                                    @endif

                                                                </p>
                                                            </div>

                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center">
                                                            <i class="fa-search-location text-dark fa-2x fa  avatar avatar-sm me-2"></i>
                                                            <div
                                                                class="d-flex flex-column justify-content-center">
                                                                <h6 class=" text-xs">Account Name</h6>
                                                                <p style="word-break: break-word; font-size: 18px" class="text-xl font-weight-bolder text-secondary mb-0">{{$virtualAccDetails['accountName']}}</p>
                                                            </div>

                                                        </li>
                                                        <li class="list-group-item d-flex align-items-center">
                                                            <i class=" text-dark fa-2x fa fa-money avatar avatar-sm me-2"></i>
                                                            <div
                                                                class="d-flex flex-column justify-content-center">
                                                                <h6 class=" text-xs">Amount Payable</h6>
                                                                <p style="word-break: break-word; font-size: 18px" class="text-xl font-weight-bolder text-secondary mb-0">
                                                                     &#{{$invoice->currency_symbol}};{{number_format($merchantGateways[$activeTab]['invoiceTotal'],2)}} ({{$invoice->transaction->currency}})</p>
                                                            </div>

                                                        </li>

                                                    </ul>
                                                    <hr>
                                                    <div class="mt-4">
                                                        <ul class="list-group " style="font-size: 14px">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <b class="text-danger">Always ensure the Account name
                                                                    matches before making a transfer</b>
                                                                <span class="badge badge-warning badge-pill"><i
                                                                        class="fa fa-info text-info"></i></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <b class="text-danger">This is a one-off account number,
                                                                    do not save or re-use it</b>
                                                                <span class="badge badge-warning badge-pill"><i
                                                                        class="fa fa-info text-info"></i></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <b class="text-danger"> Due to delays on the banking
                                                                    networks, please wait 15 minutes for our bank to
                                                                    acknowledge receipt. </b>
                                                                <span class="badge badge-warning badge-pill"><i
                                                                        class="fa fa-info text-info"></i></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <b class="text-danger">Ensure you complete your transfer
                                                                    within the period displayed by the timer </b>
                                                                <span class="badge badge-warning badge-pill"><i
                                                                        class="fa fa-info text-info"></i></span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                            </div>



                                        @endif
                                        <br>


                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                @endif


                @if(isset($merchantGateways['cashatbank']))
                    <div id="cashatbank" class="container tab-pane  @if($activeTab === "cashatbank") active @endif"><br>

                        <div class="text-center mt-3">
                            <h3 class="text-secondary font-weight-normal">Pay Using CashAtBank.</h3>

                            <div>
                                <div class="mx-auto mt-4">
                                    <h4 class=" mb-0 mt-2 up"> Kindly Visit the UNIBADAN MICROFINANCE Bank with your
                                        Invoice Number</h4>
                                    <br>
                                    <h2 class=" mb-0 mt-2 up"> {{str_replace("INV","",$invoice->invoice_no)}} </h2>

                                </div>
                            </div>

                        </div>

                    </div>
                @endif


                @if(isset($merchantGateways['googlepay']))
                    <div id="googlepay" class="container tab-pane  @if($activeTab === "googlepay") active @endif"><br>

                        <div class="text-center mt-3">
                            <h3 class="text-secondary font-weight-normal">Pay Using GooglePAY.</h3>

                            <div>
                                <div class="mx-auto mt-4">
                                    <input type="button" class="btn-check" id="generateAcc">
                                    <label class="btn btn-lg btn-outline-secondary border-2 px-6 py-5" for="btncheck2"
                                           onclick="authorizeWith('Googlepay')">
                                        <i class="fa-brands fa-google-pay " style="font-size: 70px">

                                        </i>
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>
                @endif

                @if(isset($merchantGateways['applepay']))
                    <div id="applepay" class="container tab-pane  @if($activeTab === "applepay") active @endif"><br>
                        <div class="text-center mt-3">
                            <h3 class="text-secondary font-weight-normal">Pay Using ApplePAY.</h3>

                            <div>
                                <div class=" mx-auto mt-4">
                                    <input type="button" class="btn-check" id="generateAcc">
                                    <label class="btn btn-lg btn-outline-secondary border-2 px-6 py-5" for="btncheck2"
                                           onclick="authorizeWith('Applepay')">
                                        <i class="fa-brands fa-apple-pay" style="font-size: 70px">

                                        </i>
                                    </label>
                                </div>
                            </div>

                        </div>

                    </div>
                @endif
            </div>
        </div>
        <div class="justify-content-center">
            <hr>
            <p class="mb-2 text-center"><i class="fa fa-lock"></i> Secured by <b
                    class="text-primary text-gradient">Saanapay</b>
            </p>
        </div>


        <script>
            document.addEventListener("DOMContentLoaded", () => {
                sclose();
                @if(isset($merchantGateways['banktransfer']))
                    @if(in_array($invoice->user->id, config('bankTransfer.initiate4merchants')))
                        generateVirtualACC();
                    @endif
                    document.getElementById('genVirtualAccstep2').focus();
                @endif
            });

            function setActiveTab(tab) {
                @this.activeTab = tab;
                let timerInterval
                Swal.fire({
                    title: 'Please Wait!',
                    timer: 1500,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading()
                        timerInterval = setInterval(null,100);
                    },
                    willClose: () => {
                        clearInterval(timerInterval)
                    }
                })

            }

            function generateRRR() {
                Swal.fire({
                    title: 'Generating RRR ! Please wait!',
                    html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                });

                Livewire.emit('generateRRR');
            }

            addEventListener('rrrGenerated', event => {

                let result = @this.remitaDetails;

                if (result.status === true) {

                    document.getElementById('genRRRstep2').style.display = "block";
                    let rrr = document.getElementById('rrr');
                    rrr.innerText = result.RRR;
                    document.getElementById('rrrLink').setAttribute('href', result.url)

                    salert('RRR GENERATED SUCCESSFULLY', 'success', 'success');

                } else {

                    salert('Please Try Again Later!', 'Error!' + result.errors, 'error');


                }


            })


            function generateVirtualACC() {
                Swal.fire({
                    title: 'Generating Virtual Account ! Please wait!',
                    html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                });

                Livewire.emit('generateVirtualAccountNumber');
            }

            addEventListener('virtualAccountGenerated', event => {

                let response = event.detail;


                let bankName = document.getElementById('bankName');

                let bankAccount = document.getElementById('bankAccount');

                let bankAccountNumber = document.getElementById('bankAccountNumber');


                if (response.status === true) {

                    // document.getElementById('genVirtualAccstep1').style.display = "none";
                    document.getElementById('genVirtualAccstep2').style.display = "block";

                    // Usage: Set the desired end time for the countdown
                    const endTime = new Date(response.endtime).getTime();
                    countdownTimer(endTime);
                    salert('Virtual Account GENERATED SUCCESSFULLY', 'success', 'success');


                } else {
                    // document.getElementById('genVirtualAccstep1').style.display = "block";
                    document.getElementById('genVirtualAccstep2').style.display = "block";
                    bankName.innerText = "😢 Oops Channel not Available! Please try other PAYMENT CHANNELS!"

                }

                sclose();


            })

            // JavaScript code for the countdown timer
            function countdownTimer(endTime) {
                const countdownElement = document.getElementById('countdown');
                const bankTransfercontent = document.getElementById('bankTransferDisplayContent');

                // Update the countdown every second
                const timer = setInterval(updateCountdown, 1000);

                function updateCountdown() {
                    const currentTime = new Date().getTime();
                    const distance = endTime - currentTime;

                    // Check if the countdown is over
                    if (distance <= 0) {
                        clearInterval(timer);
                        countdownElement.textContent = 'Countdown is over! Account Number Expired, Please Reload Page';
                        bankTransfercontent.style.visibility = 'hidden';
                        return;
                    }

                    // Calculate the remaining time
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Format the remaining time as a string
                    // Update the countdown element with the remaining time
                    countdownElement.textContent = `${hours}h:${minutes}m:${seconds}s`;
                }

                // Initial update to prevent delay
                updateCountdown();
            }

            function cardPayment(element) {
                event.preventDefault();
                let pin = document.getElementById('pin');
                let otp = document.getElementById('otp');
                if (pin !== null) {
                    //pin length validation;
                    if (pin.value.length < 4) {
                        salert("Validation Failed!", "Please enter a 4-digit PIN!", "error");
                        return;
                    }
                    authorizeWith('Pin')
                    return;
                }

                if (otp !== null) {
                    //otp length validation;
                    if (otp.value.length < 4) {
                        salert("Validation Failed!", "Please enter a valid OTP!", "error");
                        return;
                    }
                    authorizeWith('Otp')
                    return;
                }


                const formData = new FormData(element);
                let formValues = {};
                formData.forEach(function (value, key) {
                    formValues[key] = value;
                });
                //bind value;
                @this.
                cardDetails
                    = JSON.stringify(formValues);

                Swal.fire({
                    title: 'Transaction Processing ! Please wait!',
                    html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                });

                Livewire.emit('processCardTransaction');

            }

            addEventListener('cardPaymentProcessed', event => {

                let response = event.detail;
                let cardDetails = @this.cardDetails;


                if (response.status === true) {
                    //check for flag;
                    if (response.flag === "pin_required") {

                        salert('Input your Pin to continue', 'Information Needed', 'info');
                    }
                    if (response.flag === "redirect_required") {

                        location.assign(response.url);

                        Swal.fire({
                            title: 'Redirecting ! Please wait!',
                            html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEnterKey: false,
                        });

                    }
                    if (response.flag === "charge_card") {

                        authorizeWith('AVS');

                    }

                    if (response.flag === "otp_required") {

                        salert('OTP Required to Proceed!', 'Information Needed', 'info');
                    }
                } else {
                    return salert('Payment Failed!', response.errors + "! or Use another Payment Channel! ", 'error');

                }

            })

            function authorizeWith(type) {

                Swal.fire({
                    title: 'Transaction Processing ! Please wait!',
                    html: '  <span class="spinner-border spinner-border-lg text-primary"></span>\n',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                });

                if (type.toUpperCase() === "PIN") {
                    let pin = document.getElementById('pin');
                    //pin length validation;
                    if (pin.value.length < 4) {
                        salert("Validation Failed!", "Please enter a 4-digit PIN!", "error");
                        return;
                    }
                    Livewire.emit('cardAuthorizationWithPin');
                }
                if (type.toUpperCase() === "OTP") {
                    let otp = document.getElementById('otp');
                    //otp length validation;
                    if (otp.value.length < 4) {
                        salert("Validation Failed!", "Please enter a valid OTP!", "error");
                        return;
                    }
                    Livewire.emit('cardAuthorizationWithOtp');
                }
                if (type.toUpperCase() === "AVS") {
                    Livewire.emit('cardAuthorizationWithAvs');
                }

                if (type.toUpperCase() === "GOOGLEPAY") {
                    Livewire.emit('payWith', 'Googlepay');
                }
                if (type.toUpperCase() === "APPLEPAY") {
                    Livewire.emit('payWith', 'Applepay');
                }

            }

            addEventListener('paymentCompleted', event => {

                let response = event.detail;

                if (response.flag === "payment_completed") {
                    stimer('Payment Completed! Redirecting', 5000);
                    setTimeout(function () {
                        sprocessing("Redirecting ! Please wait!");
                        location.assign("{{route('receipt',$invoice->invoice_no)}}")
                    }, 5000)


                }

                if (response.flag === "processing") {
                    salert('Payment Processing!', "Pending!", 'info');
                }

                if (response.status === false) {
                    salert('Payment Failed!', "Payment Failed! Please Try Again or Use another Channel!", 'error');
                }


            });


            function getUssdAccountDetails() {

                Swal.fire({
                    title: 'Getting Info! Please wait!',
                    icon: 'info',
                    button: false,
                    closeOnEsc: false,
                    closeOnClickOutside: false,
                });

                let selectedBank = document.getElementById("bankList").value;
                if (selectedBank === "") {
                    alert("Please Select a Bank");

                }


            }


        </script>

    </div>
@else
    <section class="mt-1">
        <div class="page-header min-vh-100">
            <div class="row ">
                <div class="col-md-7 mb-5">
                    <div class=" pb-3 text-center " style="margin-top: 3rem;">
                        <h2 class="font-weight-bolder">
                            😢 <br>Payment Gateway Not Configured!
                        </h2>
                        <h3>Please contact Saanapy Support!.</h3>
                        <p class="mb-0">
                            <a href="mailto:support@saanapay.ng">
                                <i class="fa fa-envelope-open fa-2x"> </i>
                                Saanapy Support
                            </a>.
                        </p>
                    </div>


                </div>

                <div class="col-md-5 ">
                    <div class="">
                        <img class=" w-100"
                             src="{{asset('assets/img/illustrations/error-500.png')}}"
                             alt="error_image">
                    </div>

                </div>
            </div>


        </div>
    </section>

@endif
