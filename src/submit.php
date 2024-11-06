<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $token = $_POST['cf-turnstile-response'] ?? null;
    $secretKey = '0x4AAAAAAAzZqTIG7TaacGERUcfc671bUTY';
    $ip = $_SERVER['REMOTE_ADDR'];

    $data = [
        'secret' => $secretKey,
        'response' => $token,
        'remoteip' => $ip,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
    $result = json_decode($response);

    if ($result->success) {
        error_log("name : " . $name);
        echo "回答ありがとうございました．";
    } else {
        echo 'CAPTCHA の検証に失敗しました。もう一度お試しください。';
    }
}
?>