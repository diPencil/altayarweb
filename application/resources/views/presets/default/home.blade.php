@extends($activeTemplate . 'layouts.frontend')
@section('content')
    @include($activeTemplate . 'components.banner')
    @if ($sections->secs != null)
        @foreach (json_decode($sections->secs) as $sec)
            @if ($sec == 'popular_tour')
                @include($activeTemplate . 'sections.membership_showcase')
            @endif
            @include($activeTemplate . 'sections.' . $sec)
            @if ($sec == 'popular_tour')
                @include($activeTemplate . 'sections.preferred_employee_system')
                @include($activeTemplate . 'sections.home_reels')
            @endif
            @if ($sec == 'blog')
                @include($activeTemplate . 'sections.membership_plans')
            @endif
        @endforeach
    @endif
@endsection
