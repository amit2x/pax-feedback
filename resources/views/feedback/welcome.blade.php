@extends('layouts.public')

@section('title', __('messages.app_name'))

@section('content')
    {{-- ============================================================
         Hero banner with namaskar image
         ============================================================ --}}
    <section class="paf-hero" aria-label="{{ __('messages.hero_aria') }}">
        <picture>
            <source
                srcset="{{ asset('images/hero-namaskar.webp') }}"
                type="image/webp">
            <img
                class="paf-hero__img"
                src="{{ asset('images/hero-namaskar.jpg') }}"
                alt="{{ __('messages.hero_alt') }}"
                width="1600"
                height="1000"
                loading="eager"
                decoding="async"
                fetchpriority="high">
        </picture>

        <div class="paf-hero__overlay" aria-hidden="true"></div>

        <span class="paf-hero__badge">
            ✈ {{ __('messages.hero_badge') }}
        </span>

        <div class="paf-hero__content">
            <span class="paf-hero__eyebrow">🇮🇳 {{ __('messages.hero_eyebrow') }}</span>
            <h2 class="paf-hero__title">{{ __('messages.hero_title') }}</h2>
            <p class="paf-hero__subtitle">{{ __('messages.hero_subtitle') }}</p>
        </div>
    </section>

    {{-- ============================================================
         Language picker
         ============================================================ --}}
    <div class="paf-card paf-card--accent">
        @if ($location)
            <div class="text-center mb-3">
                <div class="small text-muted mb-1">{{ __('messages.experience_at') }}</div>
                <div class="fw-semibold" style="font-size:1.05rem;">
                    {{ $location->service?->name ?? $location->name }}
                </div>
                <div class="small text-muted">
                    {{ $location->checkpoint_label }}
                    @if ($location->terminal) · {{ $location->terminal->name }} @endif
                </div>
            </div>
        @else
            <h2 class="paf-step-title text-center mb-2">
                {{ __('messages.select_language') }}
            </h2>
            <p class="paf-step-hint text-center">
                {{ __('messages.takes_less_than_a_minute') }}
            </p>
        @endif

        <form method="POST" action="{{ route('feedback.start') }}">
            @csrf

            <div class="paf-lang-switcher">
                <button type="submit" name="language_code" value="en" class="btn btn-outline-primary" aria-label="English">
                    <div style="font-size:1.15rem;">🇬🇧</div>
                    <div style="font-size:0.78rem;">English</div>
                </button>

                <button type="submit" name="language_code" value="hi" class="btn btn-outline-primary" aria-label="हिंदी">
                    <div style="font-size:1.15rem;">🇮🇳</div>
                    <div style="font-size:0.78rem;">हिंदी</div>
                </button>

                <button type="submit" name="language_code" value="bn" class="btn btn-outline-primary" aria-label="বাংলা">
                    <div style="font-size:1.15rem;">🇮🇳</div>
                    <div style="font-size:0.78rem;">বাংলা</div>
                </button>
            </div>
        </form>
    </div>

    {{-- Trust strip --}}
    <div class="text-center mt-2">
        <div class="paf-trust-line" style="display:inline-flex;">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
            </svg>
            <span>{{ __('messages.privacy_short') }}</span>
        </div>
    </div>
@endsection
