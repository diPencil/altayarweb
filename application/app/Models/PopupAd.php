<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PopupAd extends Model
{
    protected $fillable = [
        'name',
        'title',
        'title_ar',
        'body',
        'body_ar',
        'cta_text',
        'cta_text_ar',
        'cta_url',
        'image',
        'placement',
        'size',
        'audience_type',
        'display_contexts',
        'page_rules',
        'membership_plan_ids',
        'target_user_ids',
        'target_employee_ids',
        'created_by_type',
        'created_by_id',
        'trigger_type',
        'trigger_value',
        'frequency',
        'frequency_value',
        'starts_at',
        'ends_at',
        'closeable',
        'status',
        'priority',
    ];

    protected $casts = [
        'display_contexts' => 'array',
        'page_rules' => 'array',
        'membership_plan_ids' => 'array',
        'target_user_ids' => 'array',
        'target_employee_ids' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'closeable' => 'boolean',
        'status' => 'boolean',
    ];

    public const PLACEMENTS = [
        'modal' => 'Center Modal',
        'top_bar' => 'Top Bar',
        'bottom_bar' => 'Bottom Bar',
        'right_corner' => 'Right Corner',
        'left_corner' => 'Left Corner',
        'right_side' => 'Right Side',
        'left_side' => 'Left Side',
    ];

    public const SIZES = [
        'compact' => 'Compact',
        'medium' => 'Medium',
        'wide' => 'Wide',
        'tall' => 'Tall',
        'topbar' => 'Top Bar Size',
    ];

    public const AUDIENCES = [
        'all' => 'Everyone',
        'guest' => 'Guests Only',
        'user' => 'Logged-in Users',
        'employee' => 'Employees',
        'membership' => 'Membership Plans',
        'no_membership' => 'Users Having No Plan',
        'specific_users' => 'Specific Users',
        'specific_employees' => 'Specific Employees',
    ];

    public const CONTEXTS = [
        'frontend' => 'Website Pages',
        'user_dashboard' => 'User Dashboard',
        'employee_dashboard' => 'Employee Dashboard',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(PopupAdEvent::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public static function requestContext(): string
    {
        if (request()->routeIs('employee.*')) {
            return 'employee_dashboard';
        }

        if (request()->routeIs('user.*')) {
            return 'user_dashboard';
        }

        return 'frontend';
    }

    public static function viewer(): array
    {
        if (Auth::guard('employee')->check()) {
            return ['type' => 'employee', 'id' => Auth::guard('employee')->id(), 'model' => Auth::guard('employee')->user()];
        }

        if (Auth::guard('web')->check()) {
            return ['type' => 'user', 'id' => Auth::guard('web')->id(), 'model' => Auth::guard('web')->user()];
        }

        return ['type' => 'guest', 'id' => null, 'model' => null];
    }

    public function matchesCurrentRequest(): bool
    {
        return $this->matchesContext(static::requestContext())
            && $this->matchesPage(request()->path())
            && $this->matchesViewer(static::viewer());
    }

    public function matchesContext(string $context): bool
    {
        $contexts = $this->display_contexts ?: ['frontend'];
        return in_array($context, $contexts, true);
    }

    public function matchesPage(string $path): bool
    {
        $rules = array_values(array_filter($this->page_rules ?: []));
        if (!$rules) {
            return true;
        }

        $path = trim($path, '/') ?: '/';
        foreach ($rules as $rule) {
            $rule = trim((string) $rule, '/');
            if ($rule === '*' || $rule === '') {
                return true;
            }
            if (Str::is($rule, $path) || Str::is($rule . '*', $path)) {
                return true;
            }
        }

        return false;
    }

    public function matchesViewer(array $viewer): bool
    {
        $type = $viewer['type'];
        $id = (int) ($viewer['id'] ?? 0);

        return match ($this->audience_type) {
            'all' => true,
            'guest' => $type === 'guest',
            'user' => $type === 'user',
            'employee' => $type === 'employee',
            'specific_users' => $type === 'user' && in_array($id, array_map('intval', $this->target_user_ids ?: []), true),
            'specific_employees' => $type === 'employee' && in_array($id, array_map('intval', $this->target_employee_ids ?: []), true),
            'membership' => $type === 'user' && $this->viewerHasMembershipPlan($viewer),
            'no_membership' => $type === 'user' && !$this->viewerCurrentMembershipPlanId($viewer),
            default => false,
        };
    }

    protected function viewerHasMembershipPlan(array $viewer): bool
    {
        $planId = $this->viewerCurrentMembershipPlanId($viewer);
        return $planId && in_array((int) $planId, array_map('intval', $this->membership_plan_ids ?: []), true);
    }

    protected function viewerCurrentMembershipPlanId(array $viewer): ?int
    {
        $user = $viewer['model'] ?? null;
        if (!$user || !method_exists($user, 'currentMembership')) {
            return null;
        }

        $membership = $user->currentMembership()->first();
        return $membership?->membership_plan_id ? (int) $membership->membership_plan_id : null;
    }

    public function localizedTitle(): string
    {
        return is_rtl() && $this->title_ar ? $this->title_ar : (string) $this->title;
    }

    public function localizedBody(): string
    {
        return is_rtl() && $this->body_ar ? $this->body_ar : (string) $this->body;
    }

    public function localizedCtaText(): string
    {
        return is_rtl() && $this->cta_text_ar ? $this->cta_text_ar : (string) $this->cta_text;
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset(getFilePath('popupAd') . '/' . $this->image) : null;
    }
}
