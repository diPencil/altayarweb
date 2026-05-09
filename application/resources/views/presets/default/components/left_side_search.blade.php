@php
    $categories = App\Models\Category::with(['tour_packages'])
        ->where('status', 1)
        ->withCount('tour_packages')
        ->latest()
        ->get();
@endphp
<form>
    <div class="base--card radius--12 mb-4 z--1">
        <h6 class="fs--20">@lang('Search Title')</h6>
        <div class="input--group search--input d-flex flex-nowrap position-relative">
            <input type="text" class="form--control" id="searchValue" value="" placeholder="@lang('Search by title')">
        </div>
    </div>

    <div class="base--card radius--12 mb-4">
        <div class="range-slider" data-group-type="status">
            <div class="label">
                <h6>@lang('Price Range')</h6>
            </div>
            <div class="range-slider-box">
                <div class="slider-box mb-4 pb-2">
                    <div class="sliderr">
                        <div class="progresss"></div>
                    </div>
                    <div class="range-input">
                        <input type="range" class="range-min" min="10" max="10000" value="10"
                            step="100">
                        <input type="range" class="range-max" min="0" max="10000" value="10000"
                            step="100">
                    </div>
                </div>
                <div class="price-input d-flex justify-content-between align-items-center gap--24">


                    <h6 class=" mb-0 text--black7">{{ $general->cur_sym }}<span class="input-min min_price"></span></h6>
                    <h6 class=" mb-0 text--black7">{{ $general->cur_sym }}<span class="input-max min_price"></span></h6>

                </div>
            </div>
        </div>
    </div>



    <div class="base--card radius--12 mb-4 ai z--1">
        <h6>@lang('Rating')(5.0)</h6>
        @for ($i = 5; $i >= 1; $i--)
            <div class="check-item">
                <div class="form--check rating--search categories-search mb-2">
                    <input class="form-check-input" name="star[]" type="checkbox" value="{{ $i }}"
                        id="rating-{{ $i }}">
                    <label for="rating-{{ $i }}" class="form-check-label">
                        @for ($j = 1; $j <= $i; $j++)
                            <i class="fas fa-star"></i>
                        @endfor
                    </label>
                </div>
            </div>
        @endfor
    </div>


    <div class="base--card radius--12 mb-4 ai z--1">
        <h6>@lang('Categories')</h6>
        @foreach ($categories as $index => $category)
            <div
                class="item d-flex justify-content-between align-items-center flex-wrap location-item {{ $index >= 7 ? 'd-none' : '' }}">
                <div class="form--check mb-2">
                    <input class="form-check-input" type="checkbox" name="category[]" value="{{ $category->id }}"
                        id="category{{ $category->id }}">
                    <label class="form-check-label" for="category{{ $category->id }}">
                        {{ __($category->name) }}
                    </label>
                </div>
                <p class="fs--16 fw--500">{{ $category->tour_packages->count() }}</p>
            </div>
        @endforeach

        @if (count($categories) > 7)
            <div class="mt-3">
                <a href="javascript:void(0)" id="toggle-locations">
                    @lang('View More')
                </a>
            </div>
        @endif
    </div>


</form>

@push('script')
    <script>
        $(document).ready(function() {
            'use strict';

            const searchInput = $("#searchValue");

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }


            let priceMin = '';
            let priceMax = '';
            let priceChanged = false;


            const debouncedFilterData = debounce(filterData, 300);

            $('.range-min, .range-max').on('input', function() {
                priceMin = $('.range-min').val();
                priceMax = $('.range-max').val();
                priceChanged = true;
                debouncedFilterData();
            });

            // Other filter events
            $("input[name='star[]'], input[name='category[]']").on('change', function() {
                priceChanged = false;
                debouncedFilterData();
            });

            searchInput.on('keyup', function() {
                priceChanged = false;
                debouncedFilterData();
            });

            function filterData() {
                const star = $("input[name='star[]']:checked").map(function() {
                    return $(this).val();
                }).get();

                const searchKeyword = searchInput.val().trim();
                const categoryId = $("input[name='category[]']:checked").map(function() {
                    return $(this).val();
                }).get();

                const data = {
                    star,
                    search: searchKeyword,
                    categoryId
                };

                if (priceChanged) {
                    data.priceMin = priceMin;
                    data.priceMax = priceMax;
                }

                $('#spinn').removeClass('d-none');

                $.ajax({
                    url: "{{ route('tour.package.side.filter') }}",
                    method: 'GET',
                    data: data,
                    success: function(response) {
                        if (response.html) {
                            $('.tour-content').html(response.html);
                        }
                        $('#spinn').addClass('d-none');
                    },
                    error: function(error) {
                        console.error('Error filtering data:', error);
                        $('#spinn').addClass('d-none');
                    }
                });




            }
        });

        $(document).ready(function() {
            'use strict';

            $('#toggle-locations').on('click', function() {
                const button = $(this);
                const hiddenLocations = $('.location-item.d-none');

                if (hiddenLocations.length) {
                    hiddenLocations.removeClass('d-none');
                    button.text('@lang('View Less')');
                } else {
                    $('.location-item').slice(5).addClass('d-none');
                    button.text('@lang('View More')');
                }
            });

            // price range
  const rangeInput = document.querySelectorAll(".range-input input");
  const minDisplay = document.querySelector(".price-input .input-min");
  const maxDisplay = document.querySelector(".price-input .input-max");
  const progress = document.querySelector(".sliderr .progresss");

  let priceGap = 100;

  function updateSliderUI() {
    let minValue = parseInt(rangeInput[0].value);
    let maxValue = parseInt(rangeInput[1].value);

    minDisplay.textContent = minValue;
    maxDisplay.textContent = maxValue;


    progress.style.left = (minValue / rangeInput[0].max) * 100 + "%";
    progress.style.right = 100 - (maxValue / rangeInput[1].max) * 100 + "%";
  }

  rangeInput.forEach(input => {
    input.addEventListener("input", e => {
      let minValue = parseInt(rangeInput[0].value);
      let maxValue = parseInt(rangeInput[1].value);

      if (maxValue - minValue < priceGap) {
        if (e.target.classList.contains("range-min")) {
          rangeInput[0].value = maxValue - priceGap;
        } else {
          rangeInput[1].value = minValue + priceGap;
        }
      }
      updateSliderUI();
    });
  });

  updateSliderUI();


        });
    </script>
@endpush
