{{--
    Travel offer card — reusable, RTL-aware (inherits dir from parent offers-hub).
    Premium layout: rounded card, discount + season pills on image, pill CTAs.
    Expects: $offer, $general, $ui, $loc, $pctOff, $finalPrice, $originalPrice,
            $offerDiscountAmt, $detailsUrl, $ratingDisplay, $perPersonLabel
--}}
@php
    $bookingUrl = route('listing.booking', [slug($offer->title), $offer->id]);
@endphp

<article class="travel-offer-card h-100 d-flex flex-column">
    <a href="{{ $detailsUrl }}" class="travel-offer-card__surface text-decoration-none text-body d-flex flex-column flex-grow-1">
        <div class="travel-offer-card__media">
            @if ($offer->image)
                <img src="{{ getImage(getFilePath('listingImage') . '/' . $offer->image) }}" alt=""
                    class="travel-offer-card__img w-100" loading="lazy" width="480" height="360">
            @else
                <div class="travel-offer-card__placeholder d-flex align-items-center justify-content-center">
                    <span class="text-muted small">@lang('No image')</span>
                </div>
            @endif

            @if ($pctOff !== null)
                <span class="travel-offer-card__badge-discount" dir="ltr">{{ str_replace(':pct', (string) $pctOff, $ui['discount_badge']) }}</span>
            @endif

            @if ($offer->listingType)
                <span class="travel-offer-card__badge-season" dir="auto">{{ __($offer->listingType->name) }}</span>
            @endif
        </div>

        <div class="travel-offer-card__body">
            <h3 class="travel-offer-card__title" dir="auto">{{ __($offer->title) }}</h3>

            @if ($offer->offerSummary())
                <div class="travel-offer-card__offer-inline travel-offer-card__offer-inline--body">
                    <i class="fa-solid fa-gift"></i>
                    <span>{{ $offer->offerSummary() }}</span>
                </div>
            @endif

            <div class="travel-offer-card__meta">
                @if ($loc !== '')
                    <span class="travel-offer-card__loc">
                        <i class="las la-map-marker travel-offer-card__loc-icon" aria-hidden="true"></i>
                        <span class="travel-offer-card__loc-text text-truncate" dir="auto">{{ __($loc) }}</span>
                    </span>
                @else
                    <span class="travel-offer-card__loc travel-offer-card__loc--empty"></span>
                @endif

                <span class="travel-offer-card__rating" dir="ltr">
                    <i class="las la-star travel-offer-card__rating-star" aria-hidden="true"></i>
                    <span class="travel-offer-card__rating-num tabular-nums">({{ $ratingDisplay }})</span>
                </span>
            </div>

            @if ($offer->end_date)
                <p class="travel-offer-card__dates mb-0">
                    <i class="las la-calendar-alt" aria-hidden="true"></i>
                    {{ $ui['expires'] }}:
                    <span class="tabular-nums" dir="ltr">{{ showDateTime($offer->end_date, 'd M Y') }}</span>
                </p>
            @endif

            <div class="travel-offer-card__price-row mt-auto">
                <div class="travel-offer-card__price" dir="ltr">
                    @if ($offerDiscountAmt > 0 && $originalPrice > 0)
                        <del class="travel-offer-card__price-old">{{ listingAmount($originalPrice) }} {{ __($offer->currency ?? 'USD') }}</del>
                    @endif
                    <span class="travel-offer-card__price-current">{{ listingPriceLabel($finalPrice, $offer->currency ?? 'USD') }}</span>
                    <span class="travel-offer-card__price-unit">{{ $perPersonLabel }}</span>
                </div>
            </div>
        </div>
    </a>

    <div class="travel-offer-card__footer">
        <div class="travel-offer-card__actions">
            <a href="{{ $bookingUrl }}" class="travel-offer-card__btn travel-offer-card__btn--primary">{{ $ui['cta_book_now'] }}</a>
            <a href="{{ $detailsUrl }}" class="travel-offer-card__btn travel-offer-card__btn--secondary">{{ $ui['cta_details'] }}</a>
        </div>
    </div>
</article>
