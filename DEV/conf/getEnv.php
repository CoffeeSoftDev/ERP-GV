<?php
$env = parse_ini_file(__DIR__ . '/.env');
echo json_encode([
  'ssdf3ld6l379hf0f' => $env['OPENAI_API_KEY']
]);
