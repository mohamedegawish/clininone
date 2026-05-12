<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chat Widget Demo</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #eef4f7;
            color: #002d5b;
        }
        .demo-wrap {
            max-width: 860px;
            margin: 48px auto;
            padding: 0 16px;
        }
        .card {
            background: #fff;
            border: 1px solid rgba(0, 45, 91, 0.1);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 12px 32px rgba(0, 45, 91, 0.08);
        }
    </style>
</head>
<body>
    <main class="demo-wrap">
        <div class="card">
            <h1>Medical Chat Widget Demo</h1>
            <p>
                If the widget is loaded correctly, you will see a floating chat button
                at the bottom-right corner.
            </p>
        </div>
    </main>

    <script src="./frontend/chat-widget.js" data-api-endpoint="./backend/chat.php" defer></script>
</body>
</html>