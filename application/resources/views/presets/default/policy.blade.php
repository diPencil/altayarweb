@extends($activeTemplate.'layouts.frontend')
@section('content')

<section class="cookie-section section--bg py-100 position-relative">

    <div class="bg--thumb position-absolute one">
        <img src="{{ asset($activeTemplateTrue . 'images/shape/element-9.png') }}" alt="shape-image">
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="coockie-wrap">
                    <div class="wyg">
                        @php
                            echo $policy->data_values->details
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>
    .wyg h1, h2, h3, h4{
        color: black;
    }
     .wyg h5{
        margin-top: 30px;
    }
    .wyg strong{
        color: black;
    }
    .wyg p{
        color:  black;
    }
    .wyg ul{
        margin-left: 40px
    }
    .wyg ul li{
        list-style-type: disc;
        color:  black;
    }
    .section-title{
        font-size: 30px;
        margin-bottom: 0;
    }
</style>
@endpush
