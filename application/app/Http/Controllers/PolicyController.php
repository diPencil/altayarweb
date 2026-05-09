<?php

namespace App\Http\Controllers;

class PolicyController extends Controller
{
    public function index()
    {
        $pageTitle = 'policy_hub.heading';
        $policies = getContent('policy_pages.element');
        if ($policies && $policies->count()) {
            $policies = $policies->sortBy(fn ($p) => policy_is_website_policy_card($p) ? 0 : 1)->values();
        }
        $sections = getContent('policy_pages.content', true);
        return view('presets.default.policy_index', compact('pageTitle', 'policies', 'sections'));
    }

    public function websitePolicy()
    {
        $pageTitle = 'Website Policy';
        $breadcrumbTrail = [
            ['label' => 'Our Privacy', 'url' => route('policy.index')],
            ['label' => 'Website Policy', 'url' => null],
        ];
        return view('presets.default.policy_website', compact('pageTitle', 'breadcrumbTrail'));
    }

    public function termsAndConditions()
    {
        $pageTitle = 'Terms And Conditions';
        $breadcrumbTrail = [
            ['label' => 'Our Privacy', 'url' => route('policy.index')],
            ['label' => 'Terms And Conditions', 'url' => null],
        ];
        return view('presets.default.policy_terms', compact('pageTitle', 'breadcrumbTrail'));
    }
}
