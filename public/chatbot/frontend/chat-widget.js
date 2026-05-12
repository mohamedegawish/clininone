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
    teal: "#2563eb",
    tealDark: "#1f5d96",
    navy: "#002D5B",
    navyMuted: "#1a3b5d",
    surface: "#E8EEF2",
    surfacePage: "#eef4f7",
    white: "#ffffff",
    textMuted: "#5a7186",
  };

  var isRtl = document.documentElement.dir === "rtl";
  
  var texts = {
    title: "ClinicOne",
    subtitle: isRtl ? "مساعد ذكي · ClinicOne" : "SMART MEDICAL · Assistant",
    placeholder: isRtl ? "اكتب رسالتك هنا..." : "Type your message...",
    send: isRtl ? "إرسال" : "Send",
    bubble: isRtl ? "تحدث مع ClinicOne" : "Chat with ClinicOne",
    typing: isRtl ? "جاري كتابة الرد..." : "Writing a reply…",
    welcome: isRtl
      ? "مرحباً! يمكنني مساعدتك في العثور على **الأطباء**، **التخصصات**، **المواعيد**، ومعلومات صحية عامة.\n\nللتشخيص أو العلاج، يرجى **استشارة الطبيب**."
      : "Hello! I can help with **doctors**, **specialties**, **appointments**, and general health information.\n\nFor diagnosis or treatment, please **consult a doctor**.",
    error: isRtl
      ? "عذراً، لم أتمكن من معالجة طلبك الآن. يرجى المحاولة مرة أخرى بعد قليل."
      : "Sorry, I could not process your request right now. Please try again in a moment.",
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
      "  font-family: 'Cairo', 'Segoe UI', Inter, Roboto, Arial, sans-serif;",
      "  direction: " + (isRtl ? "rtl" : "ltr") + ";",
      "}",
      ".mcw-bubble {",
      "  width: 62px;",
      "  height: 62px;",
      "  border-radius: 999px;",
      "  border: 1px solid rgba(255, 255, 255, 0.4);",
      "  background: linear-gradient(135deg, " + C.teal + ", " + C.tealDark + ");",
      "  color: #fff;",
      "  display: flex;",
      "  align-items: center;",
      "  justify-content: center;",
      "  cursor: pointer;",
      "  box-shadow: 0 12px 32px rgba(37, 99, 235, 0.35);",
      "  transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);",
      "  backdrop-filter: blur(8px);",
      "  position: relative;",
      "  z-index: 2;",
      "}",
      ".mcw-bubble:hover { transform: translateY(-4px) scale(1.05); box-shadow: 0 16px 48px rgba(37, 99, 235, 0.5); }",
      ".mcw-bubble:active { transform: scale(0.95); }",
      ".mcw-bubble svg { width: 30px; height: 30px; stroke-width: 2.5; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }",
      ".mcw-panel {",
      "  position: absolute;",
      "  bottom: 80px;",
      "  right: 0;",
      "  width: min(420px, calc(100vw - 40px));",
      "  height: min(640px, calc(100vh - 120px));",
      "  border-radius: 24px;",
      "  overflow: hidden;",
      "  display: flex;",
      "  flex-direction: column;",
      "  border: 1px solid rgba(255, 255, 255, 0.2);",
      "  background: rgba(255, 255, 255, 0.95);",
      "  backdrop-filter: blur(20px);",
      "  box-shadow: 0 32px 80px rgba(0, 45, 91, 0.25);",
      "  opacity: 0;",
      "  transform: translateY(20px) scale(.95);",
      "  transition: all .4s cubic-bezier(0.16, 1, 0.3, 1);",
      "  pointer-events: none;",
      "  transform-origin: bottom right;",
      "  z-index: 1;",
      "}",
      ".mcw-panel.open {",
      "  opacity: 1;",
      "  transform: translateY(0) scale(1);",
      "  pointer-events: auto;",
      "}",
      ".mcw-header {",
      "  padding: 20px 24px;",
      "  background: linear-gradient(135deg, " + C.navy + ", " + C.navyMuted + ");",
      "  color: " + C.white + ";",
      "  display: flex;",
      "  justify-content: space-between;",
      "  align-items: center;",
      "  gap: 12px;",
      "  box-shadow: 0 4px 12px rgba(0,0,0,0.1);",
      "}",
      ".mcw-brand { display: flex; align-items: center; gap: 14px; }",
      ".mcw-brand-mark {",
      "  width: 42px; height: 42px; border-radius: 12px;",
      "  background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center;",
      "  backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.1);",
      "}",
      ".mcw-brand-mark svg { width: 24px; height: 24px; stroke: #fff; fill: none; stroke-width: 2.5; }",
      ".mcw-title { font-size: 18px; font-weight: 800; letter-spacing: -0.01em; }",
      ".mcw-subtitle { font-size: 11px; color: " + C.teal + "; margin-top: 2px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; opacity: 0.9; }",
      ".mcw-close {",
      "  border: none;",
      "  background: rgba(255,255,255,0.1);",
      "  color: " + C.white + ";",
      "  font-size: 20px;",
      "  cursor: pointer;",
      "  width: 36px; height: 36px; border-radius: 12px;",
      "  display: flex; align-items: center; justify-content: center;",
      "  transition: all .2s;",
      "}",
      ".mcw-close:hover { background: rgba(255,255,255,0.2); transform: rotate(90deg); }",
      ".mcw-messages {",
      "  flex: 1;",
      "  overflow-y: auto;",
      "  padding: 24px;",
      "  display: flex;",
      "  flex-direction: column;",
      "  gap: 16px;",
      "  background: linear-gradient(to bottom, #f8fafc, #f1f5f9);",
      "  scrollbar-width: thin;",
      "  scrollbar-color: rgba(0,0,0,0.1) transparent;",
      "}",
      ".mcw-msg {",
      "  max-width: 85%;",
      "  border-radius: 20px;",
      "  padding: 12px 18px;",
      "  font-size: 14.5px;",
      "  line-height: 1.6;",
      "  word-break: break-word;",
      "  box-shadow: 0 2px 4px rgba(0,0,0,0.02);",
      "}",
      ".mcw-msg.bot {",
      "  align-self: flex-start;",
      "  background: #fff;",
      "  color: " + C.navy + ";",
      "  border: 1px solid #e2e8f0;",
      "  border-bottom-left-radius: 4px;",
      "}",
      ".mcw-msg.user {",
      "  align-self: flex-end;",
      "  background: linear-gradient(135deg, " + C.teal + ", " + C.tealDark + ");",
      "  color: " + C.white + ";",
      "  border-bottom-right-radius: 4px;",
      "  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);",
      "}",
      ".mcw-typing-wrap {",
      "  align-self: flex-start;",
      "  display: flex;",
      "  flex-direction: column;",
      "  gap: 8px;",
      "}",
      ".mcw-typing-label {",
      "  font-size: 11px; font-weight: 700; color: " + C.tealDark + "; opacity: 0.8; margin-inline-start: 4px;",
      "}",
      ".mcw-typing {",
      "  display: inline-flex;",
      "  gap: 6px;",
      "  background: #fff;",
      "  border-radius: 16px;",
      "  padding: 12px 16px;",
      "  border: 1px solid #e2e8f0;",
      "  box-shadow: 0 4px 12px rgba(0,0,0,0.03);",
      "}",
      ".mcw-dot {",
      "  width: 6px;",
      "  height: 6px;",
      "  border-radius: 50%;",
      "  background: " + C.teal + ";",
      "  animation: mcwTyping 1.4s infinite ease-in-out;",
      "}",
      ".mcw-form {",
      "  padding: 20px 24px;",
      "  display: flex;",
      "  gap: 12px;",
      "  background: #fff;",
      "  border-top: 1px solid #f1f5f9;",
      "}",
      ".mcw-input {",
      "  flex: 1;",
      "  border: 1px solid #e2e8f0;",
      "  background: #f8fafc;",
      "  color: " + C.navy + ";",
      "  border-radius: 16px;",
      "  padding: 12px 18px;",
      "  font-size: 14px;",
      "  transition: all .2s;",
      "}",
      ".mcw-input:focus { border-color: " + C.teal + "; background: #fff; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }",
      ".mcw-send {",
      "  border: none;",
      "  border-radius: 16px;",
      "  background: linear-gradient(135deg, " + C.teal + ", " + C.tealDark + ");",
      "  color: #fff;",
      "  width: 52px; height: 52px;",
      "  display: flex; align-items: center; justify-content: center;",
      "  cursor: pointer;",
      "  transition: all .3s cubic-bezier(0.34, 1.56, 0.64, 1);",
      "  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);",
      "}",
      ".mcw-send svg { width: 22px; height: 22px; stroke: #fff; fill: none; stroke-width: 2.5; }",
      ".mcw-send:hover { transform: translateY(-2px) scale(1.05); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4); }",
      ".mcw-send[disabled] { opacity: .5; transform: none; box-shadow: none; }",
      "@keyframes mcwTyping { 0%, 80%, 100% { transform: translateY(0); opacity: .4; } 40% { transform: translateY(-6px); opacity: 1; } }",
      "@media (max-width: 480px) {",
      "  #medical-chat-widget-root { right: 16px; bottom: 16px; }",
      "  .mcw-panel { width: calc(100vw - 32px); height: min(75vh, 620px); border-radius: 20px; }",
      "}",
    ].join("\n");
    document.head.appendChild(style);
  }

  function clinicMarkSvg() {
    return (
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>'
    );
  }

  function sendIconSvg() {
    return (
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>'
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
        sendIconSvg() +
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
    var botMsgEl = null;
    var fullReply = "";

    fetch(apiEndpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: text }),
      credentials: "same-origin",
    })
      .then(function (res) {
        if (!res.ok) throw new Error("Request failed");
        removeTypingIndicator(typingEl);
        
        // Create bot message element early for streaming
        botMsgEl = document.createElement("div");
        botMsgEl.className = "mcw-msg bot";
        messages.appendChild(botMsgEl);
        scrollMessages(messages);

        var reader = res.body.getReader();
        var decoder = new TextDecoder();
        var buffer = "";

        return readStream();

        function readStream() {
          return reader.read().then(function (result) {
            if (result.done) return;

            var chunk = decoder.decode(result.value, { stream: true });
            buffer += chunk;
            
            var lines = buffer.split("\n");
            // Keep the last partial line in the buffer
            buffer = lines.pop();

            lines.forEach(function (line) {
              if (line.startsWith("data: ")) {
                var dataStr = line.replace(/^data: /, "").trim();
                if (!dataStr || dataStr === "[DONE]") return;

                try {
                  var data = JSON.parse(dataStr);
                  var content = (data.choices && data.choices[0].delta && data.choices[0].delta.content) || "";
                  if (content) {
                    fullReply += content;
                    botMsgEl.innerHTML = formatBotContent(fullReply);
                    scrollMessages(messages);
                  }
                } catch (e) {
                  // If JSON is incomplete, we could theoretically put it back in buffer, 
                  // but usually line split is enough for SSE.
                }
              }
            });

            return readStream();
          });
        }
      })
      .catch(function (err) {
        removeTypingIndicator(typingEl);
        if (!botMsgEl) {
          addMessage(messages, "bot", texts.error);
        } else {
          botMsgEl.innerHTML = formatBotContent(fullReply + "\n\n" + texts.error);
        }
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
