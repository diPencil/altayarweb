<?php

namespace App\Services;

use App\Models\ChatConversation;
use App\Models\Extension;
use App\Models\Listing;
use App\Models\MembershipPlan;
use App\Models\ServiceBooking;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiChatAssistantService
{
    public function extension(): ?Extension
    {
        return Extension::where('act', 'ai-chat-assistant')->first();
    }

    public function enabled(): bool
    {
        return (bool) optional($this->extension())->status;
    }

    public function config(): array
    {
        $extension = $this->extension();
        if (! $extension || ! $extension->shortcode) {
            return [];
        }

        return collect($extension->shortcode)->mapWithKeys(function ($item, $key) {
            return [$key => is_array($item) ? ($item['value'] ?? null) : ($item->value ?? null)];
        })->all();
    }

    public function chatSettings(): array
    {
        $settings = json_decode($this->config()['chat_settings'] ?? '{}', true);

        return is_array($settings) ? $settings : [];
    }

    public function title(): string
    {
        return $this->chatSettings()['title'] ?? 'Feliz AI';
    }

    public function subtitle(): string
    {
        return $this->chatSettings()['subtitle'] ?? 'Fast answers for travel, offers, and memberships';
    }

    public function placeholder(): string
    {
        return $this->chatSettings()['placeholder'] ?? 'Ask about offers, memberships, cashback, or booking help...';
    }

    public function quickActions(): array
    {
        return $this->chatSettings()['quick_actions'] ?? [
            ['label' => 'View offers', 'url' => '/offers/limited'],
            ['label' => 'View membership', 'url' => '/membership-details'],
            ['label' => 'Book now', 'url' => '/browse'],
        ];
    }

    public function maxMessageLength(): int
    {
        return (int) ($this->chatSettings()['max_length'] ?? 2000);
    }

    public function pollInterval(): int
    {
        return max(2000, (int) ($this->chatSettings()['poll_interval'] ?? 4000));
    }

    public function provider(): string
    {
        return Str::lower($this->config()['provider'] ?? 'feliz_ai');
    }

    public function model(): string
    {
        return $this->config()['model'] ?? 'feliz-ai';
    }

    public function apiModel(): string
    {
        $configuredModel = Str::lower(trim((string) $this->model()));

        if ($configuredModel === '' || in_array($configuredModel, ['feliz-ai', 'feliz_ai', 'default'], true)) {
            return 'gemini-2.5-flash';
        }

        return $configuredModel;
    }

    public function apiKey(): string
    {
        return trim((string) ($this->config()['api_key'] ?? ''));
    }

    public function isReady(): bool
    {
        return $this->enabled() && $this->provider() === 'feliz_ai' && $this->apiKey() !== '';
    }

    public function resolveConversation(array $attributes = [], bool $create = true): ?ChatConversation
    {
        $user = auth()->user();
        if ($user instanceof \App\Models\Admin) {
            $user = null;
        }

        $forceNew = (bool) ($attributes['force_new'] ?? false);
        $sessionKey = $attributes['session_key'] ?? session()->get('ai_chat_session_key');

        if ($forceNew) {
            $sessionKey = (string) Str::uuid();
            session()->put('ai_chat_session_key', $sessionKey);
        }

        // 1. Try finding by session_key
        if ($sessionKey) {
            $conversation = ChatConversation::where('session_key', $sessionKey)->first();

            if ($conversation) {
                // If we found it and user just logged in, update the user_id
                if ($user && ! $conversation->user_id) {
                    $conversation->update(['user_id' => $user->id]);
                }

                return $conversation;
            }
        }

        // 2. If not found by session key but user is logged in, try finding their latest existing conversation
        if ($user) {
            $conversation = ChatConversation::where('user_id', $user->id)->latest('last_message_at')->first();
            if ($conversation) {
                session()->put('ai_chat_session_key', $conversation->session_key);
                return $conversation;
            }
        }

        if (! $create) {
            return null;
        }

        // 3. Create new if still not found
        if (! $sessionKey) {
            $sessionKey = (string) Str::uuid();
            session()->put('ai_chat_session_key', $sessionKey);
        }

        $conversation = new ChatConversation();
        $conversation->user_id = $user?->id;
        $conversation->session_key = $sessionKey;
        $conversation->name = $attributes['name'] ?? $user?->fullname ?? 'Guest';
        $conversation->email = $attributes['email'] ?? $user?->email;
        $conversation->locale = $attributes['locale'] ?? app()->getLocale();
        $conversation->chat_type = $attributes['chat_type'] ?? 'hybrid';
        $conversation->status = 'open';
        $conversation->ai_enabled = $attributes['ai_enabled'] ?? true;
        $conversation->metadata = $attributes['metadata'] ?? [];
        $conversation->last_message_at = now();
        $conversation->save();

        return $conversation;
    }

    public function buildKnowledgeBase(ChatConversation $conversation): array
    {
        $config = $this->config();
        $locale = $conversation->locale ?? app()->getLocale();

        return [
            'static_knowledge' => trim(($config['static_knowledge'] ?? '') . "\n\n" . $this->getBusinessKnowledge($locale)),
            'knowledge_urls' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($config['knowledge_urls'] ?? '')) ?: []))),
            'membership_plans' => $this->getMembershipPlansKnowledge(),
            'offers' => $this->getOffersKnowledge(),
            'destinations' => $this->getDestinationsKnowledge(),
            'user_context' => $this->getUserContextKnowledge($conversation),
            'booking_process' => [
                'Find a trip, review the price and offer, submit booking details, complete payment if needed, and wait for confirmation.',
                'If the user wants a quicker answer, recommend the best offer or membership plan first.',
            ],
        ];
    }

    protected function getMembershipPlansKnowledge(): array
    {
        return MembershipPlan::query()
            ->where('status', 1)
            ->latest('id')
            ->take(8)
            ->get()
            ->map(fn($plan) => [
                'name' => $plan->name,
                'price' => (float) $plan->price,
                'duration_days' => (int) $plan->duration_days,
                'bonus_points' => (int) $plan->bonus_points,
                'benefits' => array_slice(array_filter((array) ($plan->benefits ?? [])), 0, 5),
            ])
            ->values()
            ->all();
    }

    protected function getOffersKnowledge(): array
    {
        return Listing::query()
            ->active()
            ->latest('id')
            ->take(8)
            ->get()
            ->map(fn($listing) => [
                'title' => $listing->title,
                'city' => $listing->city,
                'country' => $listing->country,
                'price' => (float) $listing->price,
                'offer' => $listing->offerSummary(),
                'url' => route('listing.details', [$listing->slug, $listing->id]),
            ])
            ->values()
            ->all();
    }

    protected function getDestinationsKnowledge(): array
    {
        return TourPackage::query()
            ->active()
            ->latest('id')
            ->take(8)
            ->get()
            ->map(fn($tourPackage) => [
                'title' => $tourPackage->title,
                'price' => (float) $tourPackage->price,
                'category' => $tourPackage->category?->name,
                'url' => route('tour.package.details', [slug($tourPackage->title), $tourPackage->id]),
            ])
            ->values()
            ->all();
    }

    protected function getUserContextKnowledge(ChatConversation $conversation): array
    {
        if (! $conversation->user_id) {
            return [];
        }

        $user = User::find($conversation->user_id);
        if (! $user) {
            return [];
        }

        return [
            'membership_points' => (int) $user->membership_points_balance,
            'cashback_balance' => (float) $user->cashback_balance,
            'memberships_count' => $user->memberships()->count(),
            'has_active_membership' => (bool) $user->hasActiveMembership(),
            'tour_bookings_count' => TourBooking::where('user_id', $user->id)->count(),
            'service_bookings_count' => $user->serviceBookings()->count(),
        ];
    }

    public function generateReply(ChatConversation $conversation, string $userMessage): array
    {
        \Log::info('AiChatAssistantService generateReply started', ['message' => $userMessage]);
        if ($this->isMembershipListRequest($userMessage)) {
            return $this->membershipListReply($conversation, $userMessage);
        }

        if ($this->isMembershipCompareRequest($userMessage)) {
            return $this->membershipCompareReply($conversation, $userMessage);
        }

        if ($this->isAboutAltayarQuestion($userMessage)) {
            return $this->aboutAltayarReply($conversation, $userMessage);
        }

        if ($this->isActivationQuestion($userMessage)) {
            return $this->activationReply($conversation, $userMessage);
        }

        if ($this->isCashbackQuestion($userMessage)) {
            return $this->cashbackReply($conversation, $userMessage);
        }

        if ($this->isPasswordQuestion($userMessage)) {
            return $this->passwordReply($conversation, $userMessage);
        }

        if ($this->isFlightQuestion($userMessage)) {
            return $this->flightReply($conversation, $userMessage);
        }

        if ($this->isIdentityQuestion($userMessage)) {
            return $this->identityReply($conversation, $userMessage);
        }

        if ($this->isContactQuestion($userMessage)) {
            return $this->contactReply($conversation, $userMessage);
        }

        if ($this->isAgentQuestion($userMessage)) {
            return $this->agentReply($conversation, $userMessage);
        }

        if ($this->isBenefitQuestion($userMessage)) {
            return $this->benefitsReply($conversation, $userMessage);
        }

        if ($this->isDestinationQuestion($userMessage)) {
            return $this->destinationsReply($conversation, $userMessage);
        }

        if ($this->isAppQuestion($userMessage)) {
            return $this->appReply($conversation, $userMessage);
        }

        if ($this->isFamilyTravelRequest($userMessage)) {
            return $this->familyTravelReply($conversation, $userMessage);
        }

        if ($this->isGroupTravelRequest($userMessage)) {
            return $this->groupTravelReply($conversation, $userMessage);
        }

        if ($this->isPaymentQuestion($userMessage)) {
            return $this->paymentReply($conversation, $userMessage);
        }

        if ($this->isSupportQuestion($userMessage)) {
            return $this->supportReply($conversation, $userMessage);
        }

        if ($this->isBookingStatusQuestion($userMessage)) {
            return $this->bookingStatusReply($conversation, $userMessage);
        }

        if ($this->isInvoiceQuestion($userMessage)) {
            return $this->invoiceReply($conversation, $userMessage);
        }

        if ($this->isCancelRefundQuestion($userMessage)) {
            return $this->cancelRefundReply($conversation, $userMessage);
        }

        if ($this->isWalletQuestion($userMessage)) {
            return $this->walletReply($conversation, $userMessage);
        }

        if ($this->isDestinationAdviceQuestion($userMessage)) {
            return $this->destinationAdviceReply($conversation, $userMessage);
        }

        if ($this->detectAssistantSubject($userMessage) === 'general' && $this->isCasualConversation($userMessage)) {
            return $this->casualReply($conversation, $userMessage);
        }

        if (! $this->isReady()) {
            \Log::info('AiChatAssistantService not ready, using fallback');
            return $this->fallbackReply($conversation, $userMessage, $this->buildKnowledgeBase($conversation));
        }

        $knowledge = $this->buildKnowledgeBase($conversation);
        $history = $conversation->messages()->latest('id')->take(10)->get()->reverse()->values()->map(function ($message) {
            return [
                'role' => in_array($message->sender_type, ['admin', 'ai', 'system'], true) ? 'model' : 'user',
                'parts' => [
                    ['text' => $message->message],
                ],
            ];
        })->all();

        $prompt = trim(($this->config()['system_prompt'] ?? '') . "\n\n" . $this->buildPromptAppendix($knowledge));
        $payload = [
            'system_instruction' => [
                'parts' => [
                    [
                        'text' => $prompt,
                    ],
                ],
            ],
            'contents' => array_merge($history, [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userMessage],
                    ],
                ],
            ]),
            'generation_config' => [
                'temperature' => 0.75,
                'max_output_tokens' => 1800,
                'response_mime_type' => 'application/json',
                'thinking_config' => [
                    'thinking_budget' => 256,
                ],
            ],
        ];

        $response = Http::timeout(30)->post('https://generativelanguage.googleapis.com/v1beta/models/' . $this->apiModel() . ':generateContent?key=' . urlencode($this->apiKey()), $payload);
        \Log::info('AiChatAssistantService API response', ['status' => $response->status(), 'body' => $response->body()]);

        if (! $response->successful()) {
            return $this->fallbackReply($conversation, $userMessage, $knowledge);
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text', '');
        $parsed = $this->decodeAssistantPayload($text);
        $messages = $this->normalizeAssistantMessages($parsed, $conversation, $userMessage);

        return [
            'reply' => $messages[0] ?? $this->fallbackReply($conversation, $userMessage)['reply'],
            'messages' => $messages,
            'suggested_replies' => array_values(array_filter((array) ($parsed['suggested_replies'] ?? []))),
            'handover_recommended' => (bool) ($parsed['handover_recommended'] ?? false),
            'raw' => $text,
        ];
    }

    public function fallbackReply(ChatConversation $conversation, string $userMessage, ?array $knowledge = null): array
    {
        $knowledge ??= $this->buildKnowledgeBase($conversation);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        if (! $this->isReady()) {
            $msg = $locale === 'ar'
                ? 'مساعد الذكاء الاصطناعي غير متوفر حالياً. يرجى التحدث مع موظف دعم بشري.'
                : 'AI assistant is currently unavailable. Please talk to a human support agent.';
            
            return [
                'reply' => $msg,
                'messages' => [$msg],
                'suggested_replies' => $locale === 'ar' ? ['تحدث مع موظف'] : ['Talk to a human'],
                'handover_recommended' => true,
            ];
        }

        $subject = $this->detectAssistantSubject($userMessage);

        return $this->smartFallbackReply($conversation, $userMessage, $knowledge, $locale, $subject);

        if ($locale === 'ar') {
            $messages = match ($subject) {
                'membership' => [
                    'أكيد، أقدر أوضح لك العضويات المتاحة.',
                    $this->bestMembershipLine($knowledge) ?? 'أرسل اسم العضوية أو الهدف منها وسأعطيك التفاصيل المناسبة.',
                ],
                'offer' => [
                    'تمام، هذه أحدث العروض المتاحة.',
                    $this->bestOfferLine($knowledge) ?? 'لو تحب أقدر أوجّهك لأفضل عرض حسب ميزانيتك أو وجهتك.',
                ],
                'booking' => [
                    'ممتاز، أقدر أساعدك في الحجز خطوة بخطوة.',
                    $this->bestBookingLine($knowledge),
                ],
                'points' => [
                    'أقدر أوضح لك النقاط والكاش باك.',
                    'لو عندك حساب مسجل أقدر أشرح لك كيف تستفيد منها داخل النظام.',
                ],
                default => [
                    $conversation->messages()->exists()
                        ? 'أكيد، أقدر أساعدك. قل لي ما الذي تبحث عنه وسأوجهك مباشرة.'
                        : 'أهلاً بك في AltayarVIP. أنا Feliz AI، مساعدك الشخصي.',
                    $conversation->messages()->exists()
                        ? 'يمكنني مساعدتك في العروض، العضويات، أو الحجز حسب احتياجك.'
                        : 'يمكنني مساعدتك في العروض، العضويات، أو الحجز. من فضلك أخبرني بما تحتاج إليه وسأقوم بخدمتك فوراً.',
                ],
            };
        } else {
            $messages = match ($subject) {
                'membership' => [
                    'Sure, I can help with the available membership plans.',
                    $this->bestMembershipLine($knowledge) ?? 'Send the plan name or your goal and I will give the right details.',
                ],
                'offer' => [
                    'Sure, here are the latest offers I can help you with.',
                    $this->bestOfferLine($knowledge) ?? 'I can also point you to the best offer for your budget or destination.',
                ],
                'booking' => [
                    'Sure, I can help you book step by step.',
                    $this->bestBookingLine($knowledge),
                ],
                'points' => [
                    'I can explain points and cashback.',
                    'If you have an account, I can guide you on how to use them in the system.',
                ],
                default => [
                    $conversation->messages()->exists()
                        ? 'Sure, I can help. Tell me what you are looking for and I will guide you directly.'
                        : 'I can help with offers, memberships, points, or booking.',
                    $conversation->messages()->exists()
                        ? 'I can help with offers, memberships, or booking based on what you need.'
                        : 'Ask me directly and I will answer based on the project data.',
                ],
            };
        }

        return [
            'reply' => $messages[0],
            'messages' => $messages,
            'suggested_replies' => $locale === 'ar'
                ? ['عرض العضويات', 'أفضل العروض', 'مساعدة في الحجز']
                : ['View memberships', 'Best offers', 'Help me book'],
            'handover_recommended' => false,
        ];
    }

    protected function smartFallbackReply(ChatConversation $conversation, string $userMessage, array $knowledge, string $locale, string $subject): array
    {
        $isArabic = $locale === 'ar';
        $dialect = $this->detectArabicDialect($userMessage);

        if ($subject === 'offer') {
            $offer = $knowledge['offers'][0] ?? null;

            if (is_array($offer)) {
                $title = $offer['title'] ?? ($isArabic ? 'عرض مميز' : 'Featured offer');
                $location = trim(implode(' - ', array_filter([$offer['city'] ?? null, $offer['country'] ?? null])));
                $price = isset($offer['price']) ? showAmount($offer['price']) : null;
                $offerText = $offer['offer'] ?? null;

                $line = $isArabic
                    ? $this->arabicByDialect($dialect, [
                        'eg' => "أحسن ترشيح عندي دلوقتي: {$title}" . ($location ? " في {$location}" : '') . ($price ? " بسعر {$price}" : '') . ($offerText ? " - {$offerText}" : '') . '.',
                        'gulf' => "أنسب ترشيح عندي الآن: {$title}" . ($location ? " في {$location}" : '') . ($price ? " بسعر {$price}" : '') . ($offerText ? " - {$offerText}" : '') . '.',
                        'msa' => "أنسب ترشيح عندي الآن: {$title}" . ($location ? " في {$location}" : '') . ($price ? " بسعر {$price}" : '') . ($offerText ? " - {$offerText}" : '') . '.',
                    ])
                    : "My best current pick: {$title}" . ($location ? " in {$location}" : '') . ($price ? " at {$price}" : '') . ($offerText ? " - {$offerText}" : '') . '.';

                $next = $isArabic
                    ? $this->arabicByDialect($dialect, [
                        'eg' => 'لو عايز أظبطهولك أكتر، قولّي الميزانية وعدد الأشخاص وميعاد السفر.',
                        'gulf' => 'إذا تبغى أضيق لك الخيارات، قل لي الميزانية وعدد الأشخاص وموعد السفر.',
                        'msa' => 'إذا أردت تضييق الخيارات، أخبرني بالميزانية وعدد الأشخاص وموعد السفر.',
                    ])
                    : 'To narrow it down, tell me your budget, number of travelers, and travel date.';

                return [
                    'reply' => $line,
                    'messages' => [$line, $next . "\n" . ($isArabic ? '[عرض كل العروض](/offers/limited)' : '[View all offers](/offers/limited)')],
                    'suggested_replies' => $isArabic ? $this->arabicByDialect($dialect, [
                        'eg' => ['ميزانيتي محددة', 'عايز عرض لشخصين', 'أفضل عروض الفنادق'],
                        'gulf' => ['ميزانيتي محددة', 'أبغى عرض لشخصين', 'أفضل عروض الفنادق'],
                        'msa' => ['ميزانيتي محددة', 'أريد عرضا لشخصين', 'أفضل عروض الفنادق'],
                    ]) : ['I have a budget', 'Offer for two', 'Best hotel offers'],
                    'handover_recommended' => false,
                ];
            }

            $message = $isArabic
                ? $this->arabicByDialect($dialect, [
                    'eg' => 'أقدر أساعدك تختار أحسن عرض، بس محتاج أعرف الوجهة أو الميزانية أو عدد الأشخاص.',
                    'gulf' => 'أقدر أساعدك تختار أفضل عرض، لكن أحتاج أعرف الوجهة أو الميزانية أو عدد الأشخاص.',
                    'msa' => 'أستطيع مساعدتك في اختيار أفضل عرض، لكن أحتاج معرفة الوجهة أو الميزانية أو عدد الأشخاص.',
                ])
                : 'I can help you choose the best offer, but I need your destination, budget, or number of travelers.';

            return [
                'reply' => $message,
                'messages' => [$message . "\n" . ($isArabic ? '[عرض العروض](/offers/limited)' : '[View offers](/offers/limited)')],
                'suggested_replies' => [],
                'handover_recommended' => false,
            ];
        }

        if ($subject === 'membership') {
            $message = $isArabic
                ? 'العضوية مفيدة لو بتسافر أو بتحجز بشكل متكرر: بتديك خصومات، نقاط مكافآت، ومزايا تختلف حسب مستوى العضوية.'
                : 'Membership is useful if you travel or book often: it gives discounts, reward points, and benefits depending on the plan level.';

            return [
                'reply' => $message,
                'messages' => [$message, $isArabic ? 'تقدر تعرض كل الباقات وتفتح تفاصيل كل عضوية من هنا: [عرض العضويات](/membership-details)' : 'You can compare all plans and open each membership here: [View memberships](/membership-details)'],
                'suggested_replies' => $isArabic ? ['عرض العضويات', 'أنسب عضوية لي', 'قارن الباقات'] : ['View memberships', 'Best plan for me', 'Compare plans'],
                'handover_recommended' => false,
            ];
        }

        if ($subject === 'booking') {
            $message = $isArabic
                ? 'أقدر أساعدك في الحجز، والأذكى نبدأ بثلاث تفاصيل: الوجهة، تاريخ السفر، وعدد المسافرين.'
                : 'I can help with booking. The smart start is three details: destination, travel date, and number of travelers.';

            return [
                'reply' => $message,
                'messages' => [$message . "\n" . ($isArabic ? '[ابدأ طلب السفر](/more-travel)' : '[Start travel request](/more-travel)')],
                'suggested_replies' => $isArabic ? ['عايز رحلة لشخصين', 'عندي موعد محدد', 'مش عارف أختار وجهة'] : ['Trip for two', 'I have dates', 'Help me choose'],
                'handover_recommended' => false,
            ];
        }

        if ($subject === 'points') {
            $message = $isArabic
                ? 'النقاط والكاش باك بتساعدك تستفيد أكتر من حجوزاتك وعضويتك، لكن أفضل طريقة استخدامها بتعتمد على رصيدك ونوع الحجز.'
                : 'Points and cashback help you get more value from bookings and membership, but the best use depends on your balance and booking type.';

            return [
                'reply' => $message,
                'messages' => [$message],
                'suggested_replies' => $isArabic ? ['اشرح النقاط', 'اشرح الكاش باك', 'إزاي أستخدمهم؟'] : ['Explain points', 'Explain cashback', 'How do I use them?'],
                'handover_recommended' => false,
            ];
        }

        if ($subject === 'activation') {
            $message = $isArabic
                ? 'لتفعيل حسابك، يرجى التأكد من اشتراكك في إحدى عضوياتنا. إذا كنت غير مفعل، يفضل التواصل مع الدعم مباشرة.'
                : 'To activate your account, please ensure you have an active membership. If you are inactive, we recommend contacting support directly.';

            return [
                'reply' => $message,
                'messages' => [$message],
                'suggested_replies' => $isArabic ? ['تحدث مع موظف', 'عرض العضويات'] : ['Talk to an agent', 'View memberships'],
                'handover_recommended' => true,
            ];
        }

        if ($subject === 'flight') {
            $message = $isArabic
                ? 'أقدر أساعدك في البحث عن رحلات الطيران. قل لي وجهتك وتاريخ السفر.'
                : 'I can help you search for flights. Please tell me your destination and travel dates.';

            return [
                'reply' => $message,
                'messages' => [$message . "\n" . ($isArabic ? '[طلب حجز طيران](/more-travel)' : '[Book a flight](/more-travel)')],
                'suggested_replies' => $isArabic ? ['أفضل الوجهات', 'تحدث مع موظف'] : ['Best destinations', 'Talk to an agent'],
                'handover_recommended' => false,
            ];
        }

        if ($subject === 'password') {
            $message = $isArabic
                ? 'لتغيير أو استعادة كلمة السر، استخدم رابط "نسيت كلمة السر" في صفحة الدخول أو الإعدادات.'
                : 'To change or reset your password, use the "Forgot Password" link on the login page or check your settings.';

            return [
                'reply' => $message,
                'messages' => [$message . "\n" . ($isArabic ? '[تغيير كلمة السر](/user/change-password)' : '[Change Password](/user/change-password)')],
                'suggested_replies' => $isArabic ? ['تحدث مع موظف'] : ['Talk to an agent'],
                'handover_recommended' => false,
            ];
        }

        $message = $conversation->messages()->exists()
            ? ($isArabic ? 'تمام، قل لي هدفك بالضبط وسأقترح عليك أفضل خطوة.' : 'Sure. Tell me your exact goal and I will suggest the best next step.')
            : ($isArabic ? 'أهلا بك. اسألني عن العروض، العضويات، الحجز، أو المدفوعات وسأوجهك مباشرة.' : 'Welcome. Ask me about offers, memberships, booking, or payments and I will guide you directly.');

        return [
            'reply' => $message,
            'messages' => [$message],
            'suggested_replies' => $isArabic ? ['أفضل العروض', 'عرض العضويات', 'مساعدة في الحجز'] : ['Best offers', 'View memberships', 'Booking help'],
            'handover_recommended' => false,
        ];
    }

    protected function buildPromptAppendix(array $knowledge): string
    {
        return trim(implode("\n\n", [
            'Business knowledge base:',
            'Static context: ' . ($knowledge['static_knowledge'] ?? 'N/A'),
            'Membership plans: ' . json_encode($knowledge['membership_plans'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'Offers: ' . json_encode($knowledge['offers'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'Destinations: ' . json_encode($knowledge['destinations'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'Booking process: ' . json_encode($knowledge['booking_process'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'Relevant URLs: ' . json_encode($knowledge['knowledge_urls'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'User context: ' . json_encode($knowledge['user_context'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'STRICT FORMATTING RULES:',
            '- NO bold text (**text**), NO italics, and NO headers (###). Use plain text only.',
            '- Use simple dashes (-) for lists.',
            '- Use Markdown ONLY for links: [Link Name](URL).',
            '- Always link to the relevant section when mentioning a service. For example, instead of just "hotels", say "[Hotels](/bookings/hotels)".',
            '- The membership page URL is /membership-details. Never use /memberships.',
            '- Hotel, offer, or exclusive offer links must go to /offers/limited. Never use /bookings/hotels.',
            '- Flight or travel request links must go to /more-travel. Never use /bookings/flights.',
            'LANGUAGE QUALITY:',
            '- If the user speaks Arabic, mirror the user dialect when clear: Egyptian Arabic for Egyptian wording, Gulf/Saudi Arabic for Gulf wording, otherwise use smooth modern Arabic.',
            '- Egyptian examples: "عايز", "إزاي", "فين", "معايا", "أنا وأصحابي". Reply naturally in Egyptian.',
            '- Gulf/Saudi examples: "أبي", "أبغى", "وش", "كيف", "وين", "مع أهلي", "أنا والشباب". Reply naturally in Gulf/Saudi Arabic.',
            '- Avoid machine-like or overly formal translation style.',
            'INTELLIGENCE AND STYLE:',
            '- Do not sound scripted. Never reuse the same opening sentence unless it genuinely fits.',
            '- Infer the user intent from the latest message and the conversation history before answering.',
            '- If the user asks a broad question, give one useful recommendation first, then ask one smart follow-up question.',
            '- If the user asks for options, compare briefly instead of listing generic marketing copy.',
            '- Prefer concise, specific advice over slogans. Mention prices, benefits, destinations, or next steps only when relevant.',
            '- Do not introduce yourself unless the user asks who you are or it is the first assistant message in the whole conversation.',
            'CASUAL CHAT:',
            '- If the user is greeting you, asking how you are, thanking you, joking, or making light small talk, answer naturally and briefly first. Do not force a sales pitch.',
            '- For casual Arabic such as "عامل ايه", "ازيك", "صباح الخير", or "مساء الخير", reply warmly in Arabic like a helpful assistant, then optionally offer help in one short sentence.',
            '- Introduce yourself only at the beginning of a new conversation or if the user asks who you are. Do not repeat "I am Feliz AI" in normal follow-up answers.',
            'Answer format: return valid JSON with keys reply, messages, suggested_replies, handover_recommended. The reply value must be the first real answer sentence. messages must be an array of 2 to 3 real answer strings in the same language as the user. Never include literal field names like "reply" or "messages" inside the messages array.',
            'Do not answer with only a vague acknowledgement. Use the knowledge base and project data to give a concrete answer.',
            'BUSINESS RULES:',
            '- NEVER invent prices or rules not in the knowledge base.',
            '- ALWAYS guide inactive users (no active membership) to contact support/admin for account activation.',
            '- ALWAYS explain that cashback is for platform credit/discounts and not for direct withdrawal unless confirmed by admin.',
            '- IF you do not know something, admit it and suggest talking to a human support agent.',
            '- IF a user asks for a human, immediately recommend handover.',
        ]));
    }

    protected function getBusinessKnowledge(string $locale = 'en'): string
    {
        if (Str::startsWith($locale, 'ar')) {
            return implode("\n", [
                'AltayarVIP هي منصة سفر فاخرة تقدم خدمات السفر، الحجوزات، العضويات، المزايا، الكاش باك، النقاط، المحفظة، الفواتير، والدعم.',
                'العضويات المتاحة: Silver, Gold, Platinum, VIP, Diamond, Business وغيرها من الباقات المهيئة في النظام.',
                'تفعيل الحساب: المستخدمين الذين ليس لديهم عضوية نشطة (المستخدمين غير النشطين) يجب عليهم التواصل مع الإدارة/الدعم لتفعيل حسابهم حسب العضوية المطلوبة.',
                'الكاش باك (Cashback): يستخدم كرصيد للعضويات أو السفر أو كمرجع للخصم. لا يمكن سحبه ككاش مباشرة من المحفظة إلا إذا كان النظام يدعم ذلك صراحة.',
                'رصيد المحفظة: منفصل عن الكاش باك. يستخدم للمدفوعات والإيداعات.',
                'النقاط (Points): نقاط ولاء ومكافآت لمزايا السفر.',
                'الحجوزات: تشمل الجولات، الرحلات الجوية، الإقامة/الفنادق، المطاعم، المقاهي، الكوبونات، والمواصلات.',
                'التأكيدات والاستردادات: يجب على الذكاء الاصطناعي عدم الوعد بتأكيد الحجز أو الاسترداد أو الدفع. دائماً وجه المستخدم للإدارة/الدعم لهذه الأمور.',
                'تصعيد الدعم: وجه المستخدم للدعم/الإدارة لتفعيل الحساب، مشاكل الدفع، مشاكل الحجز، أو الطلبات الخاصة.',
            ]);
        }

        return implode("\n", [
            'AltayarVIP is a premium travel membership platform providing travel services, bookings, memberships, benefits, cashback, points, wallet, invoices, and support.',
            'Memberships available: Silver, Gold, Platinum, VIP, Diamond, Business, and others configured in the system.',
            'Account Activation: Users with no active membership (inactive users) MUST contact admin/support to activate their account according to the required membership.',
            'Cashback: Used as membership/travel credit or discount reference. It is NOT a direct wallet withdrawal unless explicitly supported.',
            'Wallet Balance: Separate from cashback. Used for payments and deposits.',
            'Points: Loyalty/reward points for travel benefits.',
            'Bookings: Includes tours, flights, stays/accommodation, restaurants, cafes, coupons, and transportation.',
            'Confirmations & Refunds: AI must NOT promise confirmation, refunds, or payments. Always refer to admin/support for these.',
            'Support Escalation: Guide user to support/admin for account activation, payment issues, booking issues, or custom requests.',
        ]);
    }

    protected function isActivationQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);
        return Str::contains($normalized, [
            'تفعيل الحساب',
            'تفعيل حسابي',
            'كيف افعل',
            'كيف أفعل',
            'تنشيط الحساب',
            'حسابي غير نشط',
            'حسابي موقف',
            'ليه الحساب مقفول',
            'how to activate',
            'activate my account',
            'activation',
            'account inactive',
        ]);
    }

    protected function activationReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        if ($locale === 'ar') {
            $message = 'لتفعيل حسابك في AltayarVIP، لازم يكون عندك عضوية نشطة. لو حسابك لسه مش متفعل، يرجى التواصل مع الإدارة أو الدعم الفني لمراجعة طلبك وتفعيل العضوية المناسبة ليك.';
            $suggested = ['تحدث مع موظف', 'عرض العضويات', 'مشكلة في الدخول'];
        } else {
            $message = 'To activate your AltayarVIP account, you need an active membership. If your account is not yet active, please contact admin or support to review your request and activate the appropriate membership for you.';
            $suggested = ['Talk to an agent', 'View memberships', 'Login issue'];
        }

        return [
            'reply' => $message,
            'messages' => [$message],
            'suggested_replies' => $suggested,
            'handover_recommended' => true,
        ];
    }

    protected function isCashbackQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);
        return Str::contains($normalized, [
            'كاش باك',
            'كاشباك',
            'cashback',
            'cash back',
            'سحب الكاش باك',
            'كيف استخدم الكاش باك',
            'cashback withdrawal',
        ]);
    }

    protected function cashbackReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        if ($locale === 'ar') {
            $message = 'الكاش باك في AltayarVIP بيستخدم كرصيد إضافي لحجوزاتك أو لتجديد العضوية والخصومات، ومش متاح للسحب النقدي المباشر إلا في حالات خاصة بتحددها الإدارة.';
            $suggested = ['رصيدي كام؟', 'كيف أستخدمه؟', 'تحدث مع موظف'];
        } else {
            $message = 'Cashback in AltayarVIP is used as extra credit for your bookings, membership renewals, or discounts. It is not available for direct cash withdrawal unless specifically approved by admin.';
            $suggested = ['My balance?', 'How to use?', 'Talk to an agent'];
        }

        return [
            'reply' => $message,
            'messages' => [$message],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isPasswordQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);
        return Str::contains($normalized, [
            'كلمة السر',
            'كلمه السر',
            'الباسورد',
            'تغيير السر',
            'نسيت السر',
            'password',
            'reset password',
            'change password',
        ]);
    }

    protected function passwordReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        if ($locale === 'ar') {
            $message = 'تقدر تغير كلمة السر من إعدادات حسابك. لو نسيت كلمة السر، استخدم رابط "نسيت كلمة السر" في صفحة تسجيل الدخول عشان يوصلك كود التفعيل على الإيميل.';
            $link = '[تغيير كلمة السر](/user/change-password)';
            $suggested = ['مشكلة في الكود', 'تغيير الإيميل', 'تحدث مع موظف'];
        } else {
            $message = 'You can change your password from your account settings. If you forgot your password, use the "Forgot Password" link on the login page to receive a reset code via email.';
            $link = '[Change Password](/user/change-password)';
            $suggested = ['Code issue', 'Change email', 'Talk to an agent'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $link],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isFlightQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);
        return Str::contains($normalized, [
            'حجز طيران',
            'تذكرة طيران',
            'تذكره طيران',
            'رحلة جوية',
            'رحلة طيران',
            'book flight',
            'flight ticket',
            'flights',
        ]);
    }

    protected function flightReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        if ($locale === 'ar') {
            $message = 'أهلاً بك! لحجز رحلة طيران، يرجى تزويدي بالوجهة، التاريخ، وعدد المسافرين. هقدر أساعدك في العثور على أفضل العروض المتاحة فوراً.';
            $link = '[طلب حجز طيران](/more-travel)';
            $suggested = ['أفضل الوجهات', 'عروض الرحلات', 'تحدث مع موظف'];
        } else {
            $message = 'Welcome! To book a flight, please provide the destination, date, and number of passengers. I can help you find the best available offers immediately.';
            $link = '[Book Flight](/more-travel)';
            $suggested = ['Best destinations', 'Flight offers', 'Talk to an agent'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $link],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isCasualConversation(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'عامل ايه',
            'عامله ايه',
            'ازيك',
            'اخبارك',
            'ايه الاخبار',
            'كيف حالك',
            'صباح الخير',
            'مساء الخير',
            'هالو',
            'هاي',
            'hello',
            'hi',
            'how are you',
            'good morning',
            'good evening',
            'thanks',
            'thank you',
        ]);
    }

    protected function isMembershipListRequest(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'عرض العضويات',
            'العضويات المتاحه',
            'العضويات المتاحة',
            'عايز اشترك',
            'عايز اشتراك',
            'ابي اشترك',
            'ابغى اشترك',
            'ودي اشترك',
            'ابي عضويه',
            'ابي عضوية',
            'ابغى عضويه',
            'ابغى عضوية',
            'وش العضويات',
            'وش الباقات',
            'عندكم عضويات',
            'عندكم باقات',
            'ابي باقه',
            'ابي باقة',
            'ابغى باقه',
            'ابغى باقة',
            'اشترك في عضويه',
            'اشترك في عضوية',
            'عضويه تقدر تساعدني',
            'عضوية تقدر تساعدني',
            'view membership',
            'view memberships',
            'available memberships',
            'membership plans',
        ]);
    }

    protected function isMembershipCompareRequest(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'قارن الباقات',
            'قارن العضويات',
            'مقارنه الباقات',
            'مقارنة الباقات',
            'فرق الباقات',
            'الفرق بين الباقات',
            'وش الفرق بين الباقات',
            'وش الفرق بين العضويات',
            'اي باقه تناسبني',
            'اي باقة تناسبني',
            'اي عضويه تناسبني',
            'اي عضوية تناسبني',
            'وش الانسب لي',
            'وش الافضل لي',
            'compare plans',
            'compare memberships',
        ]);
    }

    protected function membershipCompareReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';
        $plans = MembershipPlan::where('status', 1)->orderBy('price', 'asc')->get();

        if ($plans->isEmpty()) {
            return $this->membershipListReply($conversation, $userMessage);
        }

        $lowest = $plans->first();
        $middle = $plans->skip(max(0, (int) floor($plans->count() / 2) - 1))->first() ?: $plans->first();
        $highest = $plans->sortByDesc('bonus_points')->first() ?: $plans->last();

        $name = fn ($plan) => $locale === 'ar' && $plan->name_ar ? $plan->name_ar : $plan->name;
        $button = fn ($plan) => '[' . $name($plan) . ' - ' . showAmount($plan->price) . '](/membership-details/' . $plan->id . ')';

        if ($locale === 'ar') {
            $dialect = $this->detectArabicDialect($userMessage);
            $intro = $this->arabicByDialect($dialect, [
                'eg' => 'المقارنة ببساطة: اختار حسب عدد مرات سفرك وحجم المزايا اللي محتاجها، مش بالسعر بس.',
                'gulf' => 'المقارنة ببساطة: اختار حسب معدل سفرك وحجم المزايا اللي تحتاجها، مو حسب السعر فقط.',
                'msa' => 'المقارنة ببساطة: اختر حسب معدل سفرك وحجم المزايا التي تحتاجها، وليس حسب السعر فقط.',
            ]);
            $advice = implode("\n", [
                $this->arabicByDialect($dialect, ['eg' => 'لو عايز بداية اقتصادية: ', 'gulf' => 'إذا تبغى بداية اقتصادية: ', 'msa' => 'إذا أردت بداية اقتصادية: ']) . $button($lowest),
                $this->arabicByDialect($dialect, ['eg' => 'لو بتسافر أنت وعيلتك أكتر من مرة في السنة: ', 'gulf' => 'إذا تسافر أنت وعائلتك أكثر من مرة في السنة: ', 'msa' => 'إذا كنت تسافر مع عائلتك أكثر من مرة في السنة: ']) . $button($middle),
                $this->arabicByDialect($dialect, ['eg' => 'لو عايز أعلى نقاط ومزايا: ', 'gulf' => 'إذا تبغى أعلى نقاط ومزايا: ', 'msa' => 'إذا أردت أعلى نقاط ومزايا: ']) . $button($highest),
            ]);
            $close = $this->arabicByDialect($dialect, [
                'eg' => 'لو قلتلي بتسافر كام مرة في السنة وميزانيتك، أحددلك أنسب باقة فورًا.',
                'gulf' => 'لو قلت لي عدد مرات سفرك في السنة وميزانيتك، أحدد لك أنسب باقة مباشرة.',
                'msa' => 'إذا أخبرتني بعدد مرات سفرك سنويا وميزانيتك، سأحدد لك أنسب باقة مباشرة.',
            ]);
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['بسافر مع عيلتي', 'عايز أعلى مزايا', 'عايز أقل سعر'],
                'gulf' => ['بسافر مع عائلتي', 'أبغى أعلى مزايا', 'أبغى أقل سعر'],
                'msa' => ['أسافر مع عائلتي', 'أريد أعلى مزايا', 'أريد أقل سعر'],
            ]);
        } else {
            $intro = 'Simple comparison: choose by how often you travel and the benefits you need, not price alone.';
            $advice = implode("\n", [
                'Best entry option: ' . $button($lowest),
                'For family travel more than once a year: ' . $button($middle),
                'For maximum points and benefits: ' . $button($highest),
            ]);
            $close = 'Tell me how often you travel yearly and your budget, and I will pick the best plan for you.';
            $suggested = ['I travel with family', 'Maximum benefits', 'Lowest price'];
        }

        return [
            'reply' => $intro,
            'messages' => [$intro, $advice, $close],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isFamilyTravelRequest(string $userMessage): bool
    {
        $message = Str::lower(trim($userMessage));
        $normalized = str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $message);

        return Str::contains($normalized, [
            'اسافر انا وعائلتي',
            'بسافر انا وعائلتي',
            'اسافر مع عائلتي',
            'بسافر مع عائلتي',
            'انا وعائلتي',
            'انا واهلي',
            'انا وعيالي',
            'انا واسرتي',
            'ابي اسافر مع اهلي',
            'ابغى اسافر مع اهلي',
            'نبغى نسافر عائله',
            'نبغى نسافر عائلة',
            'سفر عائلي',
            'مع العائله',
            'مع العائلة',
            'family travel',
            'travel with my family',
        ]);
    }

    protected function isGroupTravelRequest(string $userMessage): bool
    {
        $message = Str::lower(trim($userMessage));
        $normalized = str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $message);

        return Str::contains($normalized, [
            'اسافر انا واصحابي',
            'اسافر مع اصحابي',
            'بسافر انا واصحابي',
            'بسافر مع اصحابي',
            'انا واصحابي',
            'مع اصحابي',
            'مع صحابي',
            'انا والشباب',
            'انا والربع',
            'مع الشباب',
            'مع الربع',
            'ابي اسافر مع الشباب',
            'ابغى اسافر مع الشباب',
            'نبغى نسافر',
            'نبي نسافر',
            'رحله شباب',
            'رحلة شباب',
            'جروب',
            'group travel',
            'travel with friends',
            'with my friends',
        ]);
    }

    protected function groupTravelReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';
        $offer = Listing::query()->active()->latest('id')->first();

        if ($locale === 'ar') {
            $dialect = $this->detectArabicDialect($userMessage);
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'ينفع طبعا. لو السفر مع أصحابك، الأحسن نختار عرض مرن يناسب عددكم وميزانية الجروب بدل عضوية فردية على طول.',
                'gulf' => 'أكيد ينفع. لو السفر مع أصحابك، الأفضل نختار عرض مرن يناسب عددكم وميزانية المجموعة بدل عضوية فردية مباشرة.',
                'msa' => 'بالتأكيد يمكن ذلك. إذا كان السفر مع الأصدقاء، فالأفضل اختيار عرض مرن يناسب عددكم وميزانية المجموعة بدلا من عضوية فردية مباشرة.',
            ]);
            $recommendation = $offer
                ? $this->arabicByDialect($dialect, [
                    'eg' => 'ترشيحي المبدئي: [' . $offer->title . '](/offers/limited) لأنه ممكن يناسب الجروب، وبعدها نحدد التفاصيل حسب العدد والميعاد.',
                    'gulf' => 'ترشيحي المبدئي: [' . $offer->title . '](/offers/limited) لأنه ممكن يكون مناسب لمجموعة، وبعدها نحدد التفاصيل حسب العدد والموعد.',
                    'msa' => 'ترشيحي المبدئي: [' . $offer->title . '](/offers/limited) لأنه قد يكون مناسبا للمجموعة، ثم نحدد التفاصيل حسب العدد والموعد.',
                ])
                : $this->arabicByDialect($dialect, [
                    'eg' => 'ابدأ من صفحة العروض، وبعدها نحدد الأنسب حسب عددكم والوجهة: [عرض العروض](/offers/limited).',
                    'gulf' => 'ابدأ من صفحة العروض، وبعدها نحدد الأنسب حسب عددكم والوجهة: [عرض العروض](/offers/limited).',
                    'msa' => 'ابدأ من صفحة العروض، ثم نحدد الأنسب حسب عددكم والوجهة: [عرض العروض](/offers/limited).',
                ]);
            $question = $this->arabicByDialect($dialect, [
                'eg' => 'هتسافروا كام شخص، وعايزين وجهة داخل مصر ولا برا؟',
                'gulf' => 'كم شخص بتسافرون، وتبغون وجهة داخلية ولا خارجية؟',
                'msa' => 'كم شخصا ستسافرون، وهل تريدون وجهة داخلية أم خارجية؟',
            ]);
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['3 أو 4 أشخاص', 'أكتر من 5 أشخاص', 'عايز عروض مناسبة'],
                'gulf' => ['3 أو 4 أشخاص', 'أكثر من 5 أشخاص', 'أبغى عروض مناسبة'],
                'msa' => ['3 أو 4 أشخاص', 'أكثر من 5 أشخاص', 'أريد عروضا مناسبة'],
            ]);
        } else {
            $message = 'Yes, absolutely. For friends or group travel, the smart move is to pick a flexible offer based on group size and budget, not a single plan immediately.';
            $recommendation = $offer
                ? 'My first pick: [' . $offer->title . '](/offers/limited), then we can narrow it down by dates and group size.'
                : 'Start from current offers, then narrow by group size and destination: [View offers](/offers/limited).';
            $question = 'How many people are traveling, and do you want a local or international trip?';
            $suggested = ['3 or 4 people', 'More than 5', 'Show suitable offers'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $recommendation, $question],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function familyTravelReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';
        $plans = MembershipPlan::where('status', 1)->orderBy('price', 'asc')->get();
        $recommended = $plans->first(function ($plan) {
            return Str::contains(Str::lower($plan->name . ' ' . $plan->name_ar), ['platinum', 'بلاتين', 'البلاتينية']);
        }) ?: $plans->first(function ($plan) {
            return Str::contains(Str::lower($plan->name . ' ' . $plan->name_ar), ['diamond', 'ماس', 'الماسية']);
        }) ?: $plans->skip(max(0, (int) floor($plans->count() / 2) - 1))->first() ?: $plans->first();
        $planName = $recommended ? (($locale === 'ar' && $recommended->name_ar) ? $recommended->name_ar : $recommended->name) : null;

        if ($locale === 'ar') {
            $dialect = $this->detectArabicDialect($userMessage);
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'لو بتسافر أنت وعيلتك، الأحسن تختار عضوية متوسطة أو عالية لأن قيمتها بتبان في الخصومات، النقاط، وأولوية الدعم مع كل حجز.',
                'gulf' => 'لو تسافر أنت وعائلتك، الأفضل تختار عضوية متوسطة أو عالية لأن قيمتها تظهر في الخصومات، النقاط، وأولوية الدعم مع كل حجز.',
                'msa' => 'إذا كنت تسافر مع عائلتك، فالأفضل اختيار عضوية متوسطة أو عالية لأن قيمتها تظهر في الخصومات، النقاط، وأولوية الدعم مع كل حجز.',
            ]);
            $recommendation = $recommended
                ? $this->arabicByDialect($dialect, [
                    'eg' => 'ترشيحي المبدئي ليك: [' . $planName . ' - ' . showAmount($recommended->price) . '](/membership-details/' . $recommended->id . ') لأنها مناسبة للسفر العائلي المتكرر.',
                    'gulf' => 'ترشيحي المبدئي لك: [' . $planName . ' - ' . showAmount($recommended->price) . '](/membership-details/' . $recommended->id . ') لأنها مناسبة للسفر العائلي المتكرر.',
                    'msa' => 'ترشيحي المبدئي لك: [' . $planName . ' - ' . showAmount($recommended->price) . '](/membership-details/' . $recommended->id . ') لأنها مناسبة للسفر العائلي المتكرر.',
                ])
                : $this->arabicByDialect($dialect, [
                    'eg' => 'ابدأ بمقارنة العضويات المتاحة من هنا: [عرض العضويات](/membership-details).',
                    'gulf' => 'ابدأ بمقارنة العضويات المتاحة من هنا: [عرض العضويات](/membership-details).',
                    'msa' => 'ابدأ بمقارنة العضويات المتاحة من هنا: [عرض العضويات](/membership-details).',
                ]);
            $question = $this->arabicByDialect($dialect, [
                'eg' => 'بتسافروا كام مرة تقريبًا في السنة؟ مرة، مرتين، ولا أكتر؟',
                'gulf' => 'كم مرة تقريبًا تسافرون في السنة؟ مرة، مرتين، أم أكثر؟',
                'msa' => 'كم مرة تقريبا تسافرون في السنة؟ مرة، مرتين، أم أكثر؟',
            ]);
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['مرة في السنة', 'مرتين أو أكتر', 'عايز أقارن الباقات'],
                'gulf' => ['مرة في السنة', 'مرتين أو أكثر', 'أبغى أقارن الباقات'],
                'msa' => ['مرة في السنة', 'مرتين أو أكثر', 'أريد مقارنة الباقات'],
            ]);
        } else {
            $message = 'For family travel, a mid or high membership usually makes more sense because discounts, points, and support priority compound with every booking.';
            $recommendation = $recommended
                ? 'My initial pick: [' . $planName . ' - ' . showAmount($recommended->price) . '](/membership-details/' . $recommended->id . ') because it fits repeat family travel.'
                : 'Start by comparing the available memberships here: [View memberships](/membership-details).';
            $question = 'How often do you travel yearly: once, twice, or more?';
            $suggested = ['Once a year', 'Twice or more', 'Compare plans'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $recommendation, $question],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isIdentityQuestion(string $userMessage): bool
    {
        $message = Str::lower(trim($userMessage));
        $normalized = str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $message);

        return Str::contains($normalized, [
            'انت مين',
            'انتي مين',
            'مين انت',
            'مين انتي',
            'من انت',
            'من انتي',
            'تعرفني بنفسك',
            'اسمك ايه',
            'اسمك اي',
            'who are you',
            'what are you',
            'your name',
        ]);
    }

    protected function isPaymentQuestion(string $userMessage): bool
    {
        $message = Str::lower(trim($userMessage));
        $normalized = str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $message);

        return Str::contains($normalized, [
            'كيف ادفع',
            'كيف الدفع',
            'طريقة الدفع',
            'طرق الدفع',
            'ابي ادفع',
            'ابغى ادفع',
            'الدفع اونلاين',
            'دفع الكتروني',
            'اي بيمنت',
            'e-payment',
            'payment',
            'pay',
        ]);
    }

    protected function paymentReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        if ($locale === 'ar') {
            $message = 'تقدر تدفع أونلاين بشكل آمن من خلال صفحة الدفع داخل حسابك، وبعد الدفع تقدر تتابع سجل العمليات والفواتير من لوحة التحكم.';
            $next = 'لو عندك حجز أو عضوية محددة، افتحها واضغط على الدفع أو ادخل صفحة المدفوعات من هنا: [الدفع الإلكتروني](/user/deposit)';
            $suggested = ['عندي مشكلة في الدفع', 'أبي فاتورة', 'أبي أكمل حجز'];
        } else {
            $message = 'You can pay securely online from your dashboard, then track transactions and invoices from your account.';
            $next = 'If you already have a booking or membership, open it and continue payment, or use: [E-Payment](/user/deposit)';
            $suggested = ['Payment issue', 'Need invoice', 'Continue booking'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $next],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isSupportQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'ابي اكلم موظف',
            'ابغى اكلم موظف',
            'اكلم الدعم',
            'تواصل مع الدعم',
            'خدمة العملاء',
            'موظف',
            'دعم',
            'مشكله',
            'مشكلة',
            'support',
            'agent',
            'human',
        ]);
    }

    protected function supportReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        $message = $locale === 'ar'
            ? 'أكيد، لو تحتاج موظف أقدر أحولك للدعم. اضغط زر تحدث مع موظف بالأسفل، أو اكتب لي المشكلة باختصار وأحاول أساعدك أولاً.'
            : 'Sure, if you need a human agent you can use the Talk to a human button below, or describe the issue and I will try to help first.';

        return [
            'reply' => $message,
            'messages' => [$message],
            'suggested_replies' => $locale === 'ar' ? ['مشكلة في الدفع', 'مشكلة في الحجز', 'أبي موظف'] : ['Payment issue', 'Booking issue', 'Human agent'],
            'handover_recommended' => true,
        ];
    }

    protected function isBookingStatusQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'حاله الحجز',
            'حالة الحجز',
            'وين حجزي',
            'فين حجزي',
            'حجزي وين',
            'تاكيد الحجز',
            'تأكيد الحجز',
            'الحجز اتاكد',
            'الحجز تأكد',
            'متابعه الحجز',
            'متابعة الحجز',
            'booking status',
            'my booking',
        ]);
    }

    protected function bookingStatusReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'تقدر تتابع حالة الحجز من لوحة التحكم. هتلاقي حجوزات الباقات وحجوزات الخدمات كل واحدة في مكانها.',
                'gulf' => 'تقدر تتابع حالة الحجز من لوحة التحكم. بتلقى حجوزات الباقات وحجوزات الخدمات كل وحدة في مكانها.',
                'msa' => 'يمكنك متابعة حالة الحجز من لوحة التحكم، حيث تظهر حجوزات الباقات وحجوزات الخدمات في أقسام منفصلة.',
            ]);
            $links = '[حجوزات الباقات](/user/booking-list)' . "\n" . '[حجوزات الخدمات](/user/service-bookings)';
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['فين الفاتورة؟', 'عايز ألغى الحجز', 'مشكلة في الدفع'],
                'gulf' => ['وين الفاتورة؟', 'أبغى ألغي الحجز', 'مشكلة في الدفع'],
                'msa' => ['أين الفاتورة؟', 'أريد إلغاء الحجز', 'مشكلة في الدفع'],
            ]);
        } else {
            $message = 'You can track booking status from your dashboard. Tour package bookings and service bookings are listed separately.';
            $links = '[Package bookings](/user/booking-list)' . "\n" . '[Service bookings](/user/service-bookings)';
            $suggested = ['Invoice', 'Cancel booking', 'Payment issue'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $links],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isInvoiceQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'فاتوره',
            'فاتورة',
            'الفاتوره',
            'الفاتورة',
            'ايصال',
            'إيصال',
            'وصل الدفع',
            'invoice',
            'receipt',
        ]);
    }

    protected function invoiceReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'الفواتير بتظهر في حسابك بعد إنشاء الفاتورة أو إتمام العملية. افتح صفحة الفواتير وهتلاقي التفاصيل والتحميل لو متاح.',
                'gulf' => 'الفواتير تظهر في حسابك بعد إنشاء الفاتورة أو إتمام العملية. افتح صفحة الفواتير وبتلقى التفاصيل والتحميل إذا كان متاح.',
                'msa' => 'تظهر الفواتير في حسابك بعد إنشاء الفاتورة أو إتمام العملية. افتح صفحة الفواتير لمراجعة التفاصيل أو التحميل إن كان متاحا.',
            ]);
            $link = '[الفواتير](/user/invoice)';
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['مش لاقي الفاتورة', 'عايز إيصال الدفع', 'مشكلة في الحجز'],
                'gulf' => ['ما لقيت الفاتورة', 'أبغى إيصال الدفع', 'مشكلة في الحجز'],
                'msa' => ['لا أجد الفاتورة', 'أريد إيصال الدفع', 'مشكلة في الحجز'],
            ]);
        } else {
            $message = 'Invoices appear in your account after an invoice is created or the transaction is completed. Open invoices to view or download details when available.';
            $link = '[Invoices](/user/invoice)';
            $suggested = ['Invoice missing', 'Payment receipt', 'Booking issue'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $link],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isCancelRefundQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'الغاء',
            'إلغاء',
            'الغي',
            'ألغي',
            'استرجاع',
            'استرداد',
            'استرد',
            'استرجع',
            'فلوسي',
            'refund',
            'cancel',
            'cancellation',
        ]);
    }

    protected function cancelRefundReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'الإلغاء أو الاسترجاع بيعتمد على نوع الحجز وسياسة المزود ووقت الطلب. الأفضل تفتح الحجز الأول وتشوف حالته، ولو محتاج موظف اضغط تحدث مع موظف.',
                'gulf' => 'الإلغاء أو الاسترجاع يعتمد على نوع الحجز وسياسة المزود ووقت الطلب. الأفضل تفتح الحجز أول وتشوف حالته، وإذا تحتاج موظف اضغط تحدث مع موظف.',
                'msa' => 'الإلغاء أو الاسترجاع يعتمد على نوع الحجز وسياسة المزود ووقت الطلب. افتح الحجز أولا لمراجعة حالته، وإذا احتجت موظفا استخدم زر تحدث مع موظف.',
            ]);
            $links = '[حجوزاتي](/user/booking-list)' . "\n" . '[المحفظة](/user/wallet)';
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['عايز أكلم موظف', 'فين حالة الحجز؟', 'مشكلة في الدفع'],
                'gulf' => ['أبغى أكلم موظف', 'وين حالة الحجز؟', 'مشكلة في الدفع'],
                'msa' => ['أريد التحدث مع موظف', 'أين حالة الحجز؟', 'مشكلة في الدفع'],
            ]);
        } else {
            $message = 'Cancellation or refund depends on booking type, provider policy, and request timing. Open the booking first, and use Talk to a human if you need an agent.';
            $links = '[My bookings](/user/booking-list)' . "\n" . '[Wallet](/user/wallet)';
            $suggested = ['Talk to an agent', 'Booking status', 'Payment issue'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $links],
            'suggested_replies' => $suggested,
            'handover_recommended' => true,
        ];
    }

    protected function isWalletQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'محفظه',
            'محفظة',
            'رصيد',
            'الكاش باك',
            'كاش باك',
            'كاشباك',
            'النقاط',
            'نقاطي',
            'wallet',
            'balance',
            'cashback',
            'points',
        ]);
    }

    protected function walletReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'الرصيد، المحفظة، الكاش باك، والعمليات تقدر تتابعهم من حسابك. لو فيه عملية ناقصة، راجع سجل العمليات الأول.',
                'gulf' => 'الرصيد، المحفظة، الكاش باك، والعمليات تقدر تتابعها من حسابك. إذا فيه عملية ناقصة، راجع سجل العمليات أول.',
                'msa' => 'يمكنك متابعة الرصيد، المحفظة، الكاش باك، وسجل العمليات من حسابك. إذا كانت هناك عملية ناقصة، راجع سجل العمليات أولا.',
            ]);
            $links = '[المحفظة](/user/wallet)' . "\n" . '[سجل العمليات](/user/transactions)';
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['اشرح الكاش باك', 'فين العمليات؟', 'مشكلة في الرصيد'],
                'gulf' => ['اشرح الكاش باك', 'وين العمليات؟', 'مشكلة في الرصيد'],
                'msa' => ['اشرح الكاش باك', 'أين العمليات؟', 'مشكلة في الرصيد'],
            ]);
        } else {
            $message = 'You can track wallet balance, cashback, points, and transactions from your account. If something is missing, check transactions first.';
            $links = '[Wallet](/user/wallet)' . "\n" . '[Transactions](/user/transactions)';
            $suggested = ['Explain cashback', 'Transactions', 'Balance issue'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $links],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isDestinationAdviceQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'وين اروح',
            'فين اروح',
            'اقترح وجهه',
            'اقترح وجهة',
            'رشح لي وجهه',
            'رشحلي وجهة',
            'ما اعرف وين اسافر',
            'مش عارف اسافر فين',
            'suggest destination',
            'where should i go',
        ]);
    }

    protected function destinationAdviceReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'أقدر أرشحلك وجهة، بس الاختيار الصح بيعتمد على الجو اللي بتحبه: استرخاء، تسوق، بحر، أو رحلة دينية.',
                'gulf' => 'أقدر أرشح لك وجهة، لكن الاختيار المناسب يعتمد على الجو اللي تفضله: استرخاء، تسوق، بحر، أو رحلة دينية.',
                'msa' => 'أستطيع ترشيح وجهة مناسبة، لكن الاختيار يعتمد على نوع الرحلة: استرخاء، تسوق، بحر، أو رحلة دينية.',
            ]);
            $next = $this->arabicByDialect($dialect, [
                'eg' => 'قولّي الميزانية وعدد الأشخاص وموعد السفر، أو افتح طلب السفر من هنا: [طلب سفر](/more-travel)',
                'gulf' => 'قل لي الميزانية وعدد الأشخاص وموعد السفر، أو افتح طلب السفر من هنا: [طلب سفر](/more-travel)',
                'msa' => 'أخبرني بالميزانية وعدد الأشخاص وموعد السفر، أو افتح طلب السفر من هنا: [طلب سفر](/more-travel)',
            ]);
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['بحر واسترخاء', 'تسوق ومدينة', 'رحلة دينية'],
                'gulf' => ['بحر واسترخاء', 'تسوق ومدينة', 'رحلة دينية'],
                'msa' => ['بحر واسترخاء', 'تسوق ومدينة', 'رحلة دينية'],
            ]);
        } else {
            $message = 'I can suggest a destination, but the right choice depends on your preferred vibe: relaxation, shopping, beach, or religious travel.';
            $next = 'Tell me your budget, group size, and dates, or start here: [Travel request](/more-travel)';
            $suggested = ['Beach and relax', 'Shopping and city', 'Religious trip'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $next],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function identityReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        $message = $locale === 'ar'
            ? 'أنا Feliz AI، المساعد الذكي الخاص بـ AltayarVIP. أقدر أساعدك في العروض، العضويات، الحجز، والمدفوعات.'
            : 'I am Feliz AI, AltayarVIP smart assistant. I can help with offers, memberships, bookings, and payments.';

        $poweredBy = $locale === 'ar'
            ? 'أنا مدعوم ومطور بواسطة Pencil Studio. تقدر تعرف عنهم أكثر من هنا: [Pencil Studio](https://dipencil.com/)'
            : 'I am powered and supported by Pencil Studio. You can learn more here: [Pencil Studio](https://dipencil.com/)';

        return [
            'reply' => $message,
            'messages' => [$message, $poweredBy],
            'suggested_replies' => $locale === 'ar'
                ? ['أفضل العروض', 'عرض العضويات', 'مساعدة في الحجز']
                : ['Best offers', 'View memberships', 'Booking help'],
            'handover_recommended' => false,
        ];
    }

    protected function isAboutAltayarQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'من انتم',
            'مين انتم',
            'ايه الطيار',
            'وش الطيار',
            'عن الشركه',
            'عن الشركة',
            'altayarvip',
            'altayar vip',
            'الطيار vip',
            'الطيار في اي بي',
            'about altayar',
            'about company',
        ]);
    }

    protected function aboutAltayarReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'AltayarVIP منصة سفر وسياحة وعضويات بتجمع العروض، الحجوزات، النقاط، والخصومات في تجربة واحدة.',
                'gulf' => 'AltayarVIP منصة سفر وسياحة وعضويات تجمع العروض، الحجوزات، النقاط، والخصومات في تجربة واحدة.',
                'msa' => 'AltayarVIP منصة سفر وسياحة وعضويات تجمع العروض، الحجوزات، النقاط، والخصومات في تجربة واحدة.',
            ]);
            $details = $this->arabicByDialect($dialect, [
                'eg' => 'الخدمات الأساسية: عروض محدودة، باقات سفر، وجهات، فنادق، رحلات، عضويات، نقاط، ودفع إلكتروني.',
                'gulf' => 'الخدمات الأساسية: عروض محدودة، باقات سفر، وجهات، فنادق، رحلات، عضويات، نقاط، ودفع إلكتروني.',
                'msa' => 'الخدمات الأساسية: عروض محدودة، باقات سفر، وجهات، فنادق، رحلات، عضويات، نقاط، ودفع إلكتروني.',
            ]);
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['عرض العضويات', 'أفضل العروض', 'عايز أسافر'],
                'gulf' => ['عرض العضويات', 'أفضل العروض', 'أبغى أسافر'],
                'msa' => ['عرض العضويات', 'أفضل العروض', 'أريد السفر'],
            ]);
        } else {
            $message = 'AltayarVIP is a travel, tourism, and membership platform that brings offers, bookings, points, and discounts into one experience.';
            $details = 'Core services include limited offers, travel packages, destinations, hotels, flights, tours, memberships, points, and e-payment.';
            $suggested = ['View memberships', 'Best offers', 'I want to travel'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $details],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isContactQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'رقم التواصل',
            'رقمكم',
            'واتساب',
            'واتس',
            'ايميل',
            'البريد',
            'تواصل',
            'العنوان',
            'المقر',
            'contact',
            'email',
            'whatsapp',
            'phone',
        ]);
    }

    protected function contactReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'تقدر تتواصل مع AltayarVIP من خلال الواتساب أو الإيميل. لو سؤالك عن حجز أو دفع، ابعت رقم العملية لو موجود.',
                'gulf' => 'تقدر تتواصل مع AltayarVIP عبر الواتساب أو الإيميل. إذا سؤالك عن حجز أو دفع، أرسل رقم العملية إذا موجود.',
                'msa' => 'يمكنك التواصل مع AltayarVIP عبر الواتساب أو البريد الإلكتروني. إذا كان سؤالك عن حجز أو دفع، أرسل رقم العملية إن وجد.',
            ]);
            $details = "واتساب: [+966 57 473 4062](https://wa.me/966574734062)\nللاستفسارات: info@altayarvip.com\nلاشتراكات النادي: info@altayarvip.net\nالمقر: الإمارات، دبي";
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['عايز أكلم موظف', 'مشكلة في الحجز', 'مشكلة في الدفع'],
                'gulf' => ['أبغى أكلم موظف', 'مشكلة في الحجز', 'مشكلة في الدفع'],
                'msa' => ['أريد التحدث مع موظف', 'مشكلة في الحجز', 'مشكلة في الدفع'],
            ]);
        } else {
            $message = 'You can contact AltayarVIP by WhatsApp or email. If your question is about a booking or payment, include the transaction number if available.';
            $details = "WhatsApp: [+966 57 473 4062](https://wa.me/966574734062)\nInquiries: info@altayarvip.com\nClub subscriptions: info@altayarvip.net\nHeadquarters: UAE, Dubai";
            $suggested = ['Talk to an agent', 'Booking issue', 'Payment issue'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $details],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isAgentQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'نظام الوكيل',
            'الوكيل',
            'agent system',
            'agent screen',
            'اصير وكيل',
            'ابي اصير وكيل',
            'ابغى اصير وكيل',
            'مندوب',
        ]);
    }

    protected function agentReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'نظام الوكيل مناسب للشركات أو الأفراد اللي محتاجين حجز أسرع وخصومات طول السنة.',
                'gulf' => 'نظام الوكيل مناسب للشركات أو الأفراد اللي يحتاجون حجز أسرع وخصومات طوال السنة.',
                'msa' => 'نظام الوكيل مناسب للشركات أو الأفراد الذين يحتاجون حجزا أسرع وخصومات طوال السنة.',
            ]);
            $details = 'من مزاياه: تسجيل سريع، عروض وخصومات، بعض الإضافات المجانية، انتقالات، وسهولة إدارة الحجوزات.';
            $link = '[نظام الوكيل](/agent)';
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['عايز أعرف الشروط', 'أكلم موظف', 'عرض العضويات'],
                'gulf' => ['أبغى أعرف الشروط', 'أكلم موظف', 'عرض العضويات'],
                'msa' => ['أريد معرفة الشروط', 'أتحدث مع موظف', 'عرض العضويات'],
            ]);
        } else {
            $message = 'The agent system is suitable for companies or individuals who need faster booking and year-round discounts.';
            $details = 'Benefits include quick registration, offers and discounts, some free additions, transportation support, and easier booking management.';
            $link = '[Agent system](/agent)';
            $suggested = ['Requirements', 'Talk to an agent', 'View memberships'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $details . "\n" . $link],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isBenefitQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'المزايا',
            'مزايا',
            'فوائد',
            'استفيد',
            'ايش استفيد',
            'وش استفيد',
            'benefits',
            'advantages',
        ]);
    }

    protected function benefitsReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'أهم مزايا AltayarVIP إنك تجمع بين عروض السفر والعضويات والنقاط في مكان واحد.',
                'gulf' => 'أهم مزايا AltayarVIP إنك تجمع بين عروض السفر والعضويات والنقاط في مكان واحد.',
                'msa' => 'أهم مزايا AltayarVIP أنك تجمع بين عروض السفر والعضويات والنقاط في مكان واحد.',
            ]);
            $details = "المزايا تشمل:\n- خصومات على الحجوزات والعروض\n- نقاط مكافآت مع العضوية\n- عروض فنادق وبعض الليالي المجانية حسب العرض\n- قسائم وخصومات\n- دعم قبل وأثناء وبعد الرحلة\n- دفع إلكتروني ومتابعة فواتير";
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['قارن الباقات', 'عرض العضويات', 'أفضل العروض'],
                'gulf' => ['قارن الباقات', 'عرض العضويات', 'أفضل العروض'],
                'msa' => ['قارن الباقات', 'عرض العضويات', 'أفضل العروض'],
            ]);
        } else {
            $message = 'The main benefit of AltayarVIP is combining travel offers, memberships, and points in one place.';
            $details = "Benefits include:\n- Booking discounts and offers\n- Membership reward points\n- Hotel deals and selected free nights\n- Vouchers and coupons\n- Support before, during, and after the trip\n- E-payment and invoice tracking";
            $suggested = ['Compare plans', 'View memberships', 'Best offers'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $details],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isDestinationQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'الوجهات',
            'وجهات',
            'الدول',
            'اماكن السفر',
            'دبي',
            'السعوديه',
            'السعودية',
            'مصر',
            'تايلاند',
            'سنغافورة',
            'امريكا',
            'destinations',
            'countries',
        ]);
    }

    protected function destinationsReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'من الوجهات البارزة عند AltayarVIP: الإمارات، السعودية، مصر، تايلاند، سنغافورة، وأمريكا.',
                'gulf' => 'من الوجهات البارزة عند AltayarVIP: الإمارات، السعودية، مصر، تايلاند، سنغافورة، وأمريكا.',
                'msa' => 'من الوجهات البارزة لدى AltayarVIP: الإمارات، السعودية، مصر، تايلاند، سنغافورة، وأمريكا.',
            ]);
            $next = $this->arabicByDialect($dialect, [
                'eg' => 'لو مش محدد وجهة، قولّي بتحب بحر، تسوق، استرخاء، ولا رحلة دينية وأنا أرشحلك.',
                'gulf' => 'إذا مو محدد وجهة، قل لي تفضل بحر، تسوق، استرخاء، أو رحلة دينية وأنا أرشح لك.',
                'msa' => 'إذا لم تحدد وجهة، أخبرني هل تفضل البحر، التسوق، الاسترخاء، أم رحلة دينية وسأرشح لك.',
            ]);
            $link = '[طلب سفر](/more-travel)';
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['بحر واسترخاء', 'تسوق ومدينة', 'رحلة دينية'],
                'gulf' => ['بحر واسترخاء', 'تسوق ومدينة', 'رحلة دينية'],
                'msa' => ['بحر واسترخاء', 'تسوق ومدينة', 'رحلة دينية'],
            ]);
        } else {
            $message = 'Popular AltayarVIP destinations include UAE, Saudi Arabia, Egypt, Thailand, Singapore, and USA.';
            $next = 'If you are not sure where to go, tell me whether you prefer beach, shopping, relaxation, or religious travel.';
            $link = '[Travel request](/more-travel)';
            $suggested = ['Beach and relax', 'Shopping and city', 'Religious trip'];
        }

        return [
            'reply' => $message,
            'messages' => [$message, $next . "\n" . $link],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function isAppQuestion(string $userMessage): bool
    {
        $normalized = $this->normalizeArabicText($userMessage);

        return Str::contains($normalized, [
            'التطبيق',
            'ابلكيشن',
            'تطبيق',
            'اندرويد',
            'ايفون',
            'ios',
            'android',
            'app',
        ]);
    }

    protected function appReply(ChatConversation $conversation, string $userMessage): array
    {
        $dialect = $this->detectArabicDialect($userMessage);
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);

        if ($isArabic) {
            $message = $this->arabicByDialect($dialect, [
                'eg' => 'AltayarVIP كان بيعرض توفر تطبيق للأندرويد و iOS. لو محتاج رابط التحميل الحالي، الأفضل تتواصل مع الدعم أو تراجع صفحة الموقع.',
                'gulf' => 'AltayarVIP كان يعرض توفر تطبيق للأندرويد و iOS. إذا تحتاج رابط التحميل الحالي، الأفضل تتواصل مع الدعم أو تراجع صفحة الموقع.',
                'msa' => 'كان AltayarVIP يعرض توفر تطبيق للأندرويد و iOS. إذا احتجت رابط التحميل الحالي، يفضل التواصل مع الدعم أو مراجعة صفحة الموقع.',
            ]);
            $suggested = $this->arabicByDialect($dialect, [
                'eg' => ['أكلم الدعم', 'عرض العضويات', 'أفضل العروض'],
                'gulf' => ['أكلم الدعم', 'عرض العضويات', 'أفضل العروض'],
                'msa' => ['أتحدث مع الدعم', 'عرض العضويات', 'أفضل العروض'],
            ]);
        } else {
            $message = 'AltayarVIP has shown app availability for Android and iOS. For the current download link, contact support or check the website.';
            $suggested = ['Contact support', 'View memberships', 'Best offers'];
        }

        return [
            'reply' => $message,
            'messages' => [$message],
            'suggested_replies' => $suggested,
            'handover_recommended' => false,
        ];
    }

    protected function membershipListReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';
        $plans = MembershipPlan::where('status', 1)->orderBy('id', 'asc')->get();

        if ($plans->isEmpty()) {
            $message = $locale === 'ar'
                ? 'لا توجد عضويات متاحة حاليا، ويمكنك الرجوع لاحقا أو التواصل مع الدعم.'
                : 'There are no memberships available right now. Please check again later or contact support.';

            return [
                'reply' => $message,
                'messages' => [$message],
                'suggested_replies' => [],
                'handover_recommended' => false,
            ];
        }

        $intro = $locale === 'ar'
            ? 'العضوية تساعدك تستفيد من مزايا AltayarVIP مثل الخصومات، نقاط المكافآت، أولوية الدعم، وتجربة حجز أسهل حسب مستوى العضوية.'
            : 'A membership helps you unlock AltayarVIP benefits such as discounts, reward points, priority support, and a smoother booking experience based on your plan.';

        $lead = $locale === 'ar'
            ? 'دول العضويات المتاحة. اختار أي عضوية لفتح تفاصيلها:'
            : 'These are the available memberships. Choose any plan to open its details:';

        $buttons = $plans->map(function ($plan) use ($locale) {
            $name = $locale === 'ar' && $plan->name_ar ? $plan->name_ar : $plan->name;
            $price = showAmount($plan->price);
            $label = $locale === 'ar'
                ? "{$name} - {$price}"
                : "{$name} - {$price}";

            return "[{$label}](/membership-details/{$plan->id})";
        })->implode("\n");

        return [
            'reply' => $intro,
            'messages' => [
                $intro,
                $lead . "\n" . $buttons,
            ],
            'suggested_replies' => [],
            'handover_recommended' => false,
        ];
    }

    protected function casualReply(ChatConversation $conversation, string $userMessage): array
    {
        $isArabic = preg_match('/\p{Arabic}/u', $userMessage);
        $locale = ($isArabic || Str::startsWith($conversation->locale ?? app()->getLocale(), 'ar')) ? 'ar' : 'en';

        $messages = $locale === 'ar'
            ? ['أنا تمام، شكرا لسؤالك. موجود معك وجاهز أساعدك في أي حاجة تحتاجها.']
            : ['I am doing well, thanks for asking. I am here and ready to help with anything you need.'];

        return [
            'reply' => $messages[0],
            'messages' => $messages,
            'suggested_replies' => $locale === 'ar'
                ? ['عايز أشوف العروض', 'مساعدة في الحجز', 'العضويات المتاحة']
                : ['View offers', 'Help me book', 'Available memberships'],
            'handover_recommended' => false,
        ];
    }

    protected function decodeAssistantPayload(string $text): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?/i', '', $text);
        $text = preg_replace('/```$/', '', $text);
        $payload = json_decode(trim($text), true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($payload)) {
            return $payload;
        }

        if (Str::startsWith($text, '{') && preg_match('/"reply"\s*:\s*"([^"]*)/s', $text, $matches)) {
            return [
                'reply' => trim(stripcslashes($matches[1])),
                'messages' => [],
                'suggested_replies' => [],
                'handover_recommended' => false,
            ];
        }

        if (Str::startsWith($text, '{') || Str::startsWith($text, '[')) {
            return [
                'reply' => '',
                'messages' => [],
                'suggested_replies' => [],
                'handover_recommended' => false,
            ];
        }

        return [
            'reply' => trim($text),
            'messages' => [],
            'suggested_replies' => [],
            'handover_recommended' => false,
        ];
    }

    protected function normalizeAssistantMessages(array $parsed, ChatConversation $conversation, string $userMessage): array
    {
        $messages = $parsed['messages'] ?? null;

        if (is_array($messages)) {
            $messages = array_values(array_filter(array_map(function ($message) {
                return $this->normalizeAssistantLinks(trim((string) $message));
            }, $messages)));
        } else {
            $messages = [];
        }

        $messages = array_values(array_filter($messages, static function ($message) {
            return ! in_array(Str::lower($message), ['reply', 'messages', 'message'], true);
        }));

        if (! empty($messages)) {
            return array_slice($messages, 0, 3);
        }

        $reply = $this->normalizeAssistantLinks(trim((string) ($parsed['reply'] ?? '')));
        if ($reply === '') {
            return $this->fallbackReply($conversation, $userMessage)['messages'];
        }

        $segments = preg_split('/(?:\n\s*\n|(?<=[.!?؟])\s+)/u', $reply, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $segments = array_values(array_filter(array_map('trim', $segments)));

        return array_slice($segments ?: [$reply], 0, 3);
    }

    protected function detectAssistantSubject(string $userMessage): string
    {
        $message = Str::lower($userMessage);

        if (Str::contains($message, ['عضوية', 'عضويات', 'اشتراك', 'اشترك', 'membership', 'plan', 'plans', 'memberships'])) {
            return 'membership';
        }

        if (Str::contains($message, ['عرض', 'عروض', 'افضل العروض', 'أفضل العروض', 'وش العروض', 'ايش العروض', 'العروض عندكم', 'ابي عرض', 'ابغى عرض', 'ودي عرض', 'عندكم عروض', 'خصم', 'خصومات', 'offer', 'offers', 'discount', 'promo', 'promotion'])) {
            return 'offer';
        }

        if (Str::contains($message, ['حجز', 'سفر', 'اسافر', 'بسافر', 'نسافر', 'نبي نسافر', 'نبغى نسافر', 'رحلة', 'رحله', 'رحلات', 'وجهة', 'وجهه', 'اصحابي', 'صحابي', 'الشباب', 'الربع', 'اهلي', 'عيالي', 'عائلتي', 'العائلة', 'العائله', 'booking', 'book', 'reserve', 'reservation', 'travel', 'trip'])) {
            return 'booking';
        }

        if (Str::contains($message, ['نقاط', 'النقاط', 'بوينت', 'كاش باك', 'كاشباك', 'استرجاع', 'points', 'cashback', 'loyalty'])) {
            return 'points';
        }

        if (Str::contains($message, ['تفعيل', 'نشط', 'تنشيط', 'activate', 'activation', 'inactive'])) {
            return 'activation';
        }

        if (Str::contains($message, ['باسورد', 'كلمة السر', 'كلمه السر', 'password', 'reset'])) {
            return 'password';
        }

        if (Str::contains($message, ['طيران', 'طياره', 'طيارة', 'flight', 'flights', 'ticket'])) {
            return 'flight';
        }

        return 'general';
    }

    protected function detectArabicDialect(string $userMessage): string
    {
        $normalized = $this->normalizeArabicText($userMessage);

        if (Str::contains($normalized, [
            'عايز',
            'عاوزه',
            'عاوز',
            'ازاي',
            'ايه',
            'فين',
            'كام',
            'دلوقتي',
            'صحابي',
            'اصحابي',
            'عيلتي',
            'معايا',
            'هسافر',
            'هتسافر',
            'برا',
        ])) {
            return 'eg';
        }

        if (Str::contains($normalized, [
            'ابي',
            'ابغى',
            'تبغى',
            'نبغى',
            'نبي',
            'وش',
            'وين',
            'كيف',
            'ودي',
            'اهلي',
            'عيالي',
            'الشباب',
            'الربع',
            'بتسافرون',
            'تسافرون',
        ])) {
            return 'gulf';
        }

        return 'msa';
    }

    protected function normalizeArabicText(string $text): string
    {
        $message = Str::lower(trim($text));

        return str_replace(['أ', 'إ', 'آ', 'ة', 'ى'], ['ا', 'ا', 'ا', 'ه', 'ي'], $message);
    }

    protected function arabicByDialect(string $dialect, array $phrases): mixed
    {
        return $phrases[$dialect] ?? $phrases['msa'] ?? reset($phrases);
    }

    protected function normalizeAssistantLinks(string $text): string
    {
        return str_replace(
            ['/memberships', '/bookings/hotels', '/bookings/flights'],
            ['/membership-details', '/offers/limited', '/more-travel'],
            $text
        );
    }

    protected function bestMembershipLine(array $knowledge): ?string
    {
        $plan = $knowledge['membership_plans'][0] ?? null;

        if (! is_array($plan)) {
            return null;
        }

        $parts = array_filter([
            $plan['name'] ?? null,
            isset($plan['price']) ? 'price ' . number_format((float) $plan['price'], 2) : null,
            isset($plan['duration_days']) ? (int) $plan['duration_days'] . ' days' : null,
            isset($plan['bonus_points']) ? (int) $plan['bonus_points'] . ' bonus points' : null,
        ]);

        return $parts ? implode(' | ', $parts) : null;
    }

    protected function bestOfferLine(array $knowledge): ?string
    {
        $offer = $knowledge['offers'][0] ?? null;

        if (! is_array($offer)) {
            return null;
        }

        $parts = array_filter([
            $offer['title'] ?? null,
            $offer['city'] ?? null,
            $offer['country'] ?? null,
            isset($offer['price']) ? 'price ' . number_format((float) $offer['price'], 2) : null,
            $offer['offer'] ?? null,
        ]);

        return $parts ? implode(' | ', $parts) : null;
    }

    protected function bestBookingLine(array $knowledge): string
    {
        $bookingProcess = $knowledge['booking_process'][0] ?? null;

        return is_string($bookingProcess) && trim($bookingProcess) !== ''
            ? $bookingProcess
            : 'Find a trip, review the price, submit booking details, complete payment if needed, then wait for confirmation.';
    }
}
