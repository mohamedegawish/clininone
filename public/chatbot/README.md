# AI Medical Chat Widget (Angular + PHP + Gemini)

Production-ready lightweight widget for medical booking websites:
- Frontend: lazy-loaded floating chat widget (`chat-widget.js`)
- Backend: secure PHP middleware (`chat.php`)
- AI: Google Gemini API via server-side cURL (`gemini-pro`)

## Project Structure

```text
/chat-widget/
├── frontend/
│   └── chat-widget.js
├── backend/
│   └── chat.php
├── config/
│   └── config.php
├── .env.example
└── README.md
```

## Features

- Floating bottom-right chat bubble
- Dark glassmorphism UI with smooth animations
- Intercom/WhatsApp-like message experience
- Typing indicator + auto-scroll
- Lazy panel initialization on first click (non-blocking to main page)
- Secure server-side Gemini calls (API key never exposed to frontend)
- Basic IP rate limiting
- Short-term response caching for repeated prompts
- Booking intent shortcut:
  - Returns: `Please use the booking form on the website to confirm your appointment.`
- Strict medical-safety system instruction and output boundaries

## 1) Backend Setup (PHP API)

1. Copy env file:
   - `cp .env.example .env` (Linux/macOS)
   - or manually create `.env` in `/chat-widget/`
2. Set your key:
   - `GEMINI_API_KEY=...`
3. Start PHP server from `/chat-widget/`:
   - `php -S localhost:8080`
4. Endpoint:
   - `POST http://localhost:8080/backend/chat.php`

### Request JSON

```json
{ "message": "I need a cardiology appointment" }
```

### Response JSON

```json
{ "reply": "Please use the booking form on the website to confirm your appointment." }
```

## 2) Angular Integration

Add widget script globally (e.g. in `src/index.html`):

```html
<script
  src="/assets/chat-widget.js"
  data-api-endpoint="https://your-domain.com/chat-widget/backend/chat.php"
  defer
></script>
```

Recommended deployment approach:
1. Copy `frontend/chat-widget.js` into Angular public assets (`src/assets/chat-widget.js`).
2. Deploy PHP backend under same domain when possible.
3. If cross-origin is required, set `ALLOWED_ORIGINS` in `.env`.

## 3) Security Notes

- Gemini key is loaded server-side from `.env` (never exposed to browser).
- `chat.php` enforces:
  - method and payload validation
  - max message size
  - rate limiting per IP
  - optional CORS allowlist
  - response length cap
- Keep `.env` outside public static hosting.

## 4) Performance Notes

- Widget panel DOM and listeners are initialized only when first opened.
- API uses short timeouts and cache for repeated questions.
- Backend returns compact JSON only.

## 5) Behavior Rules Implemented

The assistant is constrained to:
- doctors/hospitals/specialties/appointments
- non-diagnostic general health awareness

The assistant is explicitly instructed to:
- avoid diagnosis, prescriptions, and dangerous advice
- recommend consulting a doctor
- refuse non-medical topics politely
- never access database or fabricate booking/doctor data

## 6) Production Hardening (Recommended)

- Put PHP behind Nginx/Apache + HTTPS
- Move `.runtime` cache/rate files to writable secure storage
- Add centralized logging/monitoring
- Add WAF and stricter abuse controls
- Add integration tests for endpoint and guardrail responses
