@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @php
        $employeeProfile = $employee ?? $Employee ?? null;
    @endphp
    <section class="profile--section py-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @include($activeTemplate . 'employee.profile.banner')
                    <div class="row main-content">

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        $(document).ready(function () {
            'user strict'
            let employeeId = {{ $employeeProfile?->id ?? 'null' }};
            //common load ajax function
            function loadContent(url, data) {
                $.ajax({
                    url: url,
                    method: 'GET',
                    data: data,
                    success: function (response) {
                        if (response.html) {
                            $('.main-content').html(response.html);
                        }
                    },
                    error: function (xhr, status, error) {
                     
                    }
                });
            }

            // load autmatic artwork
            loadContent("{{ route('seller.artwork.filter') }}", { agentId: employeeId, EmployeeId: employeeId });

            $('ul li a').on('click', function() {
                $('ul li a').removeClass('active');
                $(this).addClass('active');
                let route = $(this).data('route');
                loadContent(route, { agentId: employeeId, EmployeeId: employeeId });
            });

           
        });
    </script>
@endpush

