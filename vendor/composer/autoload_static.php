<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8ecb5be0a2feaea9a691a7e201f8a26b
{
    public static $files = array (
        'c257f8e6c6fd01559b7437f250cb4044' => __DIR__ . '/..' . '/denman-digital/wp-utils/utils.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit8ecb5be0a2feaea9a691a7e201f8a26b::$classMap;

        }, null, ClassLoader::class);
    }
}