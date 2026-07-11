<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

require_method('POST');

json_response(['mensagem' => 'Compartilhamento registrado']);
