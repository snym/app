<?php

namespace App;

use Composer\Script\Event;

define('DS', DIRECTORY_SEPARATOR);

function releaseResource($source, $destination)
{
    // 释放文件到目标位置
    clearstatcache();
    $replace = true;
    if (is_file($destination)) {
        $filename = basename($destination);
        echo "{$filename} has already existed, do you want to replace it? [ Y / N (default) ] : ";
        $answer = strtolower(trim(strtoupper(fgets(STDIN))));
        if (!in_array($answer, ['y', 'yes'])) {
            $replace = false;
        }
    }

    if ($replace) {
        copy($source, $destination);
    }
}

/**
 * Class Run
 * @author  : evalor <master@evalor.cn>
 * @package App
 */
class Run
{
    static function postCreateCmd(Event $event)
    {
        $easyswooleRoot     = __DIR__ . DS . '..' . DS;
        $easyswooleVendor   = $easyswooleRoot . 'vendor' . DS . 'easyswoole' . DS . 'easyswoole' . DS . 'src' . DS;
        $easyswooleResource = $easyswooleVendor . 'Resource' . DS;

        // 因为全新安装 不做检查直接覆盖
        copy($easyswooleResource . 'Config.tpl', $easyswooleRoot . 'Config.php');
        copy($easyswooleResource . 'EasySwooleEvent.tpl', $easyswooleRoot . 'EasySwooleEvent.php');

        // 创建临时目录
        @mkdir($easyswooleRoot . 'Temp', 0755) && chmod($easyswooleRoot . 'Temp', 0755);
        @mkdir($easyswooleRoot . 'Log', 0755) && chmod($easyswooleRoot . 'Log', 0755);

        // 创建安装锁定文件
        file_put_contents($easyswooleRoot . 'easyswoole.install', 'installed at ' . date('Y-m-d H:i:s'));

        // 创建控制台脚本快捷方式
        file_put_contents($easyswooleRoot . 'easyswoole', "<?php\nrequire './vendor/bin/easyswoole';");

        // 删除自身
        unlink($easyswooleRoot . 'App/Run.php');
    }
}