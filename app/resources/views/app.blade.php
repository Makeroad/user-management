<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー管理システム課題</title>
    @vite('resources/js/main.js')
</head>
<body>
    <div id="app" data-user='@json($user ?? null)'></div>
</body>
</html>