<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/validation_functions.php';

$input = file_get_contents('php://input');
$data  = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['valid' => false, 'error' => 'Virheellinen pyyntö']);
    exit;
}

$type    = $data['type']    ?? '';
$value   = trim((string)($data['value']   ?? ''));
$compare = trim((string)($data['compare'] ?? ''));

if ($value === '') {
    echo json_encode(['valid' => false]);
    exit;
}

switch ($type) {
    case 'username':
        $valid = isUsernameValid($value);
        break;
    case 'email':
        $valid = isEmailValid($value);
        break;
    case 'password':
        $valid = isPasswordValid($value, $compare);
        break;
    default:
        $valid = false;
}

echo json_encode(['valid' => $valid]);
exit;