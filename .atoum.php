<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$extension = new \mageekguy\atoum\blackfire\extension($script);
$extension->addToRunner($runner);
