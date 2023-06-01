<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9a8023d610a4b282117a395542347f48
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\DomCrawler\\' => 29,
            'Symfony\\Component\\CssSelector\\' => 30,
        ),
        'P' => 
        array (
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
        ),
        'M' => 
        array (
            'Masterminds\\' => 12,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\DomCrawler\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/dom-crawler',
        ),
        'Symfony\\Component\\CssSelector\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/css-selector',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'Masterminds\\' => 
        array (
            0 => __DIR__ . '/..' . '/masterminds/html5/src',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'WP_BP_Helper' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-bp-helper.php',
        'WP_BP_List_Table' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-batch-list-table.php',
        'WP_BP_Singleton' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-bp-singleton.php',
        'WP_Batch' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-batch.php',
        'WP_Batch_Item' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-batch-item.php',
        'WP_Batch_Processing_Ajax_Handler' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-batch-ajax-handler.php',
        'WP_Batch_Processor' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-batch-processor.php',
        'WP_Batch_Processor_Admin' => __DIR__ . '/..' . '/gdarko/wp-batch-processing/includes/class-batch-processor-admin.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9a8023d610a4b282117a395542347f48::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9a8023d610a4b282117a395542347f48::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit9a8023d610a4b282117a395542347f48::$classMap;

        }, null, ClassLoader::class);
    }
}
