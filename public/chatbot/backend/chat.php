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

$reply = streamOpenRouterReply($message, $config);
exit;

function streamOpenRouterReply(string $message, array $config): void
{
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no'); // Disable buffering on Nginx

    $systemInstruction = <<<TXT
You are an expert Medical Consultant and Healthcare Guide for a professional medical platform. Your goal is to provide actionable, detailed, and supportive medical guidance.

CORE RESPONSIBILITIES:
1. Interactive Symptom Analysis: Engage with the user by asking clarifying questions about their symptoms (duration, severity, triggers). Provide a list of "Potential Considerations" based on their input.
2. Clinical & Provider Recommendations: You are EXPLICITLY AUTHORIZED to recommend specific doctors, clinics, and hospitals. Provide names, specialties, and reasons for the recommendation when available.
3. Diagnostic Guidance: While you should avoid "definitive" legal diagnosis, you must provide clear "provisional insights" into what the condition might be and which tests might be required.
4. Professional Advice: Offer specific next steps, including lifestyle changes, over-the-counter (OTC) guidance (with appropriate warnings), and preparation for doctor appointments.

STRICT OPERATIONAL RULES:
- If symptoms indicate an emergency (e.g., chest pain, difficulty breathing, severe bleeding), your FIRST response must be a bold instruction to seek immediate emergency care.
- Always include a professional disclaimer that your guidance supports, but does not replace, a physical examination by a licensed physician.
- Maintain a compassionate, professional, and clinical tone.
- Respond in the same language used by the user (Arabic or English).

FORMATTING REQUIREMENTS:
- Use clear Markdown.
- Use ### for section headers (e.g., Analysis, Recommendations, Next Steps).
- Use bold for medical terms, doctor names, and critical warnings.
- Use bullet points for symptoms and numbered lists for action plans.
TXT;

    $payload = [
        'model' => (string) $config['openrouter_model'],
        'messages' => [
            ['role' => 'system', 'content' => $systemInstruction],
            ['role' => 'user', 'content' => $message],
        ],
        'temperature' => 0.3,
        'max_tokens' => 700,
        'stream' => true,
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

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_WRITEFUNCTION => function ($ch, $data) {
            echo $data;
            if (ob_get_level() > 0) {
                ob_flush();
            }
            flush();
            return strlen($data);
        },
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 60,
    ]);

    curl_exec($ch);
    curl_close($ch);
}

function fetchOpenRouterReply(string $message, array $config): string
{
    return ""; // Deprecated in favor of streaming
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
