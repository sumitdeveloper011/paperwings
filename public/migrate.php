<?php
/**
 * Laravel Migration Script
 * 
 * This script runs database migrations after deployment.
 * 
 * USAGE:
 * 1. Upload this file to public/ directory
 * 2. Visit: https://yourdomain.com/migrate.php
 * 3. DELETE THIS FILE IMMEDIATELY after running for security
 * 
 * IMPORTANT: This file must be deleted after use to prevent unauthorized access
 */

// Prevent running multiple times
if (file_exists(__DIR__ . '/.migrated')) {
    die('Already migrated. Delete .migrated file in public/ to run again.');
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Laravel Database Migration</title>
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
        .info {
            color: #004085;
            padding: 10px;
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            margin: 10px 0;
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
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            font-size: 12px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📊 Laravel Database Migration</h1>";

try {
    // Load Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "<div class='step'><strong>✓ Laravel loaded successfully</strong></div>";
    
    // Check database connection
    echo "<div class='step'>";
    echo "<div class='command'>Testing database connection...</div>";
    
    try {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();
        echo "<div class='success'>✓ Connected to database: <strong>{$dbName}</strong></div>";
    } catch (Exception $e) {
        echo "<div class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<div class='info'>
            <strong>Fix:</strong><br>
            1. Check database credentials in .env file<br>
            2. Verify database exists in cPanel<br>
            3. Ensure database user has correct permissions
        </div>";
        throw new Exception('Database connection failed');
    }
    echo "</div>";
    
    // Run migrations
    echo "<div class='step'>";
    echo "<div class='command'>Running: php artisan migrate --force</div>";
    
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        
        echo "<div class='success'>✓ Migrations completed successfully</div>";
        
        if (!empty(trim($output))) {
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Migration failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        throw $e;
    }
    
    echo "</div>";
    
    // Check if seeders are needed
    echo "<div class='step'>";
    echo "<div class='info'>
        <strong>Optional: Run Database Seeders</strong><br>
        If you need to seed initial data, run this command via SSH:<br>
        <code style='background:#fff;padding:5px;border-radius:3px;'>php artisan db:seed --force</code>
    </div>";
    echo "</div>";
    
    // List migrated tables
    echo "<div class='step'>";
    echo "<div class='command'>Checking database tables...</div>";
    
    try {
        $tables = DB::select('SHOW TABLES');
        $tableCount = count($tables);
        
        if ($tableCount > 0) {
            echo "<div class='success'>✓ Found {$tableCount} tables in database</div>";
            
            // Show first 10 tables
            echo "<details style='margin-top:10px;'>";
            echo "<summary style='cursor:pointer;color:#3454D1;font-weight:bold;'>View Tables (showing first 10)</summary>";
            echo "<ul style='margin-top:10px;'>";
            
            $displayCount = min(10, $tableCount);
            for ($i = 0; $i < $displayCount; $i++) {
                $tableArray = (array) $tables[$i];
                $tableName = reset($tableArray);
                echo "<li>" . htmlspecialchars($tableName) . "</li>";
            }
            
            if ($tableCount > 10) {
                echo "<li><em>... and " . ($tableCount - 10) . " more tables</em></li>";
            }
            
            echo "</ul>";
            echo "</details>";
        } else {
            echo "<div class='warning'>⚠️ No tables found - Migration may have failed</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Could not list tables: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    echo "</div>";
    
    // Create marker file
    file_put_contents(__DIR__ . '/.migrated', date('Y-m-d H:i:s'));
    
    echo "<h2>🎉 Migration Complete!</h2>";
    
    echo "<div class='warning'>
        ⚠️ CRITICAL: DELETE THIS FILE NOW!<br><br>
        For security reasons, you MUST delete this file immediately:<br>
        <strong>public/migrate.php</strong><br><br>
        Also delete: <strong>public/.migrated</strong> (hidden file)
    </div>";
    
    echo "<div class='step'>
        <strong>Next Steps:</strong><br>
        1. Delete this file (migrate.php) via cPanel File Manager<br>
        2. Delete .migrated file in public/ directory<br>
        3. Run optimization: <a href='/optimize.php' target='_blank'>Run optimize.php</a><br>
        4. Or run via SSH: <code style='background:#f8f9fa;padding:5px;border-radius:3px;'>php artisan config:cache && php artisan route:cache && php artisan view:cache</code>
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
        1. Verify .env database credentials are correct<br>
        2. Ensure database exists in cPanel MySQL Databases<br>
        3. Check database user has all privileges<br>
        4. Verify PHP version is 8.2 or higher<br>
        5. Make sure vendor/ folder is uploaded
    </div>";
}

echo "
    </div>
</body>
</html>";
