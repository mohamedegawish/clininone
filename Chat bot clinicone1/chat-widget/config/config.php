<?php
declare(strict_types=1);

/**
 * Lightweight .env loader. Supports KEY=value per line.
 */
function loadEnvFile(string $path): void
{
    if (!is_file($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        $value = trim($value, "\"'");

        $existing = getenv($name);
        if ($name !== '' && ($existing === false || $existing === '')) {
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
        }
    }
}

loadEnvFile(__DIR__ . '/../.env');

return [
    // OpenRouter: https://openrouter.ai — set OPENROUTER_API_KEY in .env
    'openrouter_api_key' => getenv('OPENROUTER_API_KEY') ?: '',
    'openrouter_endpoint' => getenv('OPENROUTER_ENDPOINT') ?: 'https://openrouter.ai/api/v1/chat/completions',
    'openrouter_model' => getenv('OPENROUTER_MODEL') ?: 'deepseek/deepseek-chat-v3.1',
    'openrouter_http_referer' => getenv('OPENROUTER_HTTP_REFERER') ?: '',
    'openrouter_app_title' => getenv('OPENROUTER_APP_TITLE') ?: 'Medical Chat Widget',

    // Basic file-based rate limiting.
    'rate_limit' => [
        'window_seconds' => (int) (getenv('RATE_LIMIT_WINDOW') ?: 60),
        'max_requests' => (int) (getenv('RATE_LIMIT_MAX') ?: 12),
    ],

    // Simple file cache for repeated prompts.
    'cache' => [
        'enabled' => (getenv('CACHE_ENABLED') ?: '1') === '1',
        'ttl_seconds' => (int) (getenv('CACHE_TTL') ?: 120),
    ],

    // Allowlist for CORS if needed. Empty means same-origin.
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', getenv('ALLOWED_ORIGINS') ?: '')
    ))),

    // Trim long outputs for stable UX.
    'max_reply_chars' => (int) (getenv('MAX_REPLY_CHARS') ?: 900),
];
