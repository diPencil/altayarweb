@extends($activeTemplate.'layouts.user.master')

@section('content')

<div class="row mb-4 justify-content-center">
    <div class="col-lg-8">
        <div class="base--card">
            <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="text-center mt-2">@lang('You have requested') <b class="text-success">{{ showAmount($data['amount'])  }} {{__($general->cur_text)}}</b> , @lang('Please pay')
                            <b class="text-success">{{showAmount($data['final_amo']) .' '.$data['method_currency'] }} </b> @lang('for successful payment')
                        </p>
                        <h4 class="text-center mb-4">@lang('Please follow the instruction below')</h4>
                        <p class="my-4 text-center">@php echo  $data->gateway->description @endphp</p>
                    </div>

                    <div class="mt-3">
                        <x-custom-form identifier="id" identifierValue="{{ $gateway->form_id }}"></x-custom-form>
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn--base btn--lg mt-3 pills">@lang('Pay Now')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
