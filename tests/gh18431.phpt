--TEST--
GH-18431 (Registering ZIP progress callback twice doesn't work)
--SKIPIF--
<?php
if (!extension_loaded('zip')) die('skip extension not loaded');
if (!method_exists('ZipArchive', 'registerProgressCallback')) die('skip libzip too old');
?>
--FILE--
<?php
$file = __DIR__ . '/gh18431.zip';
$callback = function($r) { var_dump($r);};
$zip = new ZipArchive;
$zip->open($file, ZIPARCHIVE::CREATE);
$zip->registerProgressCallback(0.5, $callback);
$zip->registerProgressCallback(0.5, $callback);
$zip->addFromString('foo', 'entry #1');
?>
--CLEAN--
<?php
$file = __DIR__ . '/gh18431.zip';
@unlink($file);
?>
--EXPECT--
float(0)
float(1)
