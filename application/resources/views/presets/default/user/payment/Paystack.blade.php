@extends($activeTemplate . 'layouts.user.master')
@section('content')
    <div class="row mb-4 justify-content-center">
        <div class="col-lg-6">
            <div class="base--card">
                <form action="{{ route('ipn.' . $deposit->gateway->alias) }}" method="POST" class="text-center">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-lg-12">
                            <ul class="list-group text-center">
                                <li class="list-group-item d-flex justify-content-between">
                                    @lang('You have to pay '):
                                    <strong>{{showAmount($deposit->final_amo)}} {{__($deposit->method_currency)}}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    @lang('You will get '):
                                    <strong>{{showAmount($deposit->amount)}} {{__($general->cur_text)}}</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-12 text-end">
                            <button type="button" class="btn btn--base btn--lg" id="btn-confirm">@lang('Pay Now')</button>
                        </div>
                    </div>
                    <script src="//js.paystack.co/v1/inline.js" data-key="{{ $data->key }}" data-email="{{ $data->email }}"
                        data-amount="{{ round($data->amount) }}" data-currency="{{$data->currency}}"
                        data-ref="{{ $data->ref }}" data-custom-button="btn-confirm">
                    </script>
                </form>
            </div>
        </div>
    </div>
@endsection
