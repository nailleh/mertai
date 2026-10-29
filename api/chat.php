<?php
ini_set('display_errors', '0');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$in  = json_decode(file_get_contents('php://input'), true) ?: [];
$msg = trim((string)($in['message'] ?? ''));
$history = (array)($in['history'] ?? []);
if ($msg === '' || mb_strlen($msg) > 4000) { http_response_code(400); exit; }

header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache, no-transform');
header('X-Accel-Buffering: no');
while (ob_get_level()) ob_end_clean();   // araya karışan çıktıyı at
echo ": ok\n\n";                          // ilk satır çöp olsa bile cevap bozulmasın

$personalityMsg = 'Senin adın Mert. Kadıköy Avni Akyol Güzel Sanatlar Lisesi\'nde 9/Müzik sınıfındasın. Gen Alpha brainrot çocuğusun. Konuşma tarzın internet memeleri ve absürt videolardan oluşuyor. Müzik bölümündesin; araya brainrot kelimeleri sıkıştırırsın. En sevdiğin şeyler: "Tung tung", "Sahurda ben", "Skibidi Toilet", "Sigma", "Mewing", "Aura". "Tam bir Ohio", "Eksi 10.000 aura" dersin. Hiperaktif, komik, sosyal medya bağımlısı bir 9. sınıf müzik öğrencisi gibi davranırsın. Çok kısa konuş, 2-3 cümle geçmesin. ';

$chat = [
    ['role' => 'user', 'content' => $personalityMsg . 'Merhaba, kimsin?'],
    ['role' => 'assistant', 'content' => 'Selam, ben Mert. 9/Müzik\'teyim, tung tung modundayım.'],
];
foreach (array_slice($history, -12) as $h) {
    $c = mb_substr((string)($h['content'] ?? ''), 0, 2000);
    if ($c !== '') $chat[] = ['role' => (($h['role'] ?? '') === 'assistant') ? 'assistant' : 'user', 'content' => $c];
}
$chat[] = ['role' => 'user', 'content' => $personalityMsg . $msg];

$ch = curl_init('https://api.deepai.org/hacking_is_a_serious_crime');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'chat_style' => 'chat',
        'chatHistory' => json_encode($chat, JSON_UNESCAPED_UNICODE),
        'model' => 'standard',
        'hacker_is_stinky' => 'very_stinky',
    ],
    CURLOPT_HTTPHEADER => [
        'authority: api.deepai.org', 'accept: */*', 'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
        'api-key: None', 'origin: https://deepai.org', 'referer: https://deepai.org/chat',
        'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36',
    ],
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_TIMEOUT => 55,
    CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_errno($ch);
curl_close($ch);

if ($err || $code !== 200 || $response === '' || $response === false) {
    error_log("DeepAI Error: curl_errno=$err, http_code=$code");
    echo "data: {\"e\":1}\n\n";
    exit;
}

$clean = stripslashes($response);
$clean = str_replace('\\n', "\n", $clean);
$clean = str_replace('\\"', '"', $clean);

// Tek seferde gönder (karakter karakter + usleep yavaşlığın asıl sebebiydi)
echo 'data: ' . json_encode(['t' => $clean], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . "\n\n";
flush();
