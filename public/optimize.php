<?php
/**
 * Laravel Optimization Script
 * 
 * This script runs essential Laravel optimization commands after deployment.
 * 
 * USAGE:
 * 1. Upload this file to public/ directory
 * 2. Visit: https://yourdomain.com/optimize.php
 * 3. DELETE THIS FILE IMMEDIATELY after running for security
 * 
 * IMPORTANT: This file must be deleted after use to prevent unauthorized access
 */

// Prevent running multiple times
if (file_exists(__DIR__ . '/.optimized')) {
    die('Already optimized. Delete .optimized file in public/ to run again.');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Optimization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #3454D1;
            padding-bottom: 10px;
        }
        .success {
            color: #28a745;
            padding: 10px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            margin: 10px 0;
        }
        .error {
            color: #dc3545;
            padding: 10px;
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
        }
        .warning {
            color: #856404;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
            font-weight: bold;
        }
        .command {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            margin: 5px 0;
        }
        .step {
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 Laravel Optimization</h1>";

try {
    // Load Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<div class='step'><strong>✓ Laravel loaded successfully</strong></div>";
    
    // Run optimization commands
    $commands = [
        'storage:link' => 'Creating storage symlink',
        'config:cache' => 'Caching configuration',
        'route:cache' => 'Caching routes',
        'view:cache' => 'Caching views',
    ];
    
    foreach ($commands as $command => $description) {
        echo "<div class='step'>";
        echo "<div class='command'>Running: php artisan {$command}</div>";
        
        try {
            Artisan::call($command);
            $output = Artisan::output();
            echo "<div class='success'>✓ {$description} - Success</div>";
            
            if (!empty(trim($output))) {
                echo "<pre style='background:#f8f9fa;padding:10px;border-radius:4px;font-size:12px;'>" . htmlspecialchars($output) . "</pre>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>✗ {$description} - Failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
        
        echo "</div>";
    }
    
    // Check if .env file exists
    echo "<div class='step'>";
    if (file_exists(__DIR__.'/../.env')) {
        echo "<div class='success'>✓ .env file exists</div>";
    } else {
        echo "<div class='error'>✗ .env file NOT found - Please create it!</div>";
    }
    echo "</div>";
    
    // Check storage permissions
    echo "<div class='step'>";
    $storagePath = __DIR__.'/../storage';
    if (is_writable($storagePath)) {
        echo "<div class='success'>✓ Storage directory is writable</div>";
    } else {
        echo "<div class='error'>✗ Storage directory is NOT writable - Set permissions to 755</div>";
    }
    echo "</div>";
    
    // Check bootstrap/cache permissions
    echo "<div class='step'>";
    $cachePath = __DIR__.'/../bootstrap/cache';
    if (is_writable($cachePath)) {
        echo "<div class='success'>✓ Bootstrap cache directory is writable</div>";
    } else {
        echo "<div class='error'>✗ Bootstrap cache directory is NOT writable - Set permissions to 755</div>";
    }
    echo "</div>";
    
    // Create marker file
    file_put_contents(__DIR__ . '/.optimized', date('Y-m-d H:i:s'));
    
    echo "<h2>🎉 Optimization Complete!</h2>";
    
    echo "<div class='warning'>
        ⚠️ CRITICAL: DELETE THIS FILE NOW!<br><br>
        For security reasons, you MUST delete this file immediately:<br>
        <strong>public/optimize.php</strong><br><br>
        Also delete: <strong>public/.optimized</strong> (hidden file)
    </div>";
    
    echo "<div class='step'>
        <strong>Next Steps:</strong><br>
        1. Delete this file (optimize.php) via cPanel File Manager<br>
        2. Delete .optimized file in public/ directory<br>
        3. Test your website: <a href='/' target='_blank'>Visit Homepage</a><br>
        4. Access admin panel: <a href='/admin/login' target='_blank'>Admin Login</a>
    </div>";
    
} catch (Exception $e) {
    echo "<div class='error'>
        <strong>Fatal Error:</strong><br>
        " . htmlspecialchars($e->getMessage()) . "<br><br>
        <strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>
        <strong>Line:</strong> " . $e->getLine() . "
    </div>";
    
    echo "<div class='step'>
        <strong>Common Solutions:</strong><br>
        1. Make sure vendor/ folder is uploaded<br>
        2. Check file permissions (storage/ and bootstrap/cache/ must be 755)<br>
        3. Verify .env file exists in root directory<br>
        4. Check PHP version is 8.2 or higher
    </div>";
}

echo "
    </div>
</body>
</html>";
