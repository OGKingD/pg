{{--    //modal to edit gateway comes here;--}}
{{--<div class="modal fade" id="modal-edit-user-gateway" tabindex="-1" aria-labelledby="modal-edit-user-gateway"--}}
{{--     aria-hidden="true" style="display: none;">--}}
{{--    <div class="modal-dialog modal-dialog-centered modal-md" role="document">--}}
{{--        <div class="modal-content">--}}

{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<form role="form"  method="post" onsubmit="editUserPaymentGateways(this)">
    <div class="modal-body p-0">
        <div class="card card-plain justify-content-center">

            <div class="card-body">
                @if(!is_null($merchantGateways))

                    @foreach($merchantGateways as $key => $gway)


                        <fieldset>
                            <legend>
                                <div class="row justify-content-center align-items-center">
                                    <div class="col-sm-auto col-8 my-auto">
                                        <div class="h-100">
                                            <h3 class="mb-1 font-weight-bolder">
                                                {{$gway['name']}}
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">
                                        <label class="form-check-label mb-0">
                                            <h4 >
                                                Active
                                            </h4>
                                        </label>
                                        <div class="form-check form-switch ms-2">
                                            <label for="{{$gway['name']}}_status"></label>
                                            <input class="form-check-input" type="checkbox" id="{{$gway['name']}}_status" name="status+{{$key}}" @if($gway['status'] === 1) checked @endif
                                            style="height: 23px !important;">
                                        </div>
                                    </div>
                                </div>
                            </legend>

                            <div class="row">
                                <h6>Merchant Service</h6>

                                <div class="col-4">
                                    <label class="form-label " for="charge_factor"> Charge Type
                                        :</label>
                                    <select class="form-control form-select " id="merchant_service_charge_factor+{{$key}}"
                                            title="Percentage / Flat Rate" name="merchant_service_charge_factor+{{$key}}">
                                        <option value="0" @if(!$gway['merchant_service']['charge_factor']) selected @endif>
                                            Flat
                                            Rate
                                        </option>
                                        <option value="1" @if($gway['merchant_service']['charge_factor']) selected @endif>
                                            Percentage(%)
                                        </option>
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="form-label" for="charge_factor_{{$key}}}}"> Charge
                                        Value:</label>
                                    <div class=" input-group mb-3">
                                        <input type="number" class="form-control"
                                               id="merchant_service_charge_factor_{{$key}}" placeholder="Value..."
                                               value="{{$gway['merchant_service']['charge']}}" step="0.1"
                                               aria-label="Charge_value" onfocus="focused(this)"
                                               onfocusout="defocused(this)" name="merchant_service_charge+{{$key}}">
                                    </div>
                                </div>

                            </div>

                            {{--                                        //Customer Service Charge--}}
                            <div class="row">
                                <h6>Customer Service</h6>

                                <div class="col-4">
                                    <label class="form-label " for="charge_factor"> Charge Type
                                        :</label>
                                    <select class="form-control form-select " id="customer_service_charge_factor+{{$key}}"
                                            title="Percentage / Flat Rate" name="customer_service_charge_factor+{{$key}}">
                                        <option value="0" @if(!$gway['customer_service']['charge_factor']) selected @endif>
                                            Flat
                                            Rate
                                        </option>
                                        <option value="1" @if($gway['customer_service']['charge_factor']) selected @endif>
                                            Percentage(%)
                                        </option>
                                    </select>
                                </div>

                                <div class="col-4">
                                    <label class="form-label" for="charge_factor_{{$key}}}}"> Charge
                                        Value:</label>
                                    <div class=" input-group mb-3">
                                        <input type="number" class="form-control"
                                               id="customer_service_charge+{{$key}}" placeholder="Value..."
                                               value="{{$gway['customer_service']['charge']}}" step="0.1"
                                               aria-label="Charge_value" onfocus="focused(this)"
                                               onfocusout="defocused(this)" name="customer_service_charge+{{$key}}">
                                    </div>
                                </div>

                            </div>


                            <div class="col d-none">
                                <label>
                                    <input type="text" name="name+{{$key}}" value="{{$gway['name']}}">
                                </label>
                            </div>

                        </fieldset>

                        <hr>

                    @endforeach

                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="text-center">
            <button type="submit" class="btn btn-round bg-gradient-info btn-sm mb-0">
                Update
            </button>
        </div>

    </div>
</form>

