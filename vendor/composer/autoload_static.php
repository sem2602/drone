<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit60c75e10297c197bb5adb97b5f26a55a
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'C' => 
        array (
            'Convertio\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Convertio\\' => 
        array (
            0 => __DIR__ . '/..' . '/convertio/convertio-php/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit60c75e10297c197bb5adb97b5f26a55a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit60c75e10297c197bb5adb97b5f26a55a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit60c75e10297c197bb5adb97b5f26a55a::$classMap;

        }, null, ClassLoader::class);
    }
}
