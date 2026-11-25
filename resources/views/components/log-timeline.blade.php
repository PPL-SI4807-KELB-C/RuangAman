@props([
    'logs',
    'activeLogId' => null,
    'showCaseBadge' => true,
])

@include('log-penanganan._timeline', [
    'logs' => $logs,
    'activeLogId' => $activeLogId,
    'showCaseBadge' => $showCaseBadge,
])
