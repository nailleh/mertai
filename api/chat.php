<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$in  = json_decode(file_get_contents('php://input'), true) ?: [];
$msg = trim((string)($in['message'] ?? ''));
$id  = (string)($in['id'] ?? '');
$history = (array)($in['history'] ?? []);
if ($msg === '' || mb_strlen($msg) > 4000) { http_response_code(400); exit; }

header('Content-Type: text/event-stream; charset=utf-8');
header('Cache-Control: no-cache, no-transform');
header('X-Accel-Buffering: no');
@ini_set('zlib.output_compression', '0');
while (ob_get_level()) ob_end_flush();
set_time_limit(120);

// Build chat history
$chatHistory = $history;
// DeepAI personality için her mesajda instruction ekle (gizli)
$personalityMsg = 'Senin adın Mert. Kadıköy Avni Akyol Güzel Sanatlar Lisesi\'nde 9/Müzik sınıfındasın. Gen Alpha brainrot çocuğusun. Konuşma tarzın internet memeleri ve absürt videolardan oluşuyor. Müzik bölümündesin; araya brainrot kelimeleri sıkıştırırsın. En sevdiğin şeyler: "Tung tung", "Sahurda ben", "Skibidi Toilet", "Sigma", "Mewing", "Aura". "Tam bir Ohio", "Eksi 10.000 aura" dersin. Hiperaktif, komik, sosyal medya bağımlısı bir 9. sınıf müzik öğrencisi gibi davranırsın. Çok kısa konuş, 2-3 cümle geçmesin. ';

if (empty($chatHistory)) {
    $chatHistory[] = ['role' => 'user', 'content' => $personalityMsg . 'Merhaba, kimsin?'];
    $chatHistory[] = ['role' => 'assistant', 'content' => 'Selam, ben Mert. 9/Müzik\'teyim, tung tung modundayım.'];
}
$chatHistory[] = ['role' => 'user', 'content' => $personalityMsg . $msg];

$postData = [
    'chat_style' => 'chat',
    'chatHistory' => json_encode($chatHistory),
    'model' => 'standard',
    'hacker_is_stinky' => 'very_stinky'
];

$headers = [
    'authority: api.deepai.org',
    'accept: */*',
    'accept-language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
    'api-key: None',
    'origin: https://deepai.org',
    'referer: https://deepai.org/chat',
    'user-agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Mobile Safari/537.36'
];

$ch = curl_init('https://api.deepai.org/hacking_is_a_serious_crime');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_errno($ch);

if ($error || $httpCode !== 200) {
    error_log("DeepAI Error: curl_errno=$error, http_code=$httpCode, response=$response");
    echo "data: {\"e\":1}\n\n";
    curl_close($ch);
    exit;
}

curl_close($ch);

// Parse response
$cleanResponse = stripslashes($response);
$cleanResponse = str_replace('\\n', "\n", $cleanResponse);
$cleanResponse = str_replace('\\"', '"', $cleanResponse);

// Stream response character by character
for ($i = 0; $i < mb_strlen($cleanResponse); $i++) {
    $char = mb_substr($cleanResponse, $i, 1);
    echo 'data: ' . json_encode(['t' => $char], JSON_UNESCAPED_UNICODE) . "\n\n";
    flush();
    usleep(10000); // 10ms delay for streaming effect
}