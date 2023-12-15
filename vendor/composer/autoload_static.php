<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6fee639c98e8301013a08db4dbdaa526
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Fpaipl\\Prody\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Fpaipl\\Prody\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit6fee639c98e8301013a08db4dbdaa526::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6fee639c98e8301013a08db4dbdaa526::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6fee639c98e8301013a08db4dbdaa526::$classMap;

        }, null, ClassLoader::class);
    }
}
