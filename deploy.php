<?php
define("TOKEN", "fenghui"); // TOKEN
define("DIR", "/app/"); // 文件目录，以"/"结尾
define("BRANCH", "refs/heads/master"); // 分支名
define("LOGFILE", "deploy.log"); // 日志文件

$content = file_get_contents("php://input");
$json = json_decode($content, true);
$file = fopen(LOGFILE, "a");
$time = time();
$token = false;

if (isset($json['token'])) {
    // Coding 的 token 是在 json 中的
    $token = $json['token'];
}

// 记录信息
function logger($file, $msg, $type = 'Info')
{
    if ($type) {
        fputs($file, "[{$type}] {$msg}\n");
    } else {
        fputs($file, "{$msg}\n");
    }
}

// 结束
function over($file)
{
    logger($file, "\n", '');
    fclose($file);
    exit;
}

// 拒绝
function forbid($file)
{
    header("HTTP/1.0 403 Forbidden");
    over($file);
}

// 执行成功
function ok()
{
    ob_start();
    header("HTTP/1.1 200 OK");
    header("Connection: close");
    header("Content-Length: " . ob_get_length());
    ob_end_flush();
    ob_flush();
    flush();
}

// 跳过
function skip($file)
{
    ok();
    over($file);
}

// 记录时间
date_default_timezone_set("PRC");
logger($file, date("Y-m-d H:i:s", $time));

// 检查 token
if (!empty(TOKEN) && $token !== TOKEN) {
    logger($file, 'token 错误: 获取到的 token：' . $token, 'Error');
    forbid($file);
}

// 检查分支
if ($json["ref"] !== BRANCH) {
    logger($file, '分支不匹配：获取到的分支：' . $json["ref"], 'Warning');
    skip($file);
}

// 检查文件和路径
if (!(file_exists(DIR . ".git") && is_dir(DIR))) {
    logger($file, '文件路径错误：' . DIR, 'Error');
    forbid($file);
}

// 执行操作
try {
    // pull
    chdir(DIR);
    $output = shell_exec('git pull');
    logger($file, 'PULL:');
    logger($file, $output);

    // 返回成功，防止之后的脚本超时
    ok();

    // 执行后续脚本
    // 执行 composer
    /*$output = shell_exec('php ' . DIR . 'composer.phar install');
    logger($file, 'composer:');
    logger($file, $output);*/

    // 执行 初始化脚本
    // 注意：此处 overwrite = y 时会覆盖原文件，在 yii 初始化时有设置 cookieValidateKey，重置会导致 cookie 存储的用户信息失效
    /*$output = shell_exec('php ' . DIR . 'init --env=Production --overwrite=y');
    logger($file, 'init:');
    logger($file, $output);*/

    // 执行 migration
    /*$output = shell_exec('php ' . DIR . 'yii migrate --interactive=0');
    logger($file, 'migration:');
    logger($file, $output);*/

} catch (Exception $e) {
    logger($file, $e, 'Error');
}

// 结束
over($file);
