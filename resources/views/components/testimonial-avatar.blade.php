@props([
    'name' => 'Golden Eye student',
    'photo' => null,
    'size' => 54,
])

@php
    $normalizedPhoto = trim((string) $photo);
    $photoPath = parse_url($normalizedPhoto, PHP_URL_PATH) ?: $normalizedPhoto;
    $photoFilename = strtolower(basename(str_replace('\\', '/', $photoPath)));
    $isRemotePortrait = \Illuminate\Support\Str::startsWith($normalizedPhoto, ['http://', 'https://', '//']);
    $localPortraitPath = ltrim(str_replace('\\', '/', $photoPath), '/');
    $hasStudentPortrait = $normalizedPhoto !== ''
        && $photoFilename !== 'user.png'
        && ($isRemotePortrait || (is_file(public_path($localPortraitPath)) && filesize(public_path($localPortraitPath)) > 0));
    $portraitUrl = $isRemotePortrait
        ? $normalizedPhoto
        : asset($localPortraitPath);
    $accessibleName = preg_replace('/\s+/u', ' ', trim(strip_tags((string) $name))) ?: 'Golden Eye student';
    $initials = collect(preg_split('/\s+/u', $accessibleName) ?: [])
        ->filter()
        ->take(2)
        ->map(fn (string $namePart): string => mb_strtoupper(mb_substr($namePart, 0, 1)))
        ->implode('') ?: 'GE';
    $avatarSize = max(40, (int) $size);
@endphp

<span
    {{ $attributes->class(['testimonial-avatar flex-shrink-0']) }}
    style="--testimonial-avatar-size: {{ $avatarSize }}px;"
    role="img"
    aria-label="{{ $accessibleName }}"
    data-testimonial-avatar="{{ $initials }}"
>
    <span class="testimonial-avatar-initials" aria-hidden="true">{{ $initials }}</span>

    @if($hasStudentPortrait)
        <img
            src="{{ $portraitUrl }}"
            alt=""
            class="testimonial-avatar-photo"
            loading="lazy"
            decoding="async"
            width="{{ $avatarSize }}"
            height="{{ $avatarSize }}"
            onerror="this.remove()"
        >
    @endif
</span>
