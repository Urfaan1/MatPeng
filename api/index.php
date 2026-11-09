<?php

// Prepare /tmp directory for serverless environment
if (! is_dir('/tmp/views')) {
    mkdir('/tmp/views', 0755, true);
}

putenv('VIEW_COMPILED_PATH=/tmp/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/views';

// Forward requests to Laravel's public entry point
$projectRoot = dirname(__DIR__);
chdir($projectRoot);
$_ENV['APP_BASE_PATH'] = $projectRoot;

require $projectRoot.'/public/index.php';
