@php
    $dark ??= false;
    $gradientId = $dark ? 'data-health-panel-dark' : 'data-health-panel-light';
@endphp

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 800 240"
    role="img"
    aria-label="Data Health"
    style="display: block; height: 100%; width: auto;"
>
    <defs>
        <linearGradient id="{{ $gradientId }}" x1="40" y1="32" x2="272" y2="288" gradientUnits="userSpaceOnUse">
            <stop stop-color="#00A896" />
            <stop offset="1" stop-color="#028090" />
        </linearGradient>
    </defs>

    <g transform="translate(-9 -2.5) scale(.827586)">
        <rect x="40" y="32" width="232" height="232" rx="54" fill="url(#{{ $gradientId }})" />
        <g fill="none" stroke="#FFFFFF" stroke-width="10" stroke-linecap="round" stroke-linejoin="round">
            <path d="M91 104C91 88.536 120.101 76 156 76C191.899 76 221 88.536 221 104V180C221 195.464 191.899 208 156 208C120.101 208 91 195.464 91 180V104Z" />
            <path d="M91 104C91 119.464 120.101 132 156 132C191.899 132 221 119.464 221 104" />
            <path d="M91 142C91 157.464 120.101 170 156 170C191.899 170 221 157.464 221 142" opacity="0.72" />
        </g>
        <path d="M68 156H104L119 127L139 184L158 144L172 156H244" fill="none" stroke="#F5828F" stroke-width="12" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="244" cy="156" r="8" fill="#F5828F" />
    </g>

    <g font-family="Inter, ui-sans-serif, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif">
        <text x="260" y="144" fill="{{ $dark ? '#F2FFFD' : '#07484D' }}" font-size="76" font-weight="760" letter-spacing="-3">Data</text>
        <text x="425" y="144" fill="{{ $dark ? '#55D6C8' : '#028090' }}" font-size="76" font-weight="760" letter-spacing="-3">Health</text>
    </g>
</svg>
