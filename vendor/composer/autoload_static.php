<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit03269bd251f2f4ce3a410186664ddc17
{
    public static $prefixLengthsPsr4 = array (
        'a' => 
        array (
            'adriangibbons\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'adriangibbons\\' => 
        array (
            0 => __DIR__ . '/..' . '/adriangibbons/php-fit-file-analysis/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit03269bd251f2f4ce3a410186664ddc17::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit03269bd251f2f4ce3a410186664ddc17::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit03269bd251f2f4ce3a410186664ddc17::$classMap;

        }, null, ClassLoader::class);
    }
}