<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita8eabe33eadaa1489969192ae4714c79
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Mimey\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Mimey\\' => 
        array (
            0 => __DIR__ . '/..' . '/ralouphie/mimey/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita8eabe33eadaa1489969192ae4714c79::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita8eabe33eadaa1489969192ae4714c79::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita8eabe33eadaa1489969192ae4714c79::$classMap;

        }, null, ClassLoader::class);
    }
}
