<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

http_response_code(200);

echo json_encode([
    'ok' => true,
    'message' => 'Vercel PHP API is running',
    'timestamp' => gmdate('c')
]);
