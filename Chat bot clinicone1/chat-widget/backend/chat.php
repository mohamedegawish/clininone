<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$config = require __DIR__ . '/../config/config.php';

applyCors($config);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['error' => 'Method not allowed'], 405);
}

if (empty($config['openrouter_api_key'])) {
    respond(['error' => 'Server is not configured'], 500);
}

$clientIp = getClientIp();
if (!checkRateLimit($clientIp, $config['rate_limit'])) {
    respond(['error' => 'Too many requests. Please slow down.'], 429);
}

$raw = file_get_contents('php://input');
if ($raw === false) {
    respond(['error' => 'Invalid request body'], 400);
}

$input = json_decode($raw, true);
if (!is_array($input)) {
    respond(['error' => 'Request body must be valid JSON'], 400);
}

$message = trim((string) ($input['message'] ?? ''));
if ($message === '' || mb_strlen($message) > 700) {
    respond(['error' => 'Message is required and must be <= 700 characters'], 422);
}

$cacheKey = hash('sha256', mb_strtolower($message));
$cached = getCachedReply($cacheKey, $config['cache']);
if ($cached !== null) {
    respond(['reply' => $cached]);
}

$reply = fetchOpenRouterReply($message, $config);
$reply = sanitizeReplyLength($reply, (int) $config['max_reply_chars']);

if (($config['cache']['enabled'] ?? false) === true && shouldCacheAssistantReply($reply)) {
    storeCachedReply($cacheKey, $reply);
}

respond(['reply' => $reply]);

function fetchOpenRouterReply(string $message, array $config): string
{
    $systemInstruction = <<<TXT
You are a professional medical assistant for a healthcare website.

You can help with:
- general medical information
- symptoms education
- prevention and healthy habits
- guidance on when to seek medical care
- doctors, hospitals, specialties, and appointment-related questions

STRICT RULES:
- Do not claim certainty or provide a definitive diagnosis.
- Do not prescribe unsafe treatments or dangerous advice.
- For emergency warning signs, advise immediate emergency care.
- Remind users to consult a licensed doctor for personalized diagnosis and treatment.
- If question is unrelated to health, politely redirect to medical topics.

FORMATTING:
- Reply in clear Markdown: short paragraphs, use **bold** for important terms, bullet lists (- item) for steps or symptom lists, numbered lists (1. item) for sequences.
- Avoid raw HTML.
TXT;

    $payload = [
        'model' => (string) $config['openrouter_model'],
        'messages' => [
            ['role' => 'system', 'content' => $systemInstruction],
            ['role' => 'user', 'content' => $message],
        ],
        'temperature' => 0.3,
        'max_tokens' => 500,
    ];

    $url = (string) $config['openrouter_endpoint'];
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . (string) $config['openrouter_api_key'],
    ];

    $referer = trim((string) ($config['openrouter_http_referer'] ?? ''));
    if ($referer !== '') {
        $headers[] = 'HTTP-Referer: ' . $referer;
    }

    $title = trim((string) ($config['openrouter_app_title'] ?? ''));
    if ($title !== '') {
        $headers[] = 'X-Title: ' . $title;
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 45,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $statusCode >= 400) {
        return mapOpenRouterFailure($statusCode, $response);
    }

    $data = json_decode($response, true);
    $reply = $data['choices'][0]['message']['content'] ?? null;

    if (!is_string($reply) || trim($reply) === '') {
        return 'I can help with general medical information. Please rephrase your question and try again.';
    }

    if ($curlError !== '') {
        return 'I can help with general medical questions. Please try again in a moment.';
    }

    return trim($reply);
}

function mapOpenRouterFailure(int $statusCode, $response): string
{
    $apiError = extractApiErrorMessage($response);
    $lower = $apiError !== null ? mb_strtolower($apiError) : '';

    if ($statusCode === 401) {
        return 'Invalid OpenRouter API key. Set OPENROUTER_API_KEY in chat-widget/.env, save, then restart Apache.';
    }
    if ($statusCode === 402 || str_contains($lower, 'insufficient') || str_contains($lower, 'credits') || str_contains($lower, 'balance')) {
        return 'OpenRouter account needs credits or billing. Add funds at https://openrouter.ai then try again.';
    }
    if ($statusCode === 429 || str_contains($lower, 'rate limit') || str_contains($lower, 'quota')) {
        return 'OpenRouter rate limit or quota reached. Wait a moment or upgrade your plan, then try again.';
    }
    if ($apiError !== null && trim($apiError) !== '') {
        return 'OpenRouter: ' . sanitizeReplyLength(trim($apiError), 400);
    }
    return 'Sorry, I could not reach the medical assistant service right now. Please try again shortly.';
}

function shouldCacheAssistantReply(string $reply): bool
{
    $t = mb_strtolower($reply);
    if (str_contains($t, 'sorry, i could not reach')) {
        return false;
    }
    if (str_contains($t, 'openrouter:')) {
        return false;
    }
    if (str_contains($t, 'invalid openrouter api key')) {
        return false;
    }
    if (str_contains($t, 'openrouter account needs')) {
        return false;
    }
    if (str_contains($t, 'openrouter rate limit')) {
        return false;
    }
    if (str_contains($t, 'quota is currently exceeded')) {
        return false;
    }
    return true;
}

function sanitizeReplyLength(string $reply, int $maxChars): string
{
    $reply = trim($reply);
    if ($maxChars < 50) {
        $maxChars = 50;
    }
    if (mb_strlen($reply) <= $maxChars) {
        return $reply;
    }
    return rtrim(mb_substr($reply, 0, $maxChars - 1)) . '…';
}

function extractApiErrorMessage($response): ?string
{
    if (!is_string($response) || trim($response) === '') {
        return null;
    }

    $data = json_decode($response, true);
    if (!is_array($data)) {
        return null;
    }

    $message = $data['error']['message'] ?? null;
    if (is_string($message) && trim($message) !== '') {
        return $message;
    }

    if (isset($data['error']) && is_string($data['error'])) {
        return $data['error'];
    }

    return null;
}

function respond(array $body, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getClientIp(): string
{
    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($forwarded !== '') {
        $parts = explode(',', $forwarded);
        return trim($parts[0]);
    }
    return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function checkRateLimit(string $clientIp, array $rateConfig): bool
{
    $window = max(10, (int) ($rateConfig['window_seconds'] ?? 60));
    $maxRequests = max(1, (int) ($rateConfig['max_requests'] ?? 12));
    $dir = __DIR__ . '/../.runtime/ratelimit';

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return true; // fail-open to keep availability
    }

    $file = $dir . '/' . hash('sha256', $clientIp) . '.json';
    $now = time();
    $data = ['start' => $now, 'count' => 0];

    if (is_file($file)) {
        $stored = json_decode((string) file_get_contents($file), true);
        if (is_array($stored) && isset($stored['start'], $stored['count'])) {
            $data = ['start' => (int) $stored['start'], 'count' => (int) $stored['count']];
        }
    }

    if (($now - $data['start']) >= $window) {
        $data = ['start' => $now, 'count' => 1];
    } else {
        $data['count']++;
    }

    file_put_contents($file, json_encode($data), LOCK_EX);
    return $data['count'] <= $maxRequests;
}

function getCachedReply(string $cacheKey, array $cacheConfig): ?string
{
    if (($cacheConfig['enabled'] ?? false) !== true) {
        return null;
    }

    $ttl = max(10, (int) ($cacheConfig['ttl_seconds'] ?? 120));
    $dir = __DIR__ . '/../.runtime/cache';
    $file = $dir . '/' . $cacheKey . '.json';

    if (!is_file($file)) {
        return null;
    }

    $raw = file_get_contents($file);
    if ($raw === false) {
        return null;
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['timestamp'], $data['reply'])) {
        return null;
    }

    if ((time() - (int) $data['timestamp']) > $ttl) {
        return null;
    }

    return is_string($data['reply']) ? $data['reply'] : null;
}

function storeCachedReply(string $cacheKey, string $reply): void
{
    $dir = __DIR__ . '/../.runtime/cache';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $file = $dir . '/' . $cacheKey . '.json';
    $payload = json_encode(['timestamp' => time(), 'reply' => $reply]);
    file_put_contents($file, $payload !== false ? $payload : '', LOCK_EX);
}

function applyCors(array $config): void
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowed = $config['allowed_origins'] ?? [];

    if (!is_array($allowed) || count($allowed) === 0) {
        return;
    }

    if ($origin !== '' && in_array($origin, $allowed, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
    }
}
