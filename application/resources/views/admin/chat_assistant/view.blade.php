@extends('admin.layouts.app')

@section('panel')
    @php
        $conversationData = $detail['conversation'];
        $messages = $detail['messages'];
        $status = strtolower($conversationData['status'] ?? '');
        $isClosed = $status === 'closed';
        $customer = $conversationData['user'] ?? [];
        $customerName = $customer['name'] ?? $conversationData['name'] ?? __('Guest');
        $customerEmail = $customer['email'] ?? $conversationData['email'] ?? '-';
        $customerPhone = $customer['phone'] ?? '-';
        $customerId = $customer['id'] ?? null;
        $customerUsername = $customer['username'] ?? '-';
        $customerStatus = $customer['status'] ?? null;
        $customerStatusLabel = $customerStatus === null ? __('Guest') : ((int) $customerStatus === 1 ? __('Active') : __('Inactive'));
        $startedAt = !empty($conversationData['created_at']) ? showDateTime($conversationData['created_at'], 'd M Y, h:i A') : '-';
        $joinedAt = !empty($customer['joined_at']) ? showDateTime($customer['joined_at'], 'd M Y, h:i A') : '-';
        $customerMembership = $customer['membership'] ?? '-';
        $customerWalletBalance = showAmount((float) ($customer['wallet_balance'] ?? 0));
        $customerCashback = showAmount((float) ($customer['cashback'] ?? 0));
        $customerPoints = (int) ($customer['points'] ?? 0);
        $customerTours = (int) ($customer['bookings']['tour'] ?? 0);
        $customerServices = (int) ($customer['bookings']['service'] ?? 0);
        $customerBookingsTotal = (int) ($customer['bookings']['total'] ?? ($customerTours + $customerServices));
        $badgeClass = $isClosed ? 'chat-live-status--closed' : 'chat-live-status--open';
        $heroClass = $isClosed ? 'is-closed' : 'is-open';

        $renderMessageContent = function (string $text): string {
            $text = str_replace(['/memberships', '/bookings/hotels', '/bookings/flights'], ['/membership-details', '/offers/limited', '/more-travel'], $text);
            $pattern = '/\[([^\]]+)\]\(([^)\s]+)\)/';
            $offset = 0;
            $result = '';

            while (preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                $matchText = $matches[0][0];
                $matchPosition = $matches[0][1];

                $result .= e(substr($text, $offset, $matchPosition - $offset));
                $result .= '<a class="chat-live-link-button" href="' . e($matches[2][0]) . '">' . e($matches[1][0]) . '</a>';
                $offset = $matchPosition + strlen($matchText);
            }

            $result .= e(substr($text, $offset));

            return nl2br($result);
        };
    @endphp

    <style>
        .chat-live-shell {
            display: grid;
            gap: 18px;
            height: calc(100vh - 210px);
            min-height: 680px;
            overflow: hidden;
        }

        .chat-live-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.85fr) minmax(320px, .85fr);
            gap: 18px;
            min-height: 0;
            height: 100%;
        }

        .chat-live-card {
            background: #fff;
            border-radius: 26px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.06);
            overflow: hidden;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .chat-live-thread__header {
            padding: 18px 20px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }

        .chat-live-thread__header.is-open {
            background: linear-gradient(135deg, #38bdf8, #0ea5e9);
        }

        .chat-live-thread__header.is-closed {
            background: linear-gradient(135deg, #38bdf8, #0284c7);
        }

        .chat-live-status--open {
            background: #16a34a;
            color: #fff !important;
        }

        .chat-live-status--closed {
            background: #ef4444;
            color: #fff !important;
        }

        .chat-live-thread__identity {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .chat-live-thread__avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex: 0 0 auto;
        }

        .chat-live-thread__name {
            font-size: 17px;
            font-weight: 800;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-live-thread__email {
            font-size: 13px;
            opacity: .92;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-live-thread__body {
            min-height: 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 24px 24px 18px;
            background: #f8fafc;
            overflow: hidden;
        }

        .chat-live-thread__messages {
            flex: 1;
            overflow-y: auto;
            display: grid;
            gap: 14px;
            align-content: start;
            padding-right: 8px;
        }

        .chat-live-message {
            max-width: min(72%, 640px);
            padding: 14px 16px;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
            border: 1px solid rgba(148, 163, 184, 0.18);
            direction: rtl;
            text-align: right;
            unicode-bidi: plaintext;
        }

        .chat-live-message.user {
            margin-right: auto;
            background: #eef2ff;
            color: #1e293b;
            border-bottom-left-radius: 6px;
        }

        .chat-live-message.admin,
        .chat-live-message.ai,
        .chat-live-message.system {
            margin-left: auto;
            background: #fff;
            color: #1e293b;
            border-bottom-right-radius: 6px;
        }

        .chat-live-message__meta {
            margin-top: 8px;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            text-align: right;
        }

        .chat-live-link-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            margin-top: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #39bff9;
            color: #fff !important;
            text-decoration: none !important;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            box-shadow: 0 8px 18px rgba(57, 191, 249, 0.18);
        }

        .chat-live-link-button:hover {
            background: #1e9ff2;
            color: #fff !important;
        }

        .chat-live-thread__empty {
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 16px;
            text-align: center;
        }

        .chat-live-thread__footer {
            border-top: 1px solid rgba(148, 163, 184, 0.18);
            padding: 14px 18px;
            text-align: center;
            color: #64748b;
            background: #fff;
            flex: 0 0 auto;
        }

        .chat-live-thread__composer {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
            padding: 18px;
            background: #fff;
            flex: 0 0 auto;
        }

        .chat-live-thread__composer form {
            display: flex;
            align-items: flex-end;
            gap: 12px;
        }

        .chat-live-thread__composer textarea {
            flex: 1;
            width: auto;
            height: 48px;
            min-height: 48px;
            max-height: 48px;
            resize: none;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, 0.32);
            padding: 10px 16px;
            box-sizing: border-box;
            outline: none;
        }

        .chat-live-thread__composer .btn {
            height: 48px;
            min-width: 92px;
            flex: 0 0 auto;
            white-space: nowrap;
        }

        [dir="rtl"] .chat-live-thread__composer form {
            flex-direction: row-reverse;
        }

        .chat-live-info {
            padding: 20px;
            min-height: 0;
            height: 100%;
            overflow: auto;
        }

        .chat-live-info__title {
            margin: 0 0 18px;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .chat-live-info__item {
            margin-bottom: 12px;
            font-size: 15px;
            line-height: 1.6;
            color: #374151;
        }

        .chat-live-info__item strong {
            color: #111827;
        }

        .chat-live-info__stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .chat-live-info__stat {
            border: 1px solid rgba(148, 163, 184, 0.14);
            border-radius: 18px;
            background: linear-gradient(180deg, #fbfdff 0%, #f4f8ff 100%);
            padding: 14px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
        }

        .chat-live-info__stat-label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .chat-live-info__stat-value {
            display: block;
            font-size: 18px;
            line-height: 1.2;
            color: #0f172a;
            font-weight: 800;
        }

        .chat-live-info__section-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px;
        }

        .chat-live-info__divider {
            height: 1px;
            background: rgba(148, 163, 184, 0.16);
            margin: 18px 0;
        }

        @media (max-width: 992px) {
            .chat-live-grid {
                grid-template-columns: 1fr;
            }

            .chat-live-info__stats {
                grid-template-columns: 1fr;
            }

            .chat-live-thread__composer form {
                flex-direction: column;
                align-items: stretch;
            }

            [dir="rtl"] .chat-live-thread__composer form {
                flex-direction: column;
            }

            .chat-live-thread__composer .btn {
                width: 100%;
            }
        }
    </style>

    <div class="chat-live-shell">
        <div class="chat-live-grid">
            <div class="chat-live-card">
                <div class="chat-live-thread__header {{ $heroClass }}">
                    <div class="chat-live-thread__identity">
                        <div class="chat-live-thread__avatar">
                            <i class="las la-user"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="chat-live-thread__name">{{ $customerName }}</div>
                            <div class="chat-live-thread__email">{{ $customerEmail }}</div>
                        </div>
                    </div>
                    <span class="badge {{ $badgeClass }}">{{ $isClosed ? __('Closed') : __('Open') }}</span>
                </div>
                <div class="chat-live-thread__body" id="chatLiveThreadBody">
                    @if(count($messages))
                        <div class="chat-live-thread__messages" id="chatLiveThreadMessages">
                            @foreach($messages as $message)
                                <div class="chat-live-message {{ $message['sender_type'] }}">
                                    <div>{!! $renderMessageContent((string) $message['message']) !!}</div>
                                    <div class="chat-live-message__meta">
                                        @if($message['sender_type'] === 'user')
                                            @lang('User')
                                        @elseif($message['sender_type'] === 'admin')
                                            @lang('Admin')
                                        @elseif($message['sender_type'] === 'ai')
                                            @lang('AI')
                                        @else
                                            @lang('System')
                                        @endif
                                        &middot; {{ showDateTime($message['created_at'], 'd M Y, h:i A') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="chat-live-thread__empty">
                            @lang('No messages yet')
                        </div>
                    @endif
                </div>

                @if($isClosed)
                    <div class="chat-live-thread__footer">
                        @lang('This conversation is closed')
                    </div>
                @else
                    <div class="chat-live-thread__composer">
                        <form action="{{ route('admin.chat-assistant.reply', $conversationData['id']) }}" method="post" id="chatLiveReplyForm">
                            @csrf
                            <textarea name="message" placeholder="@lang('Write a reply...')" required id="chatLiveReplyMessage"></textarea>
                            <div class="text-end mt-3">
                                <button type="submit" class="btn btn--primary">@lang('Send')</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="chat-live-card">
                <div class="chat-live-info">
                    <h3 class="chat-live-info__title">@lang('Customer Info')</h3>

                    <div class="chat-live-info__stats">
                        <div class="chat-live-info__stat">
                            <span class="chat-live-info__stat-label">@lang('Trips')</span>
                            <span class="chat-live-info__stat-value">{{ $customerTours }}</span>
                        </div>
                        <div class="chat-live-info__stat">
                            <span class="chat-live-info__stat-label">@lang('Service bookings')</span>
                            <span class="chat-live-info__stat-value">{{ $customerServices }}</span>
                        </div>
                        <div class="chat-live-info__stat">
                            <span class="chat-live-info__stat-label">@lang('Total bookings')</span>
                            <span class="chat-live-info__stat-value">{{ $customerBookingsTotal }}</span>
                        </div>
                        <div class="chat-live-info__stat">
                            <span class="chat-live-info__stat-label">@lang('Wallet balance')</span>
                            <span class="chat-live-info__stat-value">{{ $customerWalletBalance }}</span>
                        </div>
                        <div class="chat-live-info__stat">
                            <span class="chat-live-info__stat-label">@lang('Cashback')</span>
                            <span class="chat-live-info__stat-value">{{ $customerCashback }}</span>
                        </div>
                        <div class="chat-live-info__stat">
                            <span class="chat-live-info__stat-label">@lang('Points')</span>
                            <span class="chat-live-info__stat-value">{{ $customerPoints }}</span>
                        </div>
                    </div>

                    <h4 class="chat-live-info__section-title">@lang('Account Details')</h4>
                    <div class="chat-live-info__item"><strong>@lang('Name'):</strong> {{ $customerName }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Username'):</strong> {{ $customerUsername }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Email'):</strong> {{ $customerEmail }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Phone'):</strong> {{ $customerPhone }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Membership'):</strong> {{ $customerMembership }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Account status'):</strong> {{ $customerStatusLabel }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Joined'):</strong> {{ $joinedAt }}</div>

                    <div class="chat-live-info__divider"></div>

                    <div class="chat-live-info__item"><strong>@lang('Started'):</strong> {{ $startedAt }}</div>
                    <div class="chat-live-info__item"><strong>@lang('Messages'):</strong> {{ count($messages) }}</div>

                    <div class="chat-live-info__divider"></div>
                </div>
            </div>
        </div>

        @if(!$isClosed)
        <div class="modal fade" id="CloseChatModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Close Session')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>@lang('Are you sure you want to close this conversation?')</p>
                    </div>
                    <div class="modal-footer">
                        <form method="post" action="{{ route('admin.chat-assistant.close', $conversationData['id']) }}">
                            @csrf
                            <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                            <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('breadcrumb-plugins')
        @if($customerId)
            <a href="{{ route('admin.users.detail', $customerId) }}" class="btn btn-sm btn--primary">
                <i class="las la-user"></i> @lang('View profile details')
            </a>
        @endif
        <a href="{{ route('admin.chat-assistant.index') }}" class="btn btn-sm btn--dark">
            <i class="las la-arrow-left"></i> @lang('All Chats')
        </a>
        @if($isClosed)
            <form method="post" action="{{ route('admin.chat-assistant.reopen', $conversationData['id']) }}" class="d-inline-block">
                @csrf
                <button type="submit" class="btn btn-sm btn--success">
                    <i class="las la-redo-alt"></i> @lang('Reopen Chat')
                </button>
            </form>
        @else
            <button type="button" class="btn btn-sm btn--danger" data-bs-toggle="modal" data-bs-target="#CloseChatModal">
                <i class="las la-times"></i> @lang('Close session')
            </button>
        @endif
    @endpush

    @push('script')
        <script>
            (function () {
                const form = document.getElementById('chatLiveReplyForm');
                const messageInput = document.getElementById('chatLiveReplyMessage');
                const body = document.getElementById('chatLiveThreadBody');
                let messages = document.getElementById('chatLiveThreadMessages');

                if (!form || !messageInput || !body) {
                    return;
                }

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function ensureMessagesNode() {
                    if (messages) {
                        return messages;
                    }

                    const emptyState = body.querySelector('.chat-live-thread__empty');
                    const wrapper = document.createElement('div');
                    wrapper.className = 'chat-live-thread__messages';
                    wrapper.id = 'chatLiveThreadMessages';

                    if (emptyState) {
                        emptyState.replaceWith(wrapper);
                    } else {
                        body.prepend(wrapper);
                    }

                    messages = wrapper;
                    return wrapper;
                }

                function appendAdminMessage(text, createdAt) {
                    const list = ensureMessagesNode();
                    const message = document.createElement('div');
                    message.className = 'chat-live-message admin';

                    const time = createdAt
                        ? new Date(createdAt).toLocaleString([], { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' })
                        : new Date().toLocaleString([], { year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' });

                    message.innerHTML = `
                        <div>${escapeHtml(text)}</div>
                        <div class="chat-live-message__meta">@lang('Admin') &middot; ${escapeHtml(time)}</div>
                    `;

                    list.appendChild(message);
                    list.scrollTop = list.scrollHeight;
                }

                form.addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const text = messageInput.value.trim();
                    if (!text) {
                        return;
                    }

                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
                        });

                        const payload = await response.json();

                        if (!response.ok || !payload.success) {
                            throw new Error(payload.message || @json(__('Unable to send reply')));
                        }

                        appendAdminMessage(text, new Date().toISOString());
                        messageInput.value = '';
                        messageInput.style.height = 'auto';
                    } catch (error) {
                        console.error(error);
                    } finally {
                        if (submitButton) {
                            submitButton.disabled = false;
                        }

                        const list = ensureMessagesNode();
                        list.scrollTop = list.scrollHeight;
                        messageInput.focus();
                    }
                });
            })();
        </script>
    @endpush

@endsection

