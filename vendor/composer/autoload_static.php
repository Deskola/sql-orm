<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit08d5f41819f7acdbd5eb2e273d5c8cf6
{
    public static $files = array (
        'ecb104f7bff892915dee4d7ca6fb6f24' => __DIR__ . '/../..' . '/common/helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Test\\' => 5,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Test\\' => 
        array (
            0 => __DIR__ . '/../..' . '/tests',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit08d5f41819f7acdbd5eb2e273d5c8cf6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit08d5f41819f7acdbd5eb2e273d5c8cf6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit08d5f41819f7acdbd5eb2e273d5c8cf6::$classMap;

        }, null, ClassLoader::class);
    }
}
