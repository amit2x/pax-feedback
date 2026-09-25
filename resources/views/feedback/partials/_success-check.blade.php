{{-- Animated success checkmark — pure CSS, no JS, CSP-safe --}}
<div class="paf-success-anim" role="img" aria-label="{{ __('messages.success_aria') }}">
    <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        {{-- Outer pulse ring --}}
        <circle class="paf-success-ring"
                cx="60" cy="60" r="52"
                fill="none" stroke="currentColor" stroke-width="3" />

        {{-- Filled circle outline --}}
        <circle class="paf-success-circle"
                cx="60" cy="60" r="46"
                fill="none" stroke="currentColor" stroke-width="4"
                pathLength="1"
                transform="rotate(-90 60 60)" />

        {{-- Checkmark (with proper spaces in the d attribute) --}}
        <path class="paf-success-check"
              d="M 36 62 L 54 78 L 86 44"
              fill="none" stroke="currentColor" stroke-width="6"
              stroke-linecap="round" stroke-linejoin="round"
              pathLength="1" />

        {{-- Sparkles --}}
        <circle class="paf-success-spark paf-spark-1" cx="94" cy="30" r="2.5" fill="currentColor" />
        <circle class="paf-success-spark paf-spark-2" cx="102" cy="52" r="2" fill="currentColor" />
        <circle class="paf-success-spark paf-spark-3" cx="26" cy="94" r="2" fill="currentColor" />
    </svg>
</div>
