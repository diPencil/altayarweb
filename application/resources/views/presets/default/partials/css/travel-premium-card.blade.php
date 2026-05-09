{{-- Premium listing card (shared by offers hub + travel hub). Inline CSS fragment — parent wraps in <style>. --}}
        /* Travel offer card — premium (rounded, badges on media, pill CTAs) */
        .travel-offer-card {
            --travel-card-cyan: #38bdf8;
            --travel-card-discount-bg: #cffafe;
            --travel-card-soft-bg: #f1f5f9;
            background: #ffffff;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
            transition: box-shadow 0.2s ease;
            border: 1px solid rgba(15, 23, 42, 0.07);
        }

        .travel-offer-card:hover {
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.11);
        }

        .travel-offer-card__surface {
            flex: 1;
            min-height: 0;
        }

        .travel-offer-card__media {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16 / 11;
            background: #e8ecf1;
        }

        @media (min-width: 992px) {
            .travel-offer-card__media {
                aspect-ratio: 16 / 10;
            }
        }

        .travel-offer-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .travel-offer-card__placeholder {
            min-height: 100%;
            aspect-ratio: 16 / 11;
        }

        .travel-offer-card__badge-discount {
            position: absolute;
            top: 12px;
            inset-inline-end: 12px;
            z-index: 2;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            color: #0f172a;
            background: var(--travel-card-discount-bg);
            line-height: 1.2;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.12);
        }

        .travel-offer-card__badge-season {
            position: absolute;
            bottom: 12px;
            inset-inline-start: 12px;
            z-index: 2;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #0f172a;
            background: #ffffff;
            line-height: 1.2;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.1);
            text-transform: uppercase;
        }

        :lang(ar) .travel-offer-card__badge-season {
            text-transform: none;
            letter-spacing: normal;
        }

        .travel-offer-card__body {
            padding: 1rem 1.15rem 0.85rem;
            display: flex;
            flex-direction: column;
            flex: 1;
            text-align: start;
        }

        .travel-offer-card__title {
            margin: 0 0 0.55rem;
            font-family: var(--heading-font);
            font-size: 1.125rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0f172a;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .travel-offer-card__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.35rem;
            min-height: 1.5rem;
        }

        .travel-offer-card__loc {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            flex: 1;
            min-width: 0;
            font-size: 0.8125rem;
            color: #94a3b8;
        }

        [dir='rtl'] .travel-offer-card__loc {
            flex-direction: row-reverse;
        }

        .travel-offer-card__loc-icon {
            flex-shrink: 0;
            font-size: 1rem;
            color: #94a3b8;
        }

        .travel-offer-card__rating {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 0.8125rem;
            color: #0f172a;
        }

        .travel-offer-card__rating-star {
            color: #fbbf24;
            font-size: 0.9rem;
        }

        .travel-offer-card__dates {
            font-size: 0.8125rem;
            color: #94a3b8;
            padding-top: 0.15rem;
            padding-bottom: 0.65rem;
        }

        .travel-offer-card__dates .las {
            color: #94a3b8;
            margin-inline-end: 0.25rem;
        }

        .travel-offer-card__price-row {
            margin-top: 0;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(15, 23, 42, 0.08);
        }

        .travel-offer-card__price {
            font-variant-numeric: tabular-nums;
            font-size: 0.9375rem;
            color: #0f172a;
        }

        .travel-offer-card__price-old {
            color: #94a3b8;
            font-size: 0.8125rem;
            margin-inline-end: 0.45rem;
        }

        .travel-offer-card__price-current {
            font-weight: 700;
            font-size: 1.0625rem;
            color: var(--travel-card-cyan);
        }

        .travel-offer-card__price-unit {
            margin-inline-start: 0.35rem;
            font-size: 0.8125rem;
            font-weight: 400;
            color: #94a3b8;
        }

        .travel-offer-card__footer {
            padding: 0 1.15rem 1.15rem;
            border-top: none;
            margin-top: 0;
        }

        .travel-offer-card__actions {
            display: flex;
            flex-direction: row;
            align-items: stretch;
            gap: 0.5rem;
            padding-top: 0.85rem;
        }

        .travel-offer-card__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0.55rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none !important;
            border: none;
            transition: filter 0.15s ease, transform 0.15s ease;
            text-align: center;
            line-height: 1.2;
            white-space: nowrap;
        }

        .travel-offer-card__btn--primary {
            flex: 1 1 0;
            min-width: 0;
            background: var(--travel-card-cyan);
            color: #ffffff !important;
            box-shadow: 0 6px 18px rgba(56, 189, 248, 0.35);
        }

        .travel-offer-card__btn--primary:hover {
            color: #ffffff !important;
            filter: brightness(1.05);
        }

        .travel-offer-card__btn--secondary {
            flex: 1 1 0;
            min-width: 0;
            background: var(--travel-card-soft-bg);
            color: #0f172a !important;
        }

        .travel-offer-card__btn--secondary:hover {
            color: #0f172a !important;
            filter: brightness(0.97);
        }

        /* Booking.com Style Hotel Cards */
        .booking-hotel-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e7e7e7 !important;
            margin-bottom: 10px;
        }
        .booking-hotel-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }
        .ratio-1x1 {
            aspect-ratio: 1 / 1;
        }
        .wishlist-btn {
            color: #003580;
            transition: all 0.2s ease;
        }
        .wishlist-btn:hover {
            background-color: #f0f0f0 !important;
            color: #d41111;
        }
        
        /* Custom Slick Nav */
        .hotel-slick-slider {
            position: relative;
        }
        .hotel-slick-slider .slick-arrow {
            position: absolute;
            top: 35%;
            width: 32px;
            height: 32px;
            background: #fff !important;
            border-radius: 50% !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            color: #333 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border: none;
            padding: 0;
            font-size: 1.2rem !important;
            cursor: pointer;
        }
        .hotel-slick-slider .slick-prev {
            left: -10px;
        }
        .hotel-slick-slider .slick-next {
            right: -10px;
        }
        .hotel-slick-slider .slick-arrow:hover {
            background: #f0f0f0 !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .hotel-slick-slider .slick-disabled,
        .airline-slick-slider .slick-disabled {
            display: none !important;
        }
        
        .airline-slick-slider .slick-arrow {
            position: absolute;
            top: 40%;
            width: 32px;
            height: 32px;
            background: #fff !important;
            border-radius: 50% !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            color: #333 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border: none;
            padding: 0;
            font-size: 1.2rem !important;
            cursor: pointer;
        }
        .airline-slick-slider .slick-prev {
            left: -10px;
        }
        .airline-slick-slider .slick-next {
            right: -10px;
        }
        
        .airline-card:hover {
            transform: translateY(-5px);
            border-color: var(--travel-card-cyan) !important;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        }
        
        .ultra-small {
            font-size: 0.7rem;
        }
