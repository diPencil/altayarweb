@extends($activeTemplate . 'layouts.chat_only')

@section('content')
@php
    $backLabel = is_rtl() ? 'رجوع' : __('Back');
@endphp

@push('style')
    <style>
        .chat-assistant-page {
            min-height: calc(100dvh - 1px);
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, rgba(241, 246, 255, 0.9) 0%, rgba(255, 255, 255, 1) 100%);
        }

        .chat-assistant-page__bar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 16px 0 12px;
        }

        .chat-assistant-page__back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            background: #fff;
            color: hsl(var(--base));
            font-weight: 700;
            text-decoration: none;
            border: 1px solid rgba(34, 87, 191, 0.12);
            box-shadow: 0 10px 28px rgba(20, 33, 61, 0.08);
        }

        .chat-assistant-page__back i {
            font-size: 16px;
        }

        .chat-assistant-page__body {
            flex: 1;
            min-height: 0;
            display: flex;
        }

        .chat-assistant-page__assistant {
            flex: 1;
            min-height: 0;
            display: flex;
        }

        .chat-assistant-page__assistant .ai-chat-assistant-shell.is-page-mode {
            flex: 1;
            min-height: 0;
        }

        .chat-assistant-page__assistant .ai-chat-assistant-shell.is-page-mode .ai-chat-assistant-panel {
            flex: 1;
            min-height: 0;
        }

        @media (max-width: 768px) {
            .chat-assistant-page {
                min-height: 100dvh;
            }

            .chat-assistant-page__bar {
                padding: 12px 0 10px;
            }

            .chat-assistant-page__back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

<div class="chat-assistant-page">
    <div class="container-fluid chat-assistant-page__bar">
        <a href="{{ route('home') }}" class="chat-assistant-page__back" id="chatAssistantBack" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
            <i class="fas fa-arrow-{{ is_rtl() ? 'right' : 'left' }}"></i>
            <span>{{ $backLabel }}</span>
        </a>
    </div>

    <div class="chat-assistant-page__body">
        <div class="container-fluid chat-assistant-page__assistant">
            @include('includes.chat-assistant', ['chatAssistantPageMode' => true])
        </div>
    </div>
</div>

<script>
    (function () {
        const backButton = document.getElementById('chatAssistantBack');
        if (!backButton) {
            return;
        }

        backButton.addEventListener('click', function (event) {
            if (document.referrer && window.history.length > 1) {
                event.preventDefault();
                window.history.back();
            }
        });
    })();
</script>
@endsection
