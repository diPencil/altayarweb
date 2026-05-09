{{-- Offers-hub chrome for More Travel (same markup/classes as Limited Offers). Root needs .offers-hub --}}
        .offers-hub {
            --offers-accent: hsl(var(--base));
            --offers-accent-soft: hsl(var(--base) / 0.12);
            --offers-dark: #0f172a;
        }

        .offers-hub__hero-visual {
            position: relative;
        }

        @media (min-width: 992px) {
            .offers-hub__hero-visual {
                display: flex;
                justify-content: flex-end;
                align-items: center;
            }

            [dir='rtl'] .offers-hub__hero-visual {
                justify-content: flex-start;
            }
        }

        .offers-hub__hero-stack {
            position: relative;
            padding-block-end: clamp(2.75rem, 7vw, 4.75rem);
        }

        .offers-hub__hero--ref {
            position: relative;
            overflow: visible;
            background: #ffffff;
        }

        .offers-hub__hero-photo-card {
            z-index: 1;
            isolation: isolate;
            max-width: min(540px, 100%);
            box-shadow: none;
            overflow: visible;
            background: transparent;
            margin-inline-end: -2.5rem;
        }

        @media (max-width: 991px) {
            .offers-hub__hero-photo-card {
                margin-inline-end: 0;
            }
        }

        .offers-hub__hero-img-shell {
            border-radius: 36px;
            isolation: isolate;
            border: 1px solid rgba(255, 255, 255, 0.16);
            box-shadow: none;
            width: fit-content;
        }

        .offers-hub__hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            border-radius: inherit;
            background: linear-gradient(
                185deg,
                rgba(15, 23, 42, 0.18) 0%,
                rgba(15, 23, 42, 0.05) 42%,
                rgba(15, 23, 42, 0.22) 78%,
                rgba(15, 23, 42, 0.48) 100%
            );
        }

        .offers-hub__hero-vignette {
            position: absolute;
            inset: 0;
            z-index: 3;
            pointer-events: none;
            border-radius: inherit;
            background:
                radial-gradient(ellipse 125% 92% at 50% 112%, rgba(15, 23, 42, 0.42) 0%, transparent 54%),
                radial-gradient(ellipse 78% 68% at 12% 10%, rgba(15, 23, 42, 0.2) 0%, transparent 50%);
        }

        .offers-hub__hero-badge {
            position: absolute;
            top: 1.25rem;
            left: 50%;
            z-index: 5;
            transform: translateX(-50%);
        }

        .offers-hub__floating-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.62rem 1.35rem;
            border-radius: 999px;
            font-size: 0.6875rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            background: #ffffff !important;
            color: #111827 !important;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: none;
            backdrop-filter: none;
        }

        .offers-hub__hero-spotlight--on-card {
            position: absolute;
            z-index: 35;
            width: min(300px, 88%);
            inset-inline-start: 0;
            bottom: -1.15rem;
            transform: translateX(calc(-1 * min(44%, 158px)));
        }

        [dir='rtl'] .offers-hub__hero-spotlight--on-card {
            inset-inline-start: auto;
            inset-inline-end: 0;
            transform: translateX(min(44%, 158px));
        }

        .travel-card-mini--spotlight {
            width: 100%;
            max-width: 308px;
            padding: 0.82rem !important;
            border-radius: 24px !important;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: none !important;
            background: #fff !important;
        }

        .travel-card-mini--spotlight:hover {
            transform: translateY(-5px);
            box-shadow: none !important;
        }

        .travel-card-mini__visual--spotlight {
            height: 148px !important;
            border-radius: 18px !important;
        }

        .travel-card-mini__visual--spotlight img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: inherit;
        }

        .travel-card-mini__content--spotlight {
            padding-inline: 0.35rem 1rem !important;
            padding-block-end: 1.05rem !important;
            padding-block-start: 0.45rem !important;
        }

        .travel-card-mini--spotlight .travel-card-mini__title {
            font-size: 1.0625rem;
            margin-bottom: 0.45rem;
        }

        .travel-card-mini--spotlight .travel-card-mini__loc {
            font-size: 0.8125rem;
            color: #64748b;
        }

        .travel-card-mini--spotlight .travel-card-mini__rating {
            font-size: 0.8125rem;
        }

        .offers-hub__hero .container {
            overflow: visible;
        }

        .offers-hub__hero-row {
            overflow: visible;
            flex-direction: row;
        }

        [dir='rtl'] .offers-hub__hero-row {
            flex-direction: row-reverse;
        }

        .offers-hub__hero-copy-inner {
            max-width: 34.5rem;
        }

        @media (min-width: 992px) {
            .offers-hub__hero-copy {
                padding-inline-end: clamp(0.5rem, 3vw, 2.25rem);
            }
        }

        .offers-hub__kicker {
            display: inline-block;
            font-size: 0.8125rem;
            font-weight: 800;
            letter-spacing: 0.11em;
            text-transform: uppercase;
            color: hsl(var(--base));
            margin-bottom: 0.85rem;
        }

        .offers-hub__title {
            font-family: var(--heading-font);
            font-size: clamp(1.85rem, 3.6vw, 2.75rem);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -0.03em;
            color: var(--offers-dark);
            margin-bottom: 1.05rem;
        }

        .offers-hub__lead {
            font-size: 1.0625rem;
            line-height: 1.7;
            max-width: 36rem;
            color: #64748b;
        }

        .offers-hub__steps {
            max-width: 36rem;
        }

        .offers-hub__step {
            margin-bottom: 1.28rem;
        }

        .offers-hub__step-num {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            background: hsl(var(--base) / 0.14);
            color: hsl(var(--base));
            font-weight: 800;
            font-size: 0.8125rem;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid hsl(var(--base) / 0.15);
            box-shadow: none;
        }

        .offers-hub__step-title {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--offers-dark);
            margin-bottom: 0.2rem;
        }

        .offers-hub__step-text {
            font-size: 0.9375rem;
            line-height: 1.55;
            color: #64748b;
        }

        .offers-hub__cta {
            padding: 0.92rem 2.15rem !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            border: none !important;
            color: #fff !important;
            background: linear-gradient(
                118deg,
                hsl(var(--base)) 0%,
                hsl(var(--base) / 0.92) 48%,
                hsl(192 78% 42%) 100%
            ) !important;
            box-shadow: none;
            transition: transform 0.22s ease, filter 0.22s ease;
        }

        .offers-hub__cta:hover {
            color: #fff !important;
            transform: translateY(-2px);
            filter: brightness(1.04);
            box-shadow: none;
        }

        .offers-hub__cta:focus-visible {
            outline: 3px solid hsl(var(--base) / 0.45);
            outline-offset: 3px;
        }

        .offers-hub__hero-img {
            display: block;
            width: 100%;
            max-width: 100%;
            object-fit: cover;
            object-position: center center;
            filter: saturate(1.05) contrast(1.02);
            border-radius: 0 !important;
        }

        .offers-hub__hero-placeholder {
            min-height: 360px;
            aspect-ratio: 16 / 10;
            background: radial-gradient(circle at 30% 28%, hsl(var(--base) / 0.25), transparent 62%),
                linear-gradient(135deg, hsl(215 24% 26%) 0%, hsl(217 36% 11%) 100%);
        }

        .travel-card-mini {
            background: #ffffff;
            border-radius: 20px;
            padding: 8px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            width: min(190px, 100%);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(15, 23, 42, 0.05);
        }

        .travel-card-mini:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
        }

        .travel-card-mini__visual {
            width: 100%;
            height: 118px;
            border-radius: 16px;
            overflow: hidden;
            background: #f1f5f9;
        }

        .travel-card-mini__visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .travel-card-mini__content {
            padding: 0 8px 10px;
        }

        .travel-card-mini__title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
            font-family: var(--heading-font);
            letter-spacing: -0.02em;
        }

        .travel-card-mini__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .travel-card-mini__loc {
            font-size: 0.65rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .travel-card-mini__loc i {
            font-size: 0.8rem;
        }

        .travel-card-mini__rating {
            font-size: 0.75rem;
            font-weight: 700;
            color: #111827;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .travel-card-mini__rating i {
            color: #fbbf24;
            font-size: 0.85rem;
        }

        .max-width-600 {
            max-width: 600px;
        }

        .offers-filter-bar {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .offers-filter-bar__label {
            display: block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-bottom: 0.25rem;
        }

        .offers-filter-bar__control {
            min-height: 48px;
            border-radius: 999px !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #ffffff !important;
            font-size: 0.875rem;
            color: #475569;
            transition: all 0.2s ease;
        }

        .offers-filter-bar__control:hover {
            border-color: #cbd5e1 !important;
        }

        .offers-filter-bar__control:focus {
            background-color: #fff !important;
            border-color: var(--offers-accent) !important;
            box-shadow: 0 0 0 4px var(--offers-accent-soft) !important;
        }

        .offers-hub__chip {
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
        }

        .offers-hub__chip.btn-outline-dark:hover {
            border-color: var(--offers-accent);
            color: var(--offers-accent);
            background: transparent;
        }

        .offers-hub__filters--pillnav {
            overflow-x: auto;
            flex-wrap: nowrap;
            scrollbar-width: thin;
        }

        @media (min-width: 992px) {
            .offers-hub__filters--pillnav {
                flex-wrap: wrap;
                overflow-x: visible;
                justify-content: center;
            }
        }

        .offers-hub__h2 {
            font-family: var(--heading-font);
            font-weight: 800;
            font-size: clamp(1.35rem, 2.2vw, 1.85rem);
            color: var(--offers-dark);
        }

        .offers-hub__saas-surface {
            background-color: #f8f9fb;
        }

        .travel-hub__dest-pill:hover {
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08) !important;
            transform: translateY(-2px);
        }

        .travel-hub__dest-badge {
            background: #cffafe;
            color: #0f172a;
        }

        @media (max-width: 991px) {
            .offers-hub__hero-spotlight--on-card {
                position: relative;
                inset-inline: auto;
                inset-inline-start: auto;
                inset-inline-end: auto;
                transform: none;
                bottom: auto;
                width: min(320px, 100%);
                margin-inline: auto;
                margin-block-start: -2.35rem;
            }

            .travel-card-mini--spotlight {
                max-width: none;
            }

            .offers-hub__hero-img {
                min-height: clamp(280px, 58vw, 420px);
                width: 100%;
                height: auto;
                aspect-ratio: auto;
            }
        }

        .scroll-top {
            box-shadow: none !important;
            filter: none !important;
        }

        .breadcrumb .bg--thumb-one,
        .breadcrumb .bg--thumb-two {
            display: none !important;
        }

        .breadcrumb {
            background: #ffffff !important;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        }
