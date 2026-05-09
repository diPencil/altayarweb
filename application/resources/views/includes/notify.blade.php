<script src="{{ asset('assets/common/js/sweetalert2.min.js') }}"></script>
<style>
    .swal2-container.swal2-toast-shown {
        width: auto !important;
        height: auto !important;
        overflow: visible !important;
    }

    .swal2-container .swal2-popup.swal2-toast.colored-toast {
        width: 380px !important;
        min-width: 280px !important;
        max-width: min(420px, calc(100vw - 32px)) !important;
        min-height: 76px !important;
        max-height: 92px !important;
        height: auto !important;
        padding: 14px 18px !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;
        box-sizing: border-box !important;
        flex-wrap: nowrap !important;
        direction: inherit;
        overflow: hidden !important;
    }

    .swal2-container .swal2-popup.swal2-toast.colored-toast .swal2-title {
        flex: 1 1 auto !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.35 !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        text-align: start !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
    }

    .swal2-container .swal2-popup.swal2-toast.colored-toast .swal2-icon {
        flex: 0 0 auto !important;
        margin: 0 !important;
        width: 2em !important;
        min-width: 2em !important;
        height: 2em !important;
        font-size: 1.05em !important;
    }

    .swal2-container .swal2-popup.swal2-toast.colored-toast .swal2-timer-progress-bar-container {
        position: absolute !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
    }

    [dir="rtl"] .swal2-container .swal2-popup.swal2-toast.colored-toast {
        flex-direction: row-reverse !important;
        font-family: "Tajawal", sans-serif;
    }
</style>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: '{{ is_rtl() ? 'top-left' : 'top-right' }}',
        width: 380,
        padding: '14px 18px',
        grow: false,
        customClass: {
            popup: 'colored-toast'
        },
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
</script>


@if(session()->has('notify'))
@foreach(session('notify') as $msg)
<script>
    "use strict";
    Toast.fire({
        icon: '{{ $msg[0] }}',
        title: '{{ __($msg[1]) }}'
    })
</script>
@endforeach
@endif

@if (isset($errors) && $errors->any())
@php
$collection = collect($errors->all());
$errors = $collection->unique();
@endphp

<script>
    "use strict";
    @foreach($errors as $error)

    Toast.fire({
        icon: 'error',
        title: '{{ __($error) }}'
    })

    @endforeach
</script>

@endif
