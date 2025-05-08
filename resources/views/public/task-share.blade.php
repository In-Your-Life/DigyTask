<html>
<head>
    <title>Task condiviso</title>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        h1 { color: #2563eb; }
        .desc { margin-top: 1rem; }
    </style>
</head>
<body>
    <h1>{{ \$task->title }}</h1>
    <div class="desc">{!! nl2br(e(\$task->description)) !!}</div>
    <div style="margin-top:2rem; color:#888; font-size:0.9em;">Questa è una copia statica condivisa. Alcune funzionalità potrebbero non essere disponibili.</div>
</body>
</html> 