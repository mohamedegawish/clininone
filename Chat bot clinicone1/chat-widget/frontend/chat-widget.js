(function () {
  "use strict";

  if (window.__MedicalChatWidgetLoaded) return;
  window.__MedicalChatWidgetLoaded = true;

  var scriptEl = document.currentScript;
  var apiEndpoint =
    (scriptEl && scriptEl.getAttribute("data-api-endpoint")) ||
    "/chat-widget/backend/chat.php";

  /* ClinicOne palette */
  var C = {
    teal: "#36B9B6",
    tealDark: "#2a9d9a",
    navy: "#002D5B",
    navyMuted: "#1a3b5d",
    surface: "#E8EEF2",
    surfacePage: "#eef4f7",
    white: "#ffffff",
    textMuted: "#5a7186",
  };

  var texts = {
    title: "ClinicOne",
    subtitle: "SMART MEDICAL · Assistant",
    placeholder: "Type your message...",
    send: "Send",
    bubble: "Chat with ClinicOne",
    typing: "Writing a reply…",
    welcome:
      "Hello! I can help with **doctors**, **specialties**, **appointments**, and general health information.\n\nFor diagnosis or treatment, please **consult a doctor**.",
    error:
      "Sorry, I could not process your request right now. Please try again in a moment.",
  };

  var state = {
    panelLoaded: false,
    isOpen: false,
    isSending: false,
  };

  var root = document.createElement("div");
  root.id = "medical-chat-widget-root";
  document.body.appendChild(root);

  injectStyles();
  renderBubble();

  function injectStyles() {
    var style = document.createElement("style");
    style.textContent = [
      "#medical-chat-widget-root {",
      "  position: fixed;",
      "  right: 20px;",
      "  bottom: 20px;",
      "  z-index: 2147483000;",
      "  font-family: 'Segoe UI', Inter, Roboto, Arial, sans-serif;",
      "}",
      ".mcw-bubble {",
      "  width: 58px;",
      "  height: 58px;",
      "  border-radius: 999px;",
      "  border: 2px solid " + C.white + ";",
      "  background: linear-gradient(145deg, " + C.teal + ", " + C.tealDark + ");",
      "  color: #fff;",
      "  display: flex;",
      "  align-items: center;",
      "  justify-content: center;",
      "  cursor: pointer;",
      "  box-shadow: 0 10px 28px rgba(0, 45, 91, 0.28);",
      "  transition: transform .2s ease, box-shadow .25s ease;",
      "}",
      ".mcw-bubble:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(54, 185, 182, 0.45); }",
      ".mcw-bubble:active { transform: scale(0.97); }",
      ".mcw-bubble svg { display: block; }",
      ".mcw-panel {",
      "  width: min(400px, calc(100vw - 24px));",
      "  height: min(620px, calc(100vh - 95px));",
      "  margin-bottom: 12px;",
      "  border-radius: 18px;",
      "  overflow: hidden;",
      "  display: flex;",
      "  flex-direction: column;",
      "  border: 1px solid rgba(0, 45, 91, 0.12);",
      "  background: " + C.white + ";",
      "  box-shadow: 0 24px 56px rgba(0, 45, 91, 0.18);",
      "  opacity: 0;",
      "  transform: translateY(8px) scale(.98);",
      "  transition: opacity .22s ease, transform .22s ease;",
      "  pointer-events: none;",
      "}",
      ".mcw-panel.open {",
      "  opacity: 1;",
      "  transform: translateY(0) scale(1);",
      "  pointer-events: auto;",
      "}",
      ".mcw-header {",
      "  padding: 14px 16px;",
      "  border-bottom: 1px solid rgba(0, 45, 91, 0.08);",
      "  background: " + C.navy + ";",
      "  color: " + C.white + ";",
      "  display: flex;",
      "  justify-content: space-between;",
      "  align-items: flex-start;",
      "  gap: 12px;",
      "}",
      ".mcw-brand { display: flex; align-items: center; gap: 10px; }",
      ".mcw-brand-mark {",
      "  width: 36px; height: 36px; border-radius: 10px;",
      "  background: rgba(255,255,255,0.12); display: flex; align-items: center; justify-content: center;",
      "}",
      ".mcw-brand-mark svg { width: 20px; height: 20px; stroke: " + C.teal + "; fill: none; stroke-width: 2.2; }",
      ".mcw-title { font-size: 16px; font-weight: 700; letter-spacing: 0.02em; }",
      ".mcw-subtitle { font-size: 11px; color: " + C.teal + "; margin-top: 4px; font-weight: 600; letter-spacing: 0.06em; }",
      ".mcw-close {",
      "  border: none;",
      "  background: rgba(255,255,255,0.1);",
      "  color: " + C.white + ";",
      "  font-size: 18px;",
      "  line-height: 1;",
      "  cursor: pointer;",
      "  width: 32px; height: 32px; border-radius: 10px;",
      "  flex-shrink: 0;",
      "}",
      ".mcw-close:hover { background: rgba(255,255,255,0.18); }",
      ".mcw-messages {",
      "  flex: 1;",
      "  overflow-y: auto;",
      "  padding: 14px;",
      "  display: flex;",
      "  flex-direction: column;",
      "  gap: 12px;",
      "  background: " + C.surfacePage + ";",
      "}",
      ".mcw-msg {",
      "  max-width: 88%;",
      "  border-radius: 14px;",
      "  padding: 11px 14px;",
      "  font-size: 13.5px;",
      "  line-height: 1.55;",
      "  word-break: break-word;",
      "  animation: mcwFadeUp .2s ease;",
      "}",
      ".mcw-msg.bot {",
      "  align-self: flex-start;",
      "  background: " + C.surface + ";",
      "  color: " + C.navy + ";",
      "  border: 1px solid rgba(0, 45, 91, 0.08);",
      "}",
      ".mcw-msg.user {",
      "  align-self: flex-end;",
      "  background: " + C.teal + ";",
      "  color: " + C.white + ";",
      "  border: 1px solid rgba(255,255,255,0.25);",
      "}",
      ".mcw-msg.bot .mcw-md-p { margin: 0 0 0.65em; }",
      ".mcw-msg.bot .mcw-md-p:last-child { margin-bottom: 0; }",
      ".mcw-msg.bot .mcw-md-h {",
      "  margin: 0.4em 0 0.35em; font-size: 1.05em; font-weight: 700; color: " + C.navy + ";",
      "}",
      ".mcw-msg.bot .mcw-md-h:first-child { margin-top: 0; }",
      ".mcw-msg.bot .mcw-md-ul, .mcw-msg.bot .mcw-md-ol {",
      "  margin: 0.35em 0 0.65em; padding-left: 1.25em;",
      "}",
      ".mcw-msg.bot .mcw-md-ul li, .mcw-msg.bot .mcw-md-ol li { margin: 0.25em 0; }",
      ".mcw-msg.bot .mcw-md-pre {",
      "  margin: 0.5em 0; padding: 10px 12px; border-radius: 8px;",
      "  background: " + C.white + "; border: 1px solid rgba(0,45,91,0.1);",
      "  font-size: 12px; overflow-x: auto; white-space: pre-wrap;",
      "}",
      ".mcw-msg.bot strong { color: " + C.navyMuted + "; font-weight: 700; }",
      ".mcw-msg.bot code {",
      "  font-size: 0.92em; padding: 2px 6px; border-radius: 4px;",
      "  background: rgba(54, 185, 182, 0.12); color: " + C.navy + ";",
      "}",
      ".mcw-typing-wrap {",
      "  align-self: flex-start;",
      "  display: flex;",
      "  flex-direction: column;",
      "  gap: 8px;",
      "  max-width: 88%;",
      "}",
      ".mcw-typing-label {",
      "  font-size: 11px; font-weight: 600; color: " + C.tealDark + ";",
      "  letter-spacing: 0.02em;",
      "}",
      ".mcw-typing {",
      "  display: inline-flex;",
      "  align-items: center;",
      "  gap: 5px;",
      "  background: " + C.white + ";",
      "  border: 1px solid rgba(54, 185, 182, 0.35);",
      "  border-radius: 14px;",
      "  padding: 10px 14px;",
      "  box-shadow: 0 4px 14px rgba(54, 185, 182, 0.12);",
      "}",
      ".mcw-dot {",
      "  width: 7px;",
      "  height: 7px;",
      "  border-radius: 999px;",
      "  background: " + C.teal + ";",
      "  animation: mcwTyping 1s infinite ease-in-out;",
      "}",
      ".mcw-dot:nth-child(2) { animation-delay: .15s; }",
      ".mcw-dot:nth-child(3) { animation-delay: .3s; }",
      ".mcw-form {",
      "  padding: 12px 14px;",
      "  display: flex;",
      "  gap: 10px;",
      "  border-top: 1px solid rgba(0, 45, 91, 0.08);",
      "  background: " + C.white + ";",
      "}",
      ".mcw-input {",
      "  flex: 1;",
      "  border: 1px solid rgba(0, 45, 91, 0.12);",
      "  background: " + C.surfacePage + ";",
      "  color: " + C.navy + ";",
      "  border-radius: 12px;",
      "  padding: 10px 14px;",
      "  font-size: 13px;",
      "  outline: none;",
      "}",
      ".mcw-input:focus { border-color: " + C.teal + "; box-shadow: 0 0 0 3px rgba(54, 185, 182, 0.2); }",
      ".mcw-input::placeholder { color: " + C.textMuted + "; }",
      ".mcw-send {",
      "  border: none;",
      "  border-radius: 12px;",
      "  background: " + C.teal + ";",
      "  color: #fff;",
      "  font-weight: 700;",
      "  font-size: 13px;",
      "  min-width: 72px;",
      "  padding: 0 14px;",
      "  cursor: pointer;",
      "  box-shadow: 0 4px 12px rgba(54, 185, 182, 0.35);",
      "}",
      ".mcw-send:hover { background: " + C.tealDark + "; }",
      ".mcw-send[disabled] { opacity: .55; cursor: not-allowed; box-shadow: none; }",
      "@keyframes mcwTyping { 0%, 80%, 100% { transform: translateY(0); opacity: .35; } 40% { transform: translateY(-4px); opacity: 1; } }",
      "@keyframes mcwFadeUp { from { opacity: 0; transform: translateY(4px);} to { opacity: 1; transform: translateY(0);} }",
      "@media (max-width: 480px) {",
      "  #medical-chat-widget-root { right: 12px; bottom: 12px; }",
      "  .mcw-panel { width: calc(100vw - 16px); height: min(78vh, 620px); }",
      "}",
    ].join("\n");
    document.head.appendChild(style);
  }

  function clinicMarkSvg() {
    return (
      '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v16M4 12h16" stroke-linecap="round"/></svg>'
    );
  }

  function renderBubble() {
    var bubble = document.createElement("button");
    bubble.type = "button";
    bubble.className = "mcw-bubble";
    bubble.setAttribute("aria-label", texts.bubble);
    bubble.innerHTML = clinicMarkSvg();
    bubble.addEventListener("click", function () {
      if (!state.panelLoaded) {
        renderPanel();
        state.panelLoaded = true;
      }
      togglePanel(true);
    });
    root.appendChild(bubble);
  }

  function renderPanel() {
    var panel = document.createElement("section");
    panel.className = "mcw-panel";
    panel.innerHTML = [
      '<header class="mcw-header">',
      '  <div class="mcw-brand">',
      '    <div class="mcw-brand-mark">' + clinicMarkSvg() + "</div>",
      "    <div>",
      '      <div class="mcw-title">' + escapeHtml(texts.title) + "</div>",
      '      <div class="mcw-subtitle">' + escapeHtml(texts.subtitle) + "</div>",
      "    </div>",
      "  </div>",
      '  <button type="button" class="mcw-close" aria-label="Close chat">×</button>',
      "</header>",
      '<main class="mcw-messages" aria-live="polite"></main>',
      '<form class="mcw-form">',
      '  <input class="mcw-input" type="text" maxlength="700" placeholder="' +
        escapeHtml(texts.placeholder) +
        '" autocomplete="off" />',
      '  <button class="mcw-send" type="submit">' +
        escapeHtml(texts.send) +
        "</button>",
      "</form>",
    ].join("");

    root.insertBefore(panel, root.firstChild);

    var closeBtn = panel.querySelector(".mcw-close");
    var form = panel.querySelector(".mcw-form");
    var input = panel.querySelector(".mcw-input");
    var sendBtn = panel.querySelector(".mcw-send");
    var messages = panel.querySelector(".mcw-messages");

    closeBtn.addEventListener("click", function () {
      togglePanel(false);
    });

    form.addEventListener("submit", function (event) {
      event.preventDefault();
      submitMessage(input, sendBtn, messages);
    });

    addMessage(messages, "bot", texts.welcome);
    scrollMessages(messages);
  }

  function togglePanel(open) {
    var panel = root.querySelector(".mcw-panel");
    if (!panel) return;

    state.isOpen = open;
    if (open) {
      panel.classList.add("open");
      var input = panel.querySelector(".mcw-input");
      if (input) input.focus();
    } else {
      panel.classList.remove("open");
    }
  }

  function submitMessage(input, sendBtn, messages) {
    var text = (input.value || "").trim();
    if (!text || state.isSending) return;

    addMessage(messages, "user", text);
    input.value = "";
    input.focus();

    state.isSending = true;
    sendBtn.disabled = true;

    var typingEl = showTypingIndicator(messages);

    fetch(apiEndpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: text }),
      credentials: "same-origin",
    })
      .then(function (res) {
        if (!res.ok) throw new Error("Request failed");
        return res.json();
      })
      .then(function (data) {
        removeTypingIndicator(typingEl);
        var reply = data && data.reply ? String(data.reply) : texts.error;
        addMessage(messages, "bot", reply);
      })
      .catch(function () {
        removeTypingIndicator(typingEl);
        addMessage(messages, "bot", texts.error);
      })
      .finally(function () {
        state.isSending = false;
        sendBtn.disabled = false;
      });
  }

  function addMessage(container, sender, text) {
    var msg = document.createElement("div");
    msg.className = "mcw-msg " + sender;
    if (sender === "bot") {
      msg.innerHTML = formatBotContent(text);
    } else {
      msg.textContent = text;
    }
    container.appendChild(msg);
    scrollMessages(container);
  }

  /**
   * Safe markdown subset for assistant replies: code fences, lists, headings, bold, italic, inline code.
   */
  function stripCodeFenceLang(inner) {
    var lines = String(inner).split("\n");
    if (lines.length < 2) return inner;
    var first = lines[0].trim();
    if (/^[a-z][a-z0-9+#.-]*$/i.test(first) && first.length <= 24) {
      return lines.slice(1).join("\n");
    }
    return inner;
  }

  function formatBotContent(raw) {
    var segments = String(raw).split(/```/);
    var html = [];
    for (var i = 0; i < segments.length; i++) {
      if (i % 2 === 1) {
        html.push(
          '<pre class="mcw-md-pre">' + escapeHtml(stripCodeFenceLang(segments[i])) + "</pre>"
        );
      } else if (segments[i]) {
        html.push(formatMarkdownParagraphs(segments[i]));
      }
    }
    return html.join("") || '<p class="mcw-md-p"></p>';
  }

  function formatMarkdownParagraphs(text) {
    var blocks = text.split(/\n\n+/);
    var out = [];
    for (var b = 0; b < blocks.length; b++) {
      var block = blocks[b].trim();
      if (!block) continue;
      out.push(formatMarkdownBlock(block));
    }
    return out.join("");
  }

  function formatMarkdownBlock(block) {
    var lines = block.split(/\n/);
    var allBullet =
      lines.length > 0 &&
      lines.every(function (l) {
        return l.trim() === "" || /^\s*[-*]\s+/.test(l);
      });
    var bulletLines = lines.filter(function (l) {
      return l.trim() !== "";
    });
    if (allBullet && bulletLines.length > 0 && bulletLines.every(function (l) { return /^\s*[-*]\s+/.test(l); })) {
      var lis = bulletLines.map(function (line) {
        var m = line.match(/^\s*[-*]\s+(.+)$/);
        return m ? "<li>" + applyInlineMarkdown(escapeHtml(m[1])) + "</li>" : "";
      });
      return '<ul class="mcw-md-ul">' + lis.join("") + "</ul>";
    }

    var allNum =
      lines.length > 0 &&
      lines.every(function (l) {
        return l.trim() === "" || /^\s*\d+\.\s+/.test(l);
      });
    var numLines = lines.filter(function (l) {
      return l.trim() !== "";
    });
    if (allNum && numLines.length > 0 && numLines.every(function (l) { return /^\s*\d+\.\s+/.test(l); })) {
      var ois = numLines.map(function (line) {
        var m = line.match(/^\s*\d+\.\s+(.+)$/);
        return m ? "<li>" + applyInlineMarkdown(escapeHtml(m[1])) + "</li>" : "";
      });
      return '<ol class="mcw-md-ol">' + ois.join("") + "</ol>";
    }

    if (lines.length === 1) {
      var h4 = lines[0].match(/^####\s+(.+)$/);
      if (h4) return '<h4 class="mcw-md-h">' + applyInlineMarkdown(escapeHtml(h4[1])) + "</h4>";
      var h3 = lines[0].match(/^###\s+(.+)$/);
      if (h3) return '<h4 class="mcw-md-h">' + applyInlineMarkdown(escapeHtml(h3[1])) + "</h4>";
      var h2 = lines[0].match(/^##\s+(.+)$/);
      if (h2) return '<h3 class="mcw-md-h">' + applyInlineMarkdown(escapeHtml(h2[1])) + "</h3>";
      var h1 = lines[0].match(/^#\s+(.+)$/);
      if (h1) return '<h3 class="mcw-md-h">' + applyInlineMarkdown(escapeHtml(h1[1])) + "</h3>";
    }

    var esc = escapeHtml(block).replace(/\n/g, "<br>");
    return '<p class="mcw-md-p">' + applyInlineMarkdown(esc) + "</p>";
  }

  function applyInlineMarkdown(s) {
    s = s.replace(/`([^`]+)`/g, "<code>$1</code>");
    s = s.replace(/\*\*([^*]+)\*\*/g, "<strong>$1</strong>");
    s = s.replace(/\*([^*]+)\*/g, "<em>$1</em>");
    return s;
  }

  function showTypingIndicator(container) {
    var wrap = document.createElement("div");
    wrap.className = "mcw-typing-wrap";
    wrap.setAttribute("aria-label", texts.typing);
    wrap.innerHTML =
      '<span class="mcw-typing-label">' +
      escapeHtml(texts.typing) +
      '</span><div class="mcw-typing" aria-hidden="true">' +
      '<span class="mcw-dot"></span><span class="mcw-dot"></span><span class="mcw-dot"></span></div>';
    container.appendChild(wrap);
    scrollMessages(container);
    return wrap;
  }

  function removeTypingIndicator(el) {
    if (el && el.parentNode) el.parentNode.removeChild(el);
  }

  function scrollMessages(container) {
    container.scrollTop = container.scrollHeight;
  }

  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
})();
