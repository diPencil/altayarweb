@extends($activeTemplate . (request()->routeIs('user.*') ? 'layouts.user.master' : 'layouts.frontend'))
@section('content')
<div class="invoice-area pt-30 pb-80" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                
                <!-- PDF Hidden Container (Perfectly sized for A4) -->
                <div style="position: absolute; left: -9999px; top: -9999px;">
                   <div id="pdf-container" style="width: 794px; min-height: 1123px; background: #ffffff; padding: 40px; display: flex; flex-direction: column; overflow: hidden;">
                        <!-- Logo & Header -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px;">
                            <div>
                                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" style="max-height: 35px;">
                                <div style="margin-top: 15px;">
                                    <h5 style="margin: 0; font-weight: bold; color: #333; font-size: 16px;">{{ __($general->site_name) }}</h5>
                                    <p style="margin: 0; color: #666; font-size: 13px;">A global search engine for<br>companies and individuals.</p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <h1 style="margin: 0; color: #333; text-transform: uppercase; letter-spacing: 2px; font-size: 28px; font-weight: bold;">{{ __('Invoice') }}</h1>
                                <p style="margin: 5px 0; color: #666; font-size: 13px;">#{{ $invoice->invoice_number }}</p>
                                <p style="margin: 0; color: #666; font-size: 13px;">{{ __('Date') }}: {{ showDateTime($invoice->issue_date, 'd M, Y') }}</p>
                            </div>
                        </div>

                        <!-- Info Boxes -->
                        <div style="display: flex; gap: 20px; margin-bottom: 40px;">
                            <div style="flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
                                <h6 style="margin: 0 0 10px 0; color: #888; text-transform: uppercase; font-size: 11px;">{{ __('Invoice To') }}</h6>
                                <h5 style="margin: 0 0 5px 0; font-weight: bold; font-size: 15px;">{{ __($invoice->user->fullname) }}</h5>
                                <p style="margin: 0; color: #666; font-size: 12px;">{{ $invoice->user->email }}</p>
                                @if($invoice->user->mobile)
                                <p style="margin: 0; color: #666; font-size: 12px;">{{ $invoice->user->mobile }}</p>
                                @endif
                            </div>
                            <div style="flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee; display: flex; flex-direction: column; justify-content: center;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span style="color: #888; font-size: 12px;">{{ __('Total Amount') }}</span>
                                    <span style="font-weight: bold; font-size: 15px;">{{ $general->cur_sym }}{{ showAmount($invoice->total_amount) }}</span>
                                </div>
                                @php $balance = $invoice->total_amount - $invoice->paid_amount; @endphp
                                <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px solid #ddd;">
                                    <span style="color: #888; font-size: 12px;">{{ __('Amount Due') }}</span>
                                    <span style="font-weight: bold; color: #d9534f; font-size: 15px;">{{ $general->cur_sym }}{{ showAmount($balance) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div style="flex-grow: 1;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #333; color: #fff;">
                                        <th style="padding: 10px; text-align: left; font-size: 12px;">{{ __('Description') }}</th>
                                        <th style="padding: 10px; text-align: center; font-size: 12px;">{{ __('Qty') }}</th>
                                        <th style="padding: 10px; text-align: right; font-size: 12px;">{{ __('Price') }}</th>
                                        <th style="padding: 10px; text-align: right; font-size: 12px;">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 12px 10px;">
                                            <div style="font-weight: bold; color: #333; font-size: 13px;">{{ __($item['name']) }}</div>
                                            <div style="font-size: 11px; color: #888; margin-top: 3px;">{{ __($item['description']) }}</div>
                                        </td>
                                        <td style="padding: 12px 10px; text-align: center; font-size: 12px;">{{ $item['quantity'] }}</td>
                                        <td style="padding: 12px 10px; text-align: right; color: #666; font-size: 12px;">{{ $general->cur_sym }}{{ showAmount($item['price']) }}</td>
                                        <td style="padding: 12px 10px; text-align: right; font-weight: bold; font-size: 12px;">{{ $general->cur_sym }}{{ showAmount($item['total']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                                <div style="width: 220px; background: #f9f9f9; padding: 12px; border-radius: 8px; border: 1px solid #eee;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="font-size: 12px;">{{ __('Subtotal') }}</span>
                                        <span style="font-size: 12px;">{{ $general->cur_sym }}{{ showAmount($invoice->subtotal) }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; font-weight: bold; padding-top: 5px; border-top: 1px solid #ddd;">
                                        <span style="font-size: 13px;">{{ __('Grand Total') }}</span>
                                        <span style="color: #00befa; font-size: 15px;">{{ $general->cur_sym }}{{ showAmount($invoice->total_amount) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Area -->
                        @if($invoice->notes)
                        <div style="margin-top: 25px; padding: 12px; border-left: 4px solid #f0ad4e; background: #fcf8e3; border-radius: 0 4px 4px 0;">
                            <h6 style="margin: 0 0 5px 0; font-size: 11px; font-weight: bold;">{{ __('Notes') }}:</h6>
                            <p style="margin: 0; font-size: 11px; color: #8a6d3b;">{{ $invoice->notes }}</p>
                        </div>
                        @endif

                        <!-- THE FOOTER -->
                        <div style="margin-top: auto; padding-top: 25px; border-top: 1px solid #eee; text-align: center;">
                            <p style="margin: 0; color: #333; font-weight: bold; font-size: 13px;">{{ __('Thank you for your business!') }}</p>
                            <p style="margin: 4px 0 0 0; color: #aaa; font-size: 10px;">{{ __('Computer generated invoice. No signature required.') }}</p>
                        </div>
                   </div>
                </div>

                <!-- Live Preview (Visible on Screen) -->
                <div class="invoice-card bg--white shadow-sm border-0 rounded-3 overflow-hidden" id="web-preview" style="max-width: 950px; margin: 0 auto; position: relative; border-radius: 12px !important; background: #fff;">
                    <div style="height: 6px; background: #00befa;"></div>
                    <div class="p-4 p-sm-5 text-start">
                        <!-- Header -->
                        <div class="row align-items-start mb-4">
                            <div class="col-sm-6 text-start mb-4 mb-sm-0">
                                <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="logo" style="max-height: 35px;">
                                <div class="mt-3">
                                    <h5 class="mb-0 fw-bold text-dark">{{ __($general->site_name) }}</h5>
                                    <p class="mb-0 text-muted small">A global search engine for<br>companies and individuals.</p>
                                </div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="d-inline-box">
                                    <h2 class="fw-bold mb-1 text-uppercase" style="letter-spacing: 2px; color: #333;">{{ __('Invoice') }}</h2>
                                    <p class="text-muted small mb-1">#{{ $invoice->invoice_number }}</p>
                                    <p class="text-muted small mb-0">{{ __('Date') }}: <span class="text-dark fw-bold">{{ showDateTime($invoice->issue_date, 'd M, Y') }}</span></p>
                                    <div class="mt-2">
                                        @if($invoice->status == 1)
                                            <span class="badge status-paid px-3 py-1 rounded-pill small">@lang('Paid')</span>
                                        @elseif($invoice->status == 2)
                                            <span class="badge status-cancelled px-3 py-1 rounded-pill small">@lang('Cancelled')</span>
                                        @else
                                            <span class="badge status-pending px-3 py-1 rounded-pill small">@lang('Pending')</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-5">

                        <!-- Info Sections -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-7">
                                <div class="p-4 rounded bg-light border border-light-subtle h-100">
                                    <h6 class="text-uppercase text-muted mb-3 small fw-bold">{{ __('Invoice To') }}</h6>
                                    <h5 class="mb-2 fw-bold text-dark">{{ __($invoice->user->fullname) }}</h5>
                                    <p class="mb-1 text-muted small"><i class="fas fa-envelope-open me-2"></i> {{ $invoice->user->email }}</p>
                                    @if($invoice->user->mobile)
                                        <p class="mb-0 text-muted small"><i class="fas fa-phone-alt me-2"></i> {{ $invoice->user->mobile }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="p-4 rounded border h-100 d-flex flex-column justify-content-center" style="background: #fdfdfd;">
                                    <h6 class="text-uppercase text-muted mb-2 small fw-bold">{{ __('Payment Summary') }}</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small">{{ __('Total Amount') }}</span>
                                        <span class="h5 mb-0 fw-bold text-dark">{{ $general->cur_sym }}{{ showAmount($invoice->total_amount) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                        <span class="text-muted small">{{ __('Balance Due') }}</span>
                                        <span class="fw-bold @if($balance > 0) text-danger @else text-success @endif">{{ $general->cur_sym }}{{ showAmount($balance) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Web Table -->
                        <div class="table-responsive mb-4 border rounded overflow-hidden">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-uppercase small text-muted fw-bold border-0">{{ __('Description') }}</th>
                                        <th class="text-center py-3 text-uppercase small text-muted fw-bold border-0">{{ __('Qty') }}</th>
                                        <th class="text-end py-3 text-uppercase small text-muted fw-bold border-0">{{ __('Price') }}</th>
                                        <th class="text-end pe-4 py-3 text-uppercase small text-muted fw-bold border-0 text-dark">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td class="ps-4 py-4">
                                            <div class="fw-bold text-dark">{{ __($item['name']) }}</div>
                                            <div class="text-muted small mt-1">
                                                <i class="fas fa-users me-1"></i> {{ __($item['description']) }}
                                                @if($item['dates'])
                                                <span class="ms-2"><i class="fas fa-clock me-1"></i> {{ $item['dates'] }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item['quantity'] }}</td>
                                        <td class="text-end text-muted">{{ $general->cur_sym }}{{ showAmount($item['price']) }}</td>
                                        <td class="text-end pe-4">
                                            <span class="fw-bold text-dark">{{ $general->cur_sym }}{{ showAmount($item['total']) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals Area -->
                        <div class="row justify-content-end mb-4">
                            <div class="col-md-5">
                                <div class="p-3 bg-light rounded border">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">{{ __('Subtotal') }}</span>
                                        <span class="fw-normal text-dark">{{ $general->cur_sym }}{{ showAmount($invoice->subtotal) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-2 border-top">
                                        <h6 class="fw-bold mb-0">{{ __('Grand Total') }}</h6>
                                        <h6 class="fw-bold mb-0 h5" style="color: #00befa;">{{ $general->cur_sym }}{{ showAmount($invoice->total_amount) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Web Footer -->
                        <div class="text-center pt-4 border-top">
                            <p class="text-muted small mb-0">{{ __('Thank you for your business!') }}</p>
                            <p class="text-muted extra-small mt-1 opacity-50">{{ __('Computer generated invoice. No signature required.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="text-center mt-5 mb-5 pb-5 no-print">
                    <button type="button" class="btn btn-outline-dark px-4 py-2 me-2 rounded-pill shadow-sm" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> {{ __('Print Preview') }}
                    </button>
                    <button type="button" id="download-pdf" class="btn btn-primary px-5 py-3 rounded-pill shadow" style="background-color: #00befa !important; border-color: #00befa !important; color: #fff !important;">
                        <i class="fas fa-file-pdf me-2"></i> {{ __('Download Professional PDF') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    @font-face { font-family: 'Tajawal'; src: url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap'); }
    body { font-family: 'Tajawal', sans-serif !important; }
    .invoice-card { font-family: 'Tajawal', sans-serif !important; }
    #pdf-container { font-family: 'Tajawal', Arial, sans-serif !important; }
    
    .status-pending { background-color: #fff3cd !important; color: #856404 !important; border: 1px solid #ffeeba !important; font-weight: bold !important; }
    .status-paid { background-color: #d1e7dd !important; color: #0f5132 !important; border: 1px solid #badbcc !important; font-weight: bold !important; }
    .status-cancelled { background-color: #f8d7da !important; color: #842029 !important; border: 1px solid #f5c2c7 !important; font-weight: bold !important; }

    @media print {
        body * { visibility: hidden !important; }
        #web-preview, #web-preview * { visibility: visible !important; }
        #web-preview { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; border: none !important; }
        .invoice-area { background: #fff !important; padding: 0 !important; }
        header, footer, .sidebar, .sidebar-menu, .navbar, .no-print, .breadcrumb, .breadcrumb-area { display: none !important; }
    }
</style>
@endpush

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    (function ($) {
        "use strict";
        $('#download-pdf').on('click', function () {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('pdf-container');
            const btn = $(this);
            btn.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...').prop('disabled', true);
            html2canvas(element, { scale: 2, useCORS: true, backgroundColor: '#ffffff' }).then(canvas => {
                const imgData = canvas.toDataURL('image/jpeg', 1.0);
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('Invoice-{{ $invoice->invoice_number }}.pdf');
                btn.html('<i class="fas fa-file-pdf me-2"></i> Download Professional PDF').prop('disabled', false);
            });
        });
    })(jQuery);
</script>
@endpush
@endsection
