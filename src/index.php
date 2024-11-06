<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡単なフォーム</title>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body>
    <h1>簡単なフォーム</h1>
    <form action="submit.php" method="post">
        <label for="name">名前:</label>
        <input type="text" id="name" name="name" value="My name is test">
        <div class="cf-turnstile" data-sitekey="0x4AAAAAAAzZqXah5hYz0ffh"></div>
        <input type="submit" value="送信">
    </form>
</body>
</html>