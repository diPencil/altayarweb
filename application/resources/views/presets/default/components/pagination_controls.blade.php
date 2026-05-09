@if ($data->total() > 0)
    <div class="d-flex justify-content-between align-items-center mt-3 gap-3 flex-wrap bg--white p-3 radius--10 shadow-sm">
        <div class="pagination-info d-flex align-items-center gap-3">
             <div class="page-size-wrapper d-flex align-items-center gap-2">
                <span class="text-muted small">@lang('Page size'):</span>
                <select class="form-select form-select-sm rows-selector" style="width: auto; cursor: pointer; border: 1px solid #e9ecef; border-radius: 8px; padding: 0.25rem 2rem 0.25rem 1rem;">
                    <option value="20" @selected(request()->rows == 20 || (!request()->rows && getPaginate() == 20))>20</option>
                    <option value="50" @selected(request()->rows == 50)>50</option>
                    <option value="100" @selected(request()->rows == 100)>100</option>
                </select>
            </div>
            <span class="text-muted small fw-bold">
                {{ $data->firstItem() ?? 0 }} @lang('to') {{ $data->lastItem() ?? 0 }} @lang('of') {{ $data->total() }}
            </span>
        </div>
        <div class="pagination-links custom-pagination">
            {{ $data->appends(request()->all())->links() }}
        </div>
    </div>

    @push('script')
    <script>
        (function($){
            "use strict";
            $('.rows-selector').off('change').on('change', function(){
                let url = new URL(window.location.href);
                url.searchParams.set('rows', $(this).val());
                url.searchParams.set('page', 1);
                window.location.href = url.href;
            });
        })(jQuery);
    </script>
    @endpush

    @push('style')
    <style>
        .custom-pagination .pagination {
            margin-bottom: 0;
            gap: 5px;
        }
        .custom-pagination .page-link {
            border-radius: 8px !important;
            border: 1px solid #e9ecef;
            color: #6c757d;
            padding: 0.4rem 0.8rem;
        }
        .custom-pagination .page-item.active .page-link {
            background-color: var(--base-color);
            border-color: var(--base-color);
            color: #fff;
        }
        .custom-pagination .page-item.disabled .page-link {
            background-color: #f8f9fa;
        }
    </style>
    @endpush
@endif
