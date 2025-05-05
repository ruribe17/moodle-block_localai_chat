<?php
require_once('../../config.php');
require_once('completion/chat.php');

$block = new block_openai_chat();
$history = json_decode(file_get_contents('php://input'), true);
$response = $block->create_completion($PAGE->context, true);

header('Content-Type: application/json');
echo json_encode($response);
exit;
