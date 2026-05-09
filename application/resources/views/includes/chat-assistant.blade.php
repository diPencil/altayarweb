@php
    $chatAssistantConfig = aiChatAssistantConfig();
    $chatSettings = json_decode($chatAssistantConfig['chat_settings'] ?? '{}', true) ?: [];
    $quickActions = $chatSettings['quick_actions'] ?? [
        ['label' => 'View offers', 'url' => '/offers/limited'],
        ['label' => 'View membership', 'url' => '/membership-details'],
        ['label' => 'Book now', 'url' => '/browse'],
    ];
    $assistantConfig = [
        'bootstrapUrl' => route('chat-assistant.bootstrap'),
        'messageUrl' => route('chat-assistant.message'),
        'pollUrl' => route('chat-assistant.poll'),
        'handoverUrl' => route('chat-assistant.handover'),
        'clearUrl' => route('chat-assistant.clear'),
        'pageUrl' => route('chat-assistant.page'),
        'title' => $chatSettings['title'] ?? __('Feliz AI Assistant'),
        'subtitle' => $chatSettings['subtitle'] ?? __('Powered by Pencil Studio'),
        'placeholder' => $chatSettings['placeholder'] ?? __('Type a message...'),
        'pollInterval' => (int) ($chatSettings['poll_interval'] ?? 4000),
        'quickActions' => $quickActions,
        'rtl' => is_rtl(),
        'locale' => app()->getLocale(),
        'bot_avatar' => asset('assets/presets/default/images/SuperFeliz.png'),
        'login_url' => route('user.login'),
        'welcomeMessage' => is_rtl()
            ? 'أهلاً بك. أنا مساعد فيليز الذكي، أقدر أساعدك في العروض، العضويات، الحجز، الدفع، أو أي استفسار عن AltayarVIP.'
            : 'Welcome. I am Feliz AI Assistant. I can help with offers, memberships, bookings, payments, or any AltayarVIP question.',
        'translations' => [
            'Book now' => __('Book now'),
            'View membership' => __('View membership'),
            'View offers' => __('View offers'),
            'Start new conversation' => __('Start new conversation'),
            'Are you sure you want to talk to a human, or continue with Feliz?' => __('Are you sure you want to talk to a human, or continue with Feliz?'),
            'Continue with Feliz' => __('Continue with Feliz'),
            'Talk to a human' => __('Talk to a human'),
            'Agent Support' => __('Agent Support'),
            'Feliz' => __('Feliz'),
            'User' => __('User'),
            'YOU' => __('YOU'),
            'AI' => __('AI'),
            'ADMIN' => __('ADMIN'),
            'SYSTEM' => __('SYSTEM'),
            'Ask about offers, memberships, cashback, or booking help...' => __('Ask about offers, memberships, cashback, or booking help...'),
            'Please log in to talk to a human agent.' => __('Please log in to talk to a human agent.'),
            'If no support agent joins within 1 minute, Feliz will resume helping you automatically.' => __('If no support agent joins within 1 minute, Feliz will resume helping you automatically.'),
            'Clear Chat' => __('Clear Chat'),
            'Are you sure you want to clear the entire conversation?' => __('Are you sure you want to clear the entire conversation?'),
            'This conversation is closed.' => __('This conversation is closed.'),
        ],
    ];
    $assistantConfig['welcomeMessage'] = is_rtl()
        ? html_entity_decode('&#1571;&#1607;&#1604;&#1575;&#1611; &#1576;&#1603;. &#1571;&#1606;&#1575; &#1605;&#1587;&#1575;&#1593;&#1583; &#1601;&#1610;&#1604;&#1610;&#1586; &#1575;&#1604;&#1584;&#1603;&#1610;&#1548; &#1571;&#1602;&#1583;&#1585; &#1571;&#1587;&#1575;&#1593;&#1583;&#1603; &#1601;&#1610; &#1575;&#1604;&#1593;&#1585;&#1608;&#1590;&#1548; &#1575;&#1604;&#1593;&#1590;&#1608;&#1610;&#1575;&#1578;&#1548; &#1575;&#1604;&#1581;&#1580;&#1586;&#1548; &#1575;&#1604;&#1583;&#1601;&#1593;&#1548; &#1571;&#1608; &#1571;&#1610; &#1575;&#1587;&#1578;&#1601;&#1587;&#1575;&#1585; &#1593;&#1606; AltayarVIP.', ENT_QUOTES, 'UTF-8')
        : 'Welcome. I am Feliz AI Assistant. I can help with offers, memberships, bookings, payments, or any AltayarVIP question.';
@endphp
<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
<style>
    .ai-chat-assistant-shell {
        position: fixed;
        right: 24px;
        inset-block-end: 24px;
        z-index: 1070;
        direction: ltr !important;
        font-family: var(--body-font, 'Tajawal'), sans-serif;
        display: flex;
        align-items: flex-end;
        gap: 16px;
    }

    .ai-chat-assistant-teaser {
        position: absolute;
        right: 96px;
        inset-block-end: 0;
        width: min(290px, calc(100vw - 140px));
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid rgba(34, 87, 191, 0.08);
        border-radius: 22px;
        box-shadow: 0 18px 48px rgba(20, 33, 61, 0.12);
        padding: 14px 16px;
        display: flex;
        gap: 12px;
        align-items: flex-start;
        transition: opacity .2s ease, transform .2s ease;
    }

    .ai-chat-assistant-teaser[dir="rtl"] {
        direction: rtl;
        flex-direction: row;
        text-align: right;
    }

    .ai-chat-assistant-teaser.hidden {
        display: none;
        opacity: 0;
        pointer-events: none;
        transform: translateY(8px) scale(.98);
    }

    .ai-chat-assistant-teaser-copy {
        flex: 1;
        min-width: 0;
    }

    .ai-chat-assistant-teaser-title {
        font-family: var(--heading-font, 'Tajawal'), sans-serif;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 700;
        color: #25324b;
        margin: 0 0 4px;
    }

    .ai-chat-assistant-teaser-text {
        font-size: 13px;
        line-height: 1.55;
        color: #5c677d;
        margin: 0;
    }

    .ai-chat-assistant-teaser-close {
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 50, 75, 0.9);
        color: #fff;
        flex-shrink: 0;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(20, 33, 61, 0.12);
    }

    .ai-chat-assistant-toggle {
        width: 84px;
        height: 84px;
        border: 0;
        border-radius: 50%;
        background: transparent;
        box-shadow: none;
        padding: 0;
        color: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .2s ease;
    }

    .ai-chat-assistant-toggle:hover {
        transform: translateY(-2px);
    }

    .ai-chat-assistant-toggle dotlottie-wc {
        width: 100%;
        height: 100%;
        display: block;
    }

    .ai-chat-assistant-panel {
        width: min(380px, calc(100vw - 120px));
        height: min(580px, calc(100vh - 60px));
        background: #ffffff;
        border: 1px solid rgba(34, 87, 191, 0.08);
        border-radius: 24px;
        box-shadow: 0 24px 70px rgba(20, 33, 61, 0.14);
        overflow: hidden;
        display: none;
        flex-direction: column;
    }

    .ai-chat-assistant-panel.open {
        display: flex;
    }

    .ai-chat-assistant-header {
        padding: 20px 20px 18px;
        background: linear-gradient(135deg, #0096D9 0%, #39bff9 100%);
        color: #fff;
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .ai-chat-assistant-header::after {
        content: '';
        position: absolute;
        inset: auto -20% -60% auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.12);
        pointer-events: none;
        z-index: 0;
    }

    .ai-chat-assistant-kicker {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 4px;
        background: #10b981;
        color: #fff;
        font-size: 9px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .ai-chat-assistant-header-top {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2px;
        position: relative;
        z-index: 2;
    }

    .ai-chat-assistant-header h3 {
        font-family: var(--heading-font, 'Tajawal'), sans-serif;
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: #fff;
    }

    [dir="rtl"] .ai-chat-assistant-header-top {
        flex-direction: row-reverse;
        justify-content: flex-end;
    }

    [dir="rtl"] .ai-chat-assistant-header h3,
    [dir="rtl"] .ai-chat-assistant-header p {
        direction: rtl;
        text-align: right;
        unicode-bidi: plaintext;
    }

    [dir="rtl"] .ai-chat-assistant-kicker {
        order: -1;
    }

    .ai-chat-assistant-header p {
        margin: 0;
        opacity: .9;
        font-size: 12px;
        max-width: 85%;
        color: rgba(255, 255, 255, 0.85);
        position: relative;
        z-index: 2;
    }

    .ai-chat-assistant-header-close {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border-radius: 50%;
        cursor: pointer;
        border: none;
        transition: background 0.2s;
        position: relative;
        z-index: 10;
    }

    .ai-chat-assistant-header-close:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    .ai-chat-assistant-header-actions {
        position: absolute;
        top: 16px;
        right: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        direction: ltr;
        flex-direction: row;
        z-index: 10;
        pointer-events: auto;
    }

    [dir="rtl"] .ai-chat-assistant-header-actions {
        right: auto;
        left: 16px;
        direction: rtl;
        flex-direction: row;
    }

    .ai-chat-assistant-header-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border-radius: 50%;
        cursor: pointer;
        border: none;
        transition: background 0.2s;
        padding: 0;
        position: relative;
        z-index: 5;
    }

    .ai-chat-assistant-header-btn:hover {
        background: rgba(255, 255, 255, 0.25);
    }
    
    .ai-chat-assistant-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        background: radial-gradient(circle at top right, rgba(57, 191, 249, .10), transparent 28%),
            linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .ai-chat-assistant-messages {
        flex: 1;
        overflow-y: auto;
        padding: 12px 16px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .ai-chat-assistant-intro {
        background: linear-gradient(180deg, rgba(34, 87, 191, 0.06), rgba(57, 191, 249, 0.05));
        border: 1px solid rgba(34, 87, 191, 0.10);
        border-radius: 20px;
        padding: 16px;
        margin-bottom: 4px;
        box-shadow: 0 14px 28px rgba(20, 33, 61, 0.05);
    }

    .ai-chat-assistant-intro-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(34, 87, 191, 0.10);
        color: #2257bf;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .ai-chat-assistant-intro-title {
        font-family: var(--heading-font, 'Tajawal'), sans-serif;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .ai-chat-assistant-intro-text {
        font-size: 13px;
        line-height: 1.7;
        color: #5c677d;
    }

    .ai-chat-assistant-message-wrapper {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
        width: 100%;
        max-width: 100%;
    }

    .ai-chat-assistant-message-wrapper.user {
        align-items: flex-end;
    }

    .ai-chat-assistant-message-wrapper.ai,
    .ai-chat-assistant-message-wrapper.admin,
    .ai-chat-assistant-message-wrapper.system {
        align-items: flex-start;
    }

    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message-wrapper.user {
        align-items: flex-start;
    }

    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message-wrapper.ai,
    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message-wrapper.admin,
    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message-wrapper.system {
        align-items: flex-end;
    }

    .ai-chat-assistant-message {
        display: table;
        width: auto !important;
        max-width: 85%;
        height: auto;
        min-height: 0;
        padding: 12px 18px;
        border-radius: 20px;
        white-space: pre-wrap;
        position: relative;
        flex-shrink: 0;
        line-height: 1.6;
        unicode-bidi: plaintext;
    }

    .ai-chat-assistant-message.user {
        background: #f1f5f9;
        color: #1e293b;
        border-radius: 18px 18px 4px 18px;
    }

    .ai-chat-assistant-message.ai,
    .ai-chat-assistant-message.admin,
    .ai-chat-assistant-message.system {
        background: #ffffff;
        color: #1e293b;
        border-radius: 18px 18px 18px 4px;
        border: 1px solid rgba(0,0,0,0.08);
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }

    .ai-chat-assistant-text {
        font-size: 13.5px;
        line-height: 1.5;
        margin: 0;
        padding: 0;
    }

    .ai-chat-assistant-link-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        max-width: 100%;
        margin-top: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #39bff9;
        color: #fff !important;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.2;
        text-decoration: none !important;
        box-shadow: 0 8px 18px rgba(57, 191, 249, 0.18);
    }

    .ai-chat-assistant-link-button + .ai-chat-assistant-link-button {
        margin-inline-start: 6px;
    }

    .ai-chat-assistant-link-button:hover {
        background: #1e9ff2;
        color: #fff !important;
    }

    .ai-chat-assistant-sender-info {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-weight: 700;
        font-size: 14px;
        color: #0096D9;
    }

    .ai-chat-assistant-sender-icon {
        width: 32px;
        height: 32px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ai-chat-assistant-meta {
        margin-top: 4px;
        font-size: 10px;
        opacity: .6;
        font-weight: 500;
        display: block;
        padding: 0 4px;
    }

    .ai-chat-assistant-typing {
        display: inline-flex;
        gap: 4px;
        align-items: center;
        padding: 12px 14px;
        border-radius: 18px;
        border: 1px solid rgba(34, 87, 191, 0.10);
        background: #fff;
        margin-inline-end: auto;
    }

    .ai-chat-assistant-typing span {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #64748b;
        animation: ai-chat-dot 1.2s infinite ease-in-out;
    }

    .ai-chat-assistant-typing span:nth-child(2) { animation-delay: .15s; }
    .ai-chat-assistant-typing span:nth-child(3) { animation-delay: .3s; }

    @keyframes ai-chat-dot {
        0%, 80%, 100% { transform: translateY(0); opacity: .45; }
        40% { transform: translateY(-4px); opacity: 1; }
    }

    .ai-chat-assistant-suggestions,
    .ai-chat-assistant-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 0 18px 12px;
    }

    .ai-chat-assistant-chip {
        border: 1px solid rgba(34, 87, 191, 0.16);
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        color: #2257bf;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
        box-shadow: 0 8px 18px rgba(20, 33, 61, 0.04);
    }

    .ai-chat-assistant-chip:hover {
        transform: translateY(-1px);
        background: linear-gradient(180deg, #ffffff 0%, #eaf2ff 100%);
    }

    .ai-chat-assistant-footer {
        border-top: 1px solid rgba(34, 87, 191, 0.10);
        background: #fff;
        padding: 14px;
    }

    .ai-chat-assistant-form {
        display: flex;
        flex-direction: row;
        gap: 8px;
        align-items: flex-end;
    }

    .ai-chat-assistant-input {
        flex: 1;
        min-height: 40px;
        max-height: 96px;
        border-radius: 16px;
        border: 1px solid rgba(34, 87, 191, 0.18);
        padding: 9px 12px;
        resize: none;
        outline: none;
        background: #f8fbff;
        color: #8f98a8;
        font-size: 13px;
        line-height: 1.5;
    }

    .ai-chat-assistant-input::placeholder {
        color: #a7b0bf;
        opacity: 1;
    }

    .ai-chat-assistant-send {
        width: 48px;
        height: 48px;
        border: 0;
        border-radius: 999px;
        padding: 0;
        font-size: 20px;
        font-weight: 700;
        font-family: var(--body-font, 'Tajawal'), sans-serif;
        text-align: center;
        text-transform: none;
        color: #ffffff;
        background-color: #39bff9 !important;
        transition: background-color 0.2s linear, box-shadow 0.2s linear, transform 0.2s linear;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        line-height: 1;
        box-shadow: none;
        cursor: pointer;
    }

    .ai-chat-assistant-send i {
        font-size: 18px;
        line-height: 1;
    }

    .ai-chat-assistant-send:hover,
    .ai-chat-assistant-send:active,
    .ai-chat-assistant-send:focus {
        background-color: #1e9ff2 !important;
        color: #ffffff !important;
        box-shadow: none;
        transform: translateY(-1px);
    }

    .ai-chat-assistant-input {
        direction: inherit;
        text-align: inherit;
    }

    .ai-chat-assistant-text {
        direction: inherit;
        text-align: inherit;
        unicode-bidi: plaintext;
    }

    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-input,
    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-text {
        text-align: right;
    }

    .ai-chat-assistant-sender-info {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-sender-info {
        flex-direction: row-reverse;
    }

    .ai-chat-assistant-message.user {
        margin-left: auto;
        margin-right: 0;
        text-align: right;
    }

    .ai-chat-assistant-panel[dir="ltr"] .ai-chat-assistant-message.user {
        margin-left: auto;
        margin-right: 0;
    }

    .ai-chat-assistant-message.ai,
    .ai-chat-assistant-message.admin,
    .ai-chat-assistant-message.system {
        margin-right: auto;
        margin-left: 0;
        text-align: left;
    }

    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message.ai,
    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message.admin,
    .ai-chat-assistant-panel[dir="rtl"] .ai-chat-assistant-message.system {
        text-align: left;
    }

    .ai-chat-assistant-handover {
        width: 100%;
        margin-bottom: 12px;
        border: 0;
        border-radius: 999px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #f97316 0%, #ef4444 100%);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
        cursor: pointer;
    }

    .ai-chat-assistant-confirmation {
        max-width: 92%;
        margin-inline-end: auto;
        background: linear-gradient(180deg, #fff8ec 0%, #ffffff 100%);
        border: 1px solid rgba(249, 115, 22, 0.18);
        box-shadow: 0 10px 24px rgba(20, 33, 61, 0.06);
    }

    .ai-chat-assistant-confirmation-text {
        margin-bottom: 10px;
        line-height: 1.55;
        color: #1f2937;
        font-weight: 600;
    }

    .ai-chat-assistant-confirmation-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ai-chat-assistant-confirmation-actions button {
        border: 0;
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .ai-chat-assistant-confirmation-cancel {
        background: #eef5ff;
        color: #2257bf;
    }

    .ai-chat-assistant-confirmation-accept {
        background: linear-gradient(135deg, #f97316 0%, #ef4444 100%);
        color: #fff;
    }

    .ai-chat-assistant-status {
        margin-top: 8px;
        font-size: 12px;
        color: #64748b;
    }

    @media (max-width: 640px) {
        .ai-chat-assistant-shell {
            right: 12px;
            inset-block-end: 12px;
            flex-direction: column;
        }

        .ai-chat-assistant-panel {
            width: calc(100vw - 24px);
            height: min(78vh, 720px);
            margin-bottom: 12px;
        }
    }

    @media (max-width: 768px) {
        .ai-chat-assistant-shell {
            right: 0;
            inset-block-end: 0;
            gap: 0;
            align-items: stretch;
        }

        .ai-chat-assistant-shell.is-mobile-fullscreen-open {
            inset: 0;
            width: 100vw;
            height: var(--ai-chat-assistant-mobile-height, 100dvh);
        }

        .ai-chat-assistant-shell.is-mobile-fullscreen-open .ai-chat-assistant-teaser,
        .ai-chat-assistant-shell.is-mobile-fullscreen-open .ai-chat-assistant-toggle {
            display: none;
        }

        .ai-chat-assistant-panel {
            width: 100vw;
            max-width: 100vw;
            height: var(--ai-chat-assistant-mobile-height, 100dvh);
            max-height: var(--ai-chat-assistant-mobile-height, 100dvh);
            border-radius: 0;
            border: 0;
            box-shadow: none;
            margin: 0;
            position: fixed;
            inset: 0;
            opacity: 0;
            transform: translateY(16px);
            pointer-events: none;
            transition: opacity 0.18s ease, transform 0.18s ease;
            padding-bottom: env(safe-area-inset-bottom);
            background: #ffffff;
        }

        #aiChatAssistantPanel.open {
            display: flex;
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: var(--ai-chat-assistant-mobile-height, 100dvh) !important;
            max-width: none !important;
            max-height: none !important;
            border-radius: 0 !important;
            z-index: 99999 !important;
            overflow: hidden !important;
            background: #fff !important;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        @supports not (height: 100dvh) {
            #aiChatAssistantPanel.open {
                height: 100vh !important;
            }
        }

        .ai-chat-assistant-header {
            position: sticky;
            top: 0;
            z-index: 3;
            padding: 16px 16px 14px;
            isolation: isolate;
        }

        .ai-chat-assistant-header-top {
            padding-inline-end: 72px;
        }

        .ai-chat-assistant-header-actions {
            top: 12px;
            right: 12px;
        }

        [dir="rtl"] .ai-chat-assistant-header-actions {
            left: 12px;
            right: auto;
        }

        .ai-chat-assistant-header-close {
            width: 36px;
            height: 36px;
        }

        .ai-chat-assistant-header-btn {
            width: 36px;
            height: 36px;
        }

        .ai-chat-assistant-header p {
            max-width: 100%;
        }

        .ai-chat-assistant-body {
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .ai-chat-assistant-messages,
        .feliz-chat-messages {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: 14px 14px 12px;
        }

        .ai-chat-assistant-intro {
            padding: 14px;
        }

        .ai-chat-assistant-message {
            max-width: 92%;
        }

        .ai-chat-assistant-suggestions,
        .ai-chat-assistant-actions {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            padding: 0 14px 12px;
            scrollbar-width: none;
        }

        .ai-chat-assistant-suggestions::-webkit-scrollbar,
        .ai-chat-assistant-actions::-webkit-scrollbar {
            display: none;
        }

        .ai-chat-assistant-chip {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .ai-chat-assistant-footer,
        .feliz-chat-composer,
        .ai-chat-input-area {
            position: sticky;
            bottom: 0;
            z-index: 3;
            padding: 12px 12px calc(12px + env(safe-area-inset-bottom));
            padding-bottom: calc(12px + env(safe-area-inset-bottom));
        }

        .ai-chat-assistant-handover {
            margin-bottom: 10px;
        }

        .ai-chat-assistant-form {
            gap: 10px;
        }

        .ai-chat-assistant-input {
            min-height: 46px;
            max-height: 120px;
            font-size: 16px;
        }

        .ai-chat-assistant-panel input,
        .ai-chat-assistant-panel textarea,
        .ai-chat-assistant-panel select,
        .ai-chat-assistant-panel button {
            font-size: 16px;
        }

        .ai-chat-assistant-send {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
        }

        .ai-chat-assistant-status {
            margin-top: 6px;
        }
    }

    body.ai-chat-assistant-scroll-locked {
        overflow: hidden;
        position: fixed;
        inset: 0;
        width: 100%;
        touch-action: none;
        overscroll-behavior: none;
    }

    body.chat-mobile-open {
        overflow: hidden !important;
    }

    body.chat-mobile-open .scroll-top,
    body.chat-mobile-open .scrollToTop,
    body.chat-mobile-open .back-to-top {
        display: none !important;
        pointer-events: none !important;
    }

    .swal2-container.ai-chat-assistant-clear-swal-container {
        position: fixed !important;
        inset: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 1000000 !important;
        padding: 20px !important;
        pointer-events: auto !important;
    }

    .swal2-container.ai-chat-assistant-clear-swal-container .swal2-popup {
        width: min(340px, calc(100vw - 40px)) !important;
        max-width: min(340px, calc(100vw - 40px)) !important;
        max-height: calc(100dvh - 40px) !important;
        overflow-y: auto !important;
        margin: 0 !important;
    }

    .ai-chat-assistant-shell.is-page-mode {
        position: relative !important;
        inset: auto !important;
        right: auto !important;
        bottom: auto !important;
        width: 100% !important;
        gap: 0 !important;
        align-items: stretch !important;
        flex-direction: column !important;
    }

    .ai-chat-assistant-shell.is-page-mode .ai-chat-assistant-teaser,
    .ai-chat-assistant-shell.is-page-mode .ai-chat-assistant-toggle {
        display: none !important;
    }

    .ai-chat-assistant-shell.is-page-mode .ai-chat-assistant-panel {
        position: relative !important;
        inset: auto !important;
        width: 100% !important;
        max-width: 100% !important;
        height: 100% !important;
        max-height: none !important;
        flex: 1 1 auto !important;
        min-height: 0 !important;
        opacity: 1 !important;
        transform: none !important;
        pointer-events: auto !important;
        display: flex !important;
    }

    .ai-chat-assistant-shell.is-page-mode .ai-chat-assistant-panel.open {
        display: flex !important;
    }
</style>

<div class="ai-chat-assistant-shell {{ ($chatAssistantPageMode ?? false) ? 'is-page-mode' : '' }}" id="aiChatAssistantShell" dir="ltr">
    @unless($chatAssistantPageMode ?? false)
    <div class="ai-chat-assistant-teaser" id="aiChatAssistantTeaser" role="status" aria-live="polite" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
        <div class="ai-chat-assistant-teaser-copy">
            <div class="ai-chat-assistant-teaser-title">@lang('Live Support')</div>
            <p class="ai-chat-assistant-teaser-text">@lang('Fast answers for travel, offers, and memberships')</p>
        </div>
        <button type="button" class="ai-chat-assistant-teaser-close" id="aiChatAssistantTeaserClose" aria-label="@lang('Close message')">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endunless

    <div class="ai-chat-assistant-panel {{ ($chatAssistantPageMode ?? false) ? 'open' : '' }}" id="aiChatAssistantPanel" aria-hidden="{{ ($chatAssistantPageMode ?? false) ? 'false' : 'true' }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
        <div class="ai-chat-assistant-header">
            <div class="ai-chat-assistant-header-actions">
                <button type="button" class="ai-chat-assistant-header-btn" id="aiChatAssistantClear" title="@lang('Clear Chat')" aria-label="@lang('Clear Chat')">
                    <i class="fas fa-trash-alt"></i>
                </button>
                    <button type="button" class="ai-chat-assistant-header-btn ai-chat-assistant-header-close" id="aiChatAssistantHeaderClose" aria-label="@lang('Close Chat')">
                        <i class="fas fa-times"></i>
                    </button>
            </div>
            <div class="ai-chat-assistant-header-top">
                <h3 id="aiChatAssistantTitle">{{ is_rtl() ? 'مساعد فيليز الذكي' : __('Feliz AI Assistant') }}</h3>
                <div class="ai-chat-assistant-kicker">@lang('Online')</div>
            </div>
            <p id="aiChatAssistantSubtitle">{{ is_rtl() ? 'بواسطة بنسل ستوديو' : __('Powered by Pencil Studio') }}</p>
        </div>

        <div class="ai-chat-assistant-body">
            <div class="ai-chat-assistant-intro" id="aiChatAssistantIntro">
                <div class="ai-chat-assistant-intro-badge">@lang('Support Agent')</div>
                <div class="ai-chat-assistant-intro-title">@lang('How can I help you?') </div>
                <div class="ai-chat-assistant-intro-text">@lang('Ask me anything about our travel services.')</div>
            </div>
            <div class="ai-chat-assistant-messages" id="aiChatAssistantMessages"></div>
            <div class="ai-chat-assistant-suggestions" id="aiChatAssistantSuggestions"></div>
        </div>

        <div class="ai-chat-assistant-footer">
            <button type="button" class="ai-chat-assistant-handover" id="aiChatAssistantHandover">@lang('Talk to a human')</button>
            <form id="aiChatAssistantForm" class="ai-chat-assistant-form" autocomplete="off">
                <textarea id="aiChatAssistantInput" class="ai-chat-assistant-input" rows="1" maxlength="{{ (int) ($chatSettings['max_length'] ?? 2000) }}" placeholder="{{ $chatSettings['placeholder'] ?? __('Type a message...') }}"></textarea>
                <button type="submit" class="btn btn--primary pill ai-chat-assistant-send" aria-label="@lang('Send')"><i class="las la-paper-plane"></i></button>
            </form>
            <div class="ai-chat-assistant-status" id="aiChatAssistantStatus"></div>
        </div>
    </div>

    @unless($chatAssistantPageMode ?? false)
    <button type="button" class="ai-chat-assistant-toggle" id="aiChatAssistantToggle" aria-label="{{ $chatSettings['title'] ?? __('Feliz AI Assistant') }}" dir="ltr">
        <dotlottie-wc
            src="https://lottie.host/3193c5bf-38e9-4003-8f9e-c3bb6ec9299b/0LrFVparmY.lottie"
            autoplay
            loop>
        </dotlottie-wc>
    </button>
    @endunless
</div>

<script>
(function () {
    if (window.__aiChatAssistantInitialized) {
        return;
    }
    window.__aiChatAssistantInitialized = true;

    const chatAssistantPageMode = @json($chatAssistantPageMode ?? false);

    const assistantConfig = @json($assistantConfig);

    const shell = document.getElementById('aiChatAssistantShell');
    const teaser = document.getElementById('aiChatAssistantTeaser');
    const teaserClose = document.getElementById('aiChatAssistantTeaserClose');
    const panel = document.getElementById('aiChatAssistantPanel');
    const toggleButton = document.getElementById('aiChatAssistantToggle');
    const messagesNode = document.getElementById('aiChatAssistantMessages');
    const suggestionsNode = document.getElementById('aiChatAssistantSuggestions');
    const introNode = document.getElementById('aiChatAssistantIntro');
    const form = document.getElementById('aiChatAssistantForm');
    const input = document.getElementById('aiChatAssistantInput');
    const sendButton = form.querySelector('.ai-chat-assistant-send');
    const statusNode = document.getElementById('aiChatAssistantStatus');
    const handoverButton = document.getElementById('aiChatAssistantHandover');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const sessionStorageKey = 'aiChatAssistant.sessionKey';
    const lastMessageStorageKey = 'aiChatAssistant.lastMessageId';
    const teaserCooldownStorageKey = 'aiChatAssistant.teaserHiddenUntil';

    let sessionKey = window.localStorage.getItem(sessionStorageKey) || '';
    let lastMessageId = parseInt(window.localStorage.getItem(lastMessageStorageKey) || '0', 10) || 0;
    let isOpen = false;
    let isLoading = false;
    let isBootstrapping = false;
    let pollingTimer = null;
    let teaserTimer = null;
    let handoverPromptNode = null;
    let conversationStatus = 'open';
    const renderedMessageIds = new Set();
    let welcomeRendered = false;
    let bodyScrollY = 0;
    const mobileQuery = window.matchMedia('(max-width: 768px)');

    if (chatAssistantPageMode) {
        isOpen = true;
        shell.classList.add('is-page-mode');
        panel.classList.add('open');
        panel.setAttribute('aria-hidden', 'false');
    }

    function syncMobileViewportHeight() {
        if (!mobileQuery.matches) {
            document.documentElement.style.removeProperty('--ai-chat-assistant-mobile-height');
            return;
        }

        const viewportHeight = window.visualViewport?.height || window.innerHeight || document.documentElement.clientHeight;
        document.documentElement.style.setProperty('--ai-chat-assistant-mobile-height', `${Math.round(viewportHeight)}px`);
    }

    function updateMobileChatViewport() {
        if (chatAssistantPageMode) {
            return;
        }

        if (!panel || !mobileQuery.matches || !panel.classList.contains('open')) {
            if (panel) {
                panel.style.removeProperty('height');
                panel.style.removeProperty('top');
            }
            return;
        }

        if (window.visualViewport) {
            panel.style.height = `${Math.round(window.visualViewport.height)}px`;
            panel.style.top = `${Math.round(window.visualViewport.offsetTop)}px`;
        } else {
            panel.style.height = '100dvh';
            panel.style.top = '0';
        }
    }

    function focusComposer() {
        if (!input) {
            return;
        }

        try {
            input.focus({ preventScroll: true });
        } catch (error) {
            input.focus();
        }
    }

    function lockBodyScroll() {
        if (!mobileQuery.matches || document.body.classList.contains('ai-chat-assistant-scroll-locked')) {
            return;
        }

        const scroller = document.scrollingElement || document.documentElement;
        bodyScrollY = scroller?.scrollTop || window.scrollY || window.pageYOffset || 0;
        document.body.classList.add('ai-chat-assistant-scroll-locked');
        document.body.style.top = `-${bodyScrollY}px`;
        document.body.style.left = '0';
        document.body.style.right = '0';
    }

    function unlockBodyScroll() {
        if (!document.body.classList.contains('ai-chat-assistant-scroll-locked')) {
            return;
        }

        document.body.classList.remove('ai-chat-assistant-scroll-locked');
        document.body.style.top = '';
        document.body.style.left = '';
        document.body.style.right = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.inset = '';
        document.body.style.touchAction = '';
        document.body.style.overscrollBehavior = '';

        const scroller = document.scrollingElement || document.documentElement;
        if (scroller && typeof bodyScrollY === 'number' && bodyScrollY > 0) {
            scroller.scrollTop = bodyScrollY;
            document.body.scrollTop = bodyScrollY;
            document.documentElement.scrollTop = bodyScrollY;
        }
    }

    function setChatOpenState(nextOpen) {
        isOpen = nextOpen;
        panel.classList.toggle('open', isOpen);
        panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        shell.classList.toggle('is-mobile-fullscreen-open', isOpen && mobileQuery.matches);
        document.body.classList.toggle('chat-mobile-open', isOpen && mobileQuery.matches);

        if (mobileQuery.matches) {
            syncMobileViewportHeight();
            updateMobileChatViewport();
            if (isOpen) {
                lockBodyScroll();
            } else {
                unlockBodyScroll();
            }
        }
    }

    function getTeaserHiddenUntil() {
        return parseInt(window.localStorage.getItem(teaserCooldownStorageKey) || '0', 10) || 0;
    }

    function showTeaser() {
        if (!teaser || isOpen) {
            return;
        }

        teaser.classList.remove('hidden');
    }

    function hideTeaser() {
        if (!teaser) {
            return;
        }

        teaser.classList.add('hidden');
    }

    function scheduleTeaserReturn() {
        if (!teaser || isOpen) {
            return;
        }

        if (teaserTimer) {
            clearTimeout(teaserTimer);
            teaserTimer = null;
        }

        const hiddenUntil = getTeaserHiddenUntil();
        const delay = Math.max(hiddenUntil - Date.now(), 0);

        if (!delay) {
            showTeaser();
            return;
        }

        hideTeaser();
        teaserTimer = window.setTimeout(() => {
            if (isOpen) return;
            window.localStorage.removeItem(teaserCooldownStorageKey);
            showTeaser();
        }, delay);
    }

    function closeTeaserForCooldown() {
        window.localStorage.setItem(teaserCooldownStorageKey, String(Date.now() + 10000));
        scheduleTeaserReturn();
    }

    function __trans(key) {
        return assistantConfig.translations?.[key] || key;
    }

    function setStatus(message) {
        statusNode.textContent = __trans(message) || '';
    }

    function setPrimaryAction(isClosed) {
        if (!handoverButton) {
            return;
        }

        const label = isClosed ? __trans('Start new conversation') : __trans('Talk to a human');
        handoverButton.textContent = label;
        handoverButton.setAttribute('aria-label', label);
        handoverButton.title = label;
    }

    function stopPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    function setComposerDisabled(isDisabled) {
        if (input) {
            input.disabled = isDisabled;
        }

        if (sendButton) {
            sendButton.disabled = isDisabled;
        }

        if (isDisabled) {
            setStatus('This conversation is closed.');
        } else if (conversationStatus !== 'closed') {
            setStatus('');
        }
    }

    function syncConversationStatus(conversation) {
        if (!conversation) {
            return;
        }

        conversationStatus = conversation.status || conversationStatus;
        setPrimaryAction(conversationStatus === 'closed');

        if (conversationStatus === 'closed') {
            setComposerDisabled(true);
            stopPolling();
        } else {
            setComposerDisabled(false);
        }
    }

    function setHandoverButtonVisible(isVisible) {
        if (!handoverButton) {
            return;
        }

        handoverButton.style.display = isVisible ? '' : 'none';
    }

    function removeHandoverPrompt() {
        if (handoverPromptNode) {
            handoverPromptNode.remove();
            handoverPromptNode = null;
        }
    }

    function syncHandoverState(conversation) {
        if (conversation?.status === 'closed') {
            setPrimaryAction(true);
            setHandoverButtonVisible(true);
            return;
        }

        const isHumanState = conversation?.status === 'human_requested' || conversation?.chat_type === 'human' || !!conversation?.human_requested_at;

        if (isHumanState) {
            removeHandoverPrompt();
            setHandoverButtonVisible(false);

            if (conversation?.chat_type === 'human') {
                removeHandoverMessages();
            }

            return;
        }

        setHandoverButtonVisible(true);
    }

    function removeHandoverMessages() {
        if (!messagesNode) {
            return;
        }

        messagesNode.querySelectorAll('[data-handover-notice="1"]').forEach((node) => node.remove());
    }

    function showHandoverPrompt() {
        if (handoverPromptNode || !messagesNode) {
            return;
        }

        if (conversationStatus === 'closed') {
            startNewConversation();
            return;
        }

        setHandoverButtonVisible(false);

        const prompt = document.createElement('div');
        prompt.className = 'ai-chat-assistant-message system ai-chat-assistant-confirmation';
        prompt.innerHTML = `
            <div class="ai-chat-assistant-confirmation-text">${escapeText(__trans('Are you sure you want to talk to a human, or continue with Feliz?'))}</div>
            <div class="ai-chat-assistant-confirmation-actions">
                <button type="button" class="ai-chat-assistant-confirmation-cancel" data-confirm-cancel>${escapeText(__trans('Continue with Feliz'))}</button>
                <button type="button" class="ai-chat-assistant-confirmation-accept" data-confirm-accept>${escapeText(__trans('Yes, talk to a human'))}</button>
            </div>
        `;

        const cancelButton = prompt.querySelector('[data-confirm-cancel]');
        const acceptButton = prompt.querySelector('[data-confirm-accept]');

        cancelButton?.addEventListener('click', () => {
            removeHandoverPrompt();
            setHandoverButtonVisible(true);
            setStatus(@json(__('Continuing with Feliz.')));
        });

        acceptButton?.addEventListener('click', async () => {
            if (acceptButton.disabled) {
                return;
            }

            acceptButton.disabled = true;
            cancelButton && (cancelButton.disabled = true);
            setStatus(@json(__('Requesting a human agent...')));
            try {
                const succeeded = await requestHandover();
                if (!succeeded) {
                    setHandoverButtonVisible(true);
                }
            } finally {
                removeHandoverPrompt();
            }
        });

        handoverPromptNode = prompt;
        messagesNode.appendChild(prompt);
        hideIntro();
        scrollToBottom();
    }

    function scrollToBottom() {
        messagesNode.scrollTop = messagesNode.scrollHeight;
    }

    function escapeText(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatAssistantText(value) {
        const text = String(value ?? '')
            .replace(/\/memberships/g, '/membership-details')
            .replace(/\/bookings\/hotels/g, '/offers/limited')
            .replace(/\/bookings\/flights/g, '/more-travel');
            
        // Improved regex to handle various URL characters and labels better
        const markdownLinkPattern = /\[([^\]]+)\]\(([^)\s]+)\)/g;
        let result = '';
        let lastIndex = 0;
        let match;

        while ((match = markdownLinkPattern.exec(text)) !== null) {
            result += escapeText(text.slice(lastIndex, match.index));
            const label = match[1];
            const url = match[2];
            result += `<a class="ai-chat-assistant-link-button" href="${escapeText(url)}">${escapeText(label)}</a>`;
            lastIndex = match.index + match[0].length;
        }

        result += escapeText(text.slice(lastIndex));
        return result;
    }

    function renderMessage(message) {
        const messageId = parseInt(message.id || '0', 10) || 0;
        if (messageId && renderedMessageIds.has(messageId)) {
            return;
        }

        hideIntro();
        const bubble = document.createElement('div');
        const isUser = message.sender_type === 'user';
        bubble.className = `ai-chat-assistant-message-wrapper ${message.sender_type || 'ai'}`;
        
        let headerHtml = '';
        if (!isUser) {
            const senderLabel = message.sender_type === 'admin' ? __trans('Agent Support') : __trans('Feliz');
            const senderAvatarAlt = message.sender_type === 'admin' ? __trans('Agent Support') : __trans('Feliz');
            headerHtml = `
                <div class="ai-chat-assistant-sender-info">
                    <div class="ai-chat-assistant-sender-icon">
                        <img src="${assistantConfig.bot_avatar}" alt="${senderAvatarAlt}" style="width: 24px; height: 24px; object-fit: contain;">
                    </div>
                    <span>${escapeText(senderLabel)}</span>
                </div>
            `;
        }

        let metaHtml = '';
        if (message.created_at) {
            const time = new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            const senderTag = isUser ? __trans('User') : __trans((message.sender_type || 'ai').toUpperCase());
            metaHtml = `<div class="ai-chat-assistant-meta">${senderTag} · ${time}</div>`;
        }

        bubble.innerHTML = `
            ${headerHtml}
            <div class="ai-chat-assistant-message ${message.sender_type || 'ai'}">
                <div class="ai-chat-assistant-text">${isUser ? escapeText(message.message) : formatAssistantText(message.message)}</div>
            </div>
            ${metaHtml}
        `;
        bubble.dataset.messageId = message.id;
        messagesNode.appendChild(bubble);
        if (messageId) {
            renderedMessageIds.add(messageId);
        }
        lastMessageId = Math.max(lastMessageId, messageId);
        window.localStorage.setItem(lastMessageStorageKey, String(lastMessageId));

        if (message.sender_type === 'system' && (message?.metadata?.handover || message?.metadata?.handover_notice || message?.metadata?.handover_trigger)) {
            bubble.dataset.handoverNotice = '1';
        }
    }

    function renderWelcomeMessage() {
        if (welcomeRendered || !messagesNode) {
            return;
        }

        renderMessage({
            sender_type: 'ai',
            message: assistantConfig.welcomeMessage,
            created_at: new Date().toISOString()
        });
        welcomeRendered = true;
    }

    function showIntro() {
        if (introNode) {
            introNode.style.display = '';
        }
    }

    function hideIntro() {
        if (introNode) {
            introNode.style.display = 'none';
        }
    }

    function renderTyping() {
        const typing = document.createElement('div');
        typing.id = 'aiChatAssistantTyping';
        typing.className = 'ai-chat-assistant-typing';
        typing.innerHTML = '<span></span><span></span><span></span>';
        messagesNode.appendChild(typing);
        scrollToBottom();
    }

    function removeTyping() {
        const typing = document.getElementById('aiChatAssistantTyping');
        if (typing) {
            typing.remove();
        }
    }

    function renderSuggestions(items) {
        suggestionsNode.innerHTML = '';
        (items || []).slice(0, 4).forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'ai-chat-assistant-chip';
            button.textContent = item;
            button.addEventListener('click', () => {
                sendMessage(item, { source: 'suggestion' });
            });
            suggestionsNode.appendChild(button);
        });
    }

    function renderActionButtons() {
        document.querySelectorAll('.ai-chat-assistant-action').forEach((button) => {
            button.addEventListener('click', () => {
                const url = button.dataset.url;
                if (url) {
                    window.location.href = url;
                }
            });
        });
    }

    function buildHeaders() {
        return {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        };
    }

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            headers: buildHeaders(),
            body: JSON.stringify(payload),
        });
        return response.json();
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        return response.json();
    }

    async function bootstrapAssistant(forceNew = false) {
        if (isBootstrapping) {
            return;
        }
        isBootstrapping = true;
        try {
            const bootstrapUrl = new URL(assistantConfig.bootstrapUrl, window.location.origin);
            if (sessionKey) {
                bootstrapUrl.searchParams.set('session_key', sessionKey);
            }
            bootstrapUrl.searchParams.set('locale', assistantConfig.locale);
            if (forceNew) {
                bootstrapUrl.searchParams.set('force_new', '1');
            }

            const response = await fetchJson(bootstrapUrl.toString());
            if (!response.enabled) {
                setStatus(@json(__('AI assistant is currently disabled.')));
                return;
            }

            if (response.conversation?.session_key) {
                sessionKey = response.conversation.session_key;
                window.localStorage.setItem(sessionStorageKey, sessionKey);
            }

            assistantConfig.title = response.assistant?.title || assistantConfig.title;
            assistantConfig.subtitle = response.assistant?.subtitle || assistantConfig.subtitle;
            assistantConfig.placeholder = response.assistant?.placeholder || assistantConfig.placeholder;

            // Header branding is locked in Blade for consistent branding
            // document.getElementById('aiChatAssistantTitle').textContent = assistantConfig.title;
            // document.getElementById('aiChatAssistantSubtitle').textContent = assistantConfig.subtitle;
            input.setAttribute('placeholder', __trans(assistantConfig.placeholder));

            messagesNode.innerHTML = '';
            renderedMessageIds.clear();
            welcomeRendered = false;
            renderWelcomeMessage();
            if ((response.messages || []).length) {
                hideIntro();
                (response.messages || []).forEach(renderMessage);
            } else {
                showIntro();
            }
            syncHandoverState(response.conversation);
            syncConversationStatus(response.conversation);
            renderSuggestions((response.assistant?.quick_actions || []).map((action) => __trans(action.label)));
            renderActionButtons();
            scrollToBottom();
            if (conversationStatus !== 'closed') {
                startPolling();
                setStatus('');
            }
        } catch (error) {
            messagesNode.innerHTML = '';
            renderedMessageIds.clear();
            welcomeRendered = false;
            showIntro();
            renderWelcomeMessage();
            setStatus('');
            console.error(error);
        } finally {
            isBootstrapping = false;
        }
    }

    async function sendMessage(messageText, options = {}) {
        const text = (messageText || input.value || '').trim();
        if (!text || isLoading) {
            return;
        }

        if (conversationStatus === 'closed') {
            setComposerDisabled(true);
            return;
        }

        if (!sessionKey) {
            await bootstrapAssistant();
        }

        isLoading = true;
        setStatus(@json(__('Sending...')));
        renderTyping();

        try {
            if (!sessionKey) {
                throw new Error('Missing session key after bootstrap');
            }

            const response = await postJson(assistantConfig.messageUrl, {
                session_key: sessionKey,
                message: text,
                source: options.source || 'composer',
                locale: assistantConfig.locale,
                url: window.location.href,
                referrer: document.referrer,
            });

            if (response?.status === 'closed') {
                syncConversationStatus({ status: 'closed' });
                setStatus(__trans(response.message || 'This conversation is closed.'));
                return;
            }

            if (response?.success === false && !response?.conversation) {
                setStatus(__trans(response.message || 'Unable to send your message right now.'));
                return;
            }

            if (response?.conversation?.session_key) {
                sessionKey = response.conversation.session_key;
                window.localStorage.setItem(sessionStorageKey, sessionKey);
            }

            input.value = '';
            messagesNode.innerHTML = '';
            renderedMessageIds.clear();
            welcomeRendered = false;
            renderWelcomeMessage();
            if ((response.messages || []).length) {
                hideIntro();
                (response.messages || []).forEach(renderMessage);
            } else {
                showIntro();
            }
            syncHandoverState(response.conversation);
            syncConversationStatus(response.conversation);
            renderSuggestions((response.suggested_replies || []).map((reply) => __trans(reply)));
            scrollToBottom();
            setStatus(response.conversation?.status === 'human_requested' ? @json(__('A human agent will join soon.')) : @json(__('Reply received.')));
        } catch (error) {
            console.error(error);
            setStatus(@json(__('Unable to send your message right now.')));
        } finally {
            removeTyping();
            isLoading = false;
        }
    }

    async function requestHandover() {
        if (!sessionKey) {
            await bootstrapAssistant();
        }

        try {
            setStatus(__trans('Requesting a human agent...'));
            const response = await fetch(assistantConfig.handoverUrl, {
                method: 'POST',
                headers: buildHeaders(),
                body: JSON.stringify({
                    session_key: sessionKey,
                    locale: assistantConfig.locale,
                }),
            });

            if (response.status === 401) {
                setStatus(__trans('Please log in to talk to a human agent.'));
                setTimeout(() => {
                    window.location.href = assistantConfig.login_url;
                }, 2000);
                return false;
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Handover failed');
            }

            messagesNode.innerHTML = '';
            renderedMessageIds.clear();
            welcomeRendered = false;
            if ((data.messages || []).length) {
                hideIntro();
                (data.messages || []).forEach(renderMessage);
            } else {
                showIntro();
            }
            syncHandoverState(data.conversation);
            syncConversationStatus(data.conversation);
            setStatus(__trans('A human support agent will join shortly.'));
            
            // Add the 1-minute notice
            renderMessage({
                sender_type: 'system',
                message: __trans('If no support agent joins within 1 minute, Feliz will resume helping you automatically.'),
                created_at: new Date().toISOString(),
                metadata: {
                    handover_notice: true
                }
            });

            scrollToBottom();
            return true;
        } catch (error) {
            console.error(error);
            setStatus(__trans('Unable to request handover right now.'));
            return false;
        }
    }

    async function startNewConversation() {
        stopPolling();
        window.localStorage.removeItem(sessionStorageKey);
        window.localStorage.removeItem(lastMessageStorageKey);
        sessionKey = '';
        lastMessageId = 0;
        conversationStatus = 'open';
        setComposerDisabled(false);
        setPrimaryAction(false);
        setStatus(@json(__('Starting a new conversation...')));
        messagesNode.innerHTML = '';
        renderedMessageIds.clear();
        welcomeRendered = false;
        showIntro();
        await bootstrapAssistant(true);
        if (isOpen) {
            if (mobileQuery.matches) {
                updateMobileChatViewport();
            } else {
                focusComposer();
            }
        }
    }

    async function pollMessages() {
        if (!isOpen || !sessionKey || conversationStatus === 'closed') {
            return;
        }

        try {
            const response = await fetchJson(`${assistantConfig.pollUrl}?session_key=${encodeURIComponent(sessionKey)}&last_id=${lastMessageId}`);
            if (response.messages?.length) {
                response.messages.forEach(renderMessage);
                scrollToBottom();
            }

            if (response.conversation) {
                syncConversationStatus(response.conversation);
            }

            if (conversationStatus === 'closed') {
                return;
            }
        } catch (error) {
            console.error(error);
        }
    }

    function startPolling() {
        stopPolling();

        pollingTimer = window.setInterval(pollMessages, assistantConfig.pollInterval || 4000);
    }

    async function clearChat() {
        const clearChatOptions = {
            title: __trans('Clear Conversation'),
            text: __trans('Are you sure you want to clear the entire conversation?'),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff2d20',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ __("Yes, Clear") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            reverseButtons: {{ is_rtl() ? 'true' : 'false' }}
        };

        if (mobileQuery.matches) {
            clearChatOptions.heightAuto = false;
            clearChatOptions.customClass = {
                container: 'ai-chat-assistant-clear-swal-container',
            };
            clearChatOptions.didOpen = () => {
                const popup = document.querySelector('.swal2-popup');
                if (popup) {
                    popup.style.width = 'min(340px, calc(100vw - 40px))';
                    popup.style.maxWidth = 'min(340px, calc(100vw - 40px))';
                    popup.style.maxHeight = 'calc(100dvh - 40px)';
                    popup.style.overflowY = 'auto';
                }
            };
        }

        const result = await Swal.fire(clearChatOptions);

        if (!result.isConfirmed) {
            return;
        }

        try {
            const response = await postJson(assistantConfig.clearUrl, {
                session_key: sessionKey
            });
            
            if (response?.status === 'closed') {
                syncConversationStatus({ status: 'closed' });
                setStatus(__trans(response.message || 'This conversation is closed.'));
                return;
            }

            if (response?.success === false) {
                setStatus(__trans(response.message || 'Unable to clear the conversation right now.'));
                return;
            }

            if (response.success) {
                messagesNode.innerHTML = '';
                renderedMessageIds.clear();
                welcomeRendered = false;
                conversationStatus = 'open';
                setComposerDisabled(false);
                showIntro();
                await bootstrapAssistant();
            }
        } catch (error) {
            console.error(error);
        }
    }

    toggleButton?.addEventListener('click', async (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (mobileQuery.matches && !chatAssistantPageMode) {
            window.location.href = assistantConfig.pageUrl;
            return;
        }

        setChatOpenState(!isOpen);
        if (isOpen) {
            hideTeaser();
        } else {
            scheduleTeaserReturn();
        }
        if (isOpen && !sessionKey) {
            await bootstrapAssistant();
        }
        if (isOpen) {
            if (mobileQuery.matches) {
                updateMobileChatViewport();
            } else {
                focusComposer();
            }
            scrollToBottom();
            syncConversationStatus({ status: conversationStatus });
        }
    });

    const headerClose = document.getElementById('aiChatAssistantHeaderClose');
    if (headerClose) {
        headerClose.addEventListener('click', (event) => {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            if (isMobile && chatAssistantPageMode && window.location.pathname.includes('/chat-assistant')) {
                event.preventDefault();
                event.stopPropagation();

                if (document.referrer && document.referrer.includes(window.location.origin) && window.history.length > 1) {
                    window.history.back();
                } else {
                    window.location.href = '/';
                }
                return;
            }

            toggleButton?.click();
        });
    }

    if (input) {
        input.addEventListener('focus', updateMobileChatViewport);
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        sendMessage();
    });

    handoverButton.addEventListener('click', showHandoverPrompt);
    document.getElementById('aiChatAssistantClear')?.addEventListener('click', clearChat);

    if (teaserClose) {
        teaserClose.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            closeTeaserForCooldown();
        });
    }

    if (teaser) {
        teaser.addEventListener('click', () => {
            toggleButton.click();
        });
    }

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    });

    document.querySelectorAll('.ai-chat-assistant-action').forEach((button) => {
        button.addEventListener('click', () => {
            const label = button.dataset.label || button.textContent || '';
            const url = button.dataset.url || '';
            if (url && label) {
                window.location.href = url;
            }
        });
    });

    scheduleTeaserReturn();
    bootstrapAssistant();

    syncMobileViewportHeight();
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', syncMobileViewportHeight);
        window.visualViewport.addEventListener('scroll', syncMobileViewportHeight);
        window.visualViewport.addEventListener('resize', updateMobileChatViewport);
        window.visualViewport.addEventListener('scroll', updateMobileChatViewport);
    } else {
        window.addEventListener('resize', syncMobileViewportHeight);
        window.addEventListener('resize', updateMobileChatViewport);
    }

    window.addEventListener('orientationchange', syncMobileViewportHeight);
    window.addEventListener('orientationchange', updateMobileChatViewport);
})();
</script>
