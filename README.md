# PHP Webアプリケーション向け Cloudflare Turnstile 統合ガイド

### 1. **概要**

Cloudflare Turnstile は、スパムや不正アクセスからフォームやウェブページを保護するためのソリューションです。本ガイドでは、PHPを用いたWebアプリケーションでTurnstileを導入し、設定する手順を説明します。

---

### 2. **必要条件**

- PHP 7.x 以上
- Cloudflare Turnstile の sitekey および secret key

---

### 3. **クライアントサイドでのTurnstile設定**

**ステップ1**: ターンスタイルJavaScriptをページに追加します。

```html
<script src="<https://challenges.cloudflare.com/turnstile/v0/api.js>" async defer></script>

```

**ステップ2**: フォームにTurnstileウィジェットを追加します。

```html
<form action="/your_form_action.php" method="POST">
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <div class="cf-turnstile" data-sitekey="YOUR_SITE_KEY"></div>
  <button type="submit">Submit</button>
</form>

```

---

### 4. **PHPでのサーバーサイド検証**

Cloudflare Turnstile トークンは、サーバー側で有効性を検証し、再利用されていないことを確認する必要があります。

**ステップ1**: フォーム送信時に `cf-turnstile-response` トークンを取得します。

```php
$token = $_POST['cf-turnstile-response'] ?? null;

```

**ステップ2**: `siteverify` エンドポイントを使用してトークンを検証します。

```php
$secretKey = 'YOUR_SECRET_KEY';
$token = $_POST['cf-turnstile-response'];
$ip = $_SERVER['REMOTE_ADDR'];

$data = [
    'secret' => $secretKey,
    'response' => $token,
    'remoteip' => $ip,
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\\r\\n",
        'method'  => 'POST',
        'content' => http_build_query($data),
    ],
];

$context  = stream_context_create($options);
$response = file_get_contents('<https://challenges.cloudflare.com/turnstile/v0/siteverify>', false, $context);
$result = json_decode($response);

if ($result->success) {
    // トークンの検証に成功した場合の処理
} else {
    // トークンの検証が失敗した場合の処理
    echo 'CAPTCHA の検証に失敗しました。もう一度お試しください。';
}

```

---

### 5. **高度な設定**

### 再試行用の冪等性

リクエストを冪等にするために、`idempotency_key` を追加して再試行リクエストを処理します。

```php
$data['idempotency_key'] = uniqid('', true);

```

### エラーハンドリング

エラーコードを確認し、適切にエラーメッセージを表示します。

```php
if (!$result->success) {
    foreach ($result->{'error-codes'} as $error) {
        echo "エラー: $error";
    }
}

```

---

### 6. **統合テスト**

1. 正しい入力でフォームを送信し、正常に検証が完了することを確認します。
2. CAPTCHAを解かずに送信して、フォーム送信がブロックされることを確認します。
3. 同じトークンを再利用して送信し、再利用されたトークンがブロックされることを確認します。

---

### 7. **追加設定オプション**

Cloudflareドキュメントを参照し、次のカスタマイズオプションを設定可能です:

- **表示テーマ**: `data-theme="dark"` または `data-theme="light"`
- **ウィジェットサイズ**: `data-size="compact"` または `data-size="flexible"`
- **実行タイミング制御**: `data-execution="execute"` により、ウィジェットのチャレンジを実行するタイミングを制御

---

以上で、Cloudflare Turnstile のPHP Webアプリケーションへの統合手順書は完了です。