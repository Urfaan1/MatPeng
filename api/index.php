<?php

// Vercel serverless entry point for Laravel
// This file routes all requests to Laravel's public/index.php

// Set the working directory to the project root
$projectRoot = dirname(__DIR__);

// Change to project root
chdir($projectRoot);

// Set required environment variables for Laravel
$_ENV['APP_BASE_PATH'] = $projectRoot;

// Bootstrap and run Laravel
require $projectRoot . '/public/index.php';
