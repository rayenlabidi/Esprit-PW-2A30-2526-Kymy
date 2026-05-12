<?php
require_once __DIR__ . '/CaptchaGuard.php';

AuthC::startSession();

$scope = isset($_GET['scope']) ? $_GET['scope'] : 'login';
$challenge = CaptchaGuard::refresh($scope);

header('Content-Type: application/json; charset=UTF-8');
echo json_encode(CaptchaGuard::publicChallenge($challenge));
?>
