<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita4b2a1d4138c53ae9cb6e9ffb3890b82
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'microfaster\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'microfaster\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita4b2a1d4138c53ae9cb6e9ffb3890b82::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita4b2a1d4138c53ae9cb6e9ffb3890b82::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
