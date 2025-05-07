--TEST--
GH-18438 (Handling of empty data and errors in ZipArchive::addPattern)
--EXTENSIONS--
zip
--SKIPIF--
<?php
if (PHP_ZTS) die("skip not for ZTS because of path prefixing breaking custom wrapper test");
?>
--FILE--
<?php
class CustomStreamWrapper {
    public $context;
    function dir_closedir() {
        return true;
    }
    function dir_opendir(string $path, int $options) {
        return true;
    }
    function dir_readdir() {
        return false;
    }
    function dir_rewinddir() {
        return false;
    }
}

$file = __DIR__ . '/gh18438.zip';
$zip = new ZipArchive;
$zip->open($file, ZIPARCHIVE::CREATE);
var_dump($zip->addPattern('/nomatches/'));
var_dump($zip->addPattern('/invalid'));

stream_wrapper_register('custom', CustomStreamWrapper::class);
var_dump($zip->addPattern('/invalid', 'custom://'));
?>
--CLEAN--
<?php
$file = __DIR__ . '/gh18438.zip';
@unlink($file);
?>
--EXPECTF--
array(0) {
}

Warning: ZipArchive::addPattern(): No ending delimiter '/' found in %s on line %d

Warning: ZipArchive::addPattern(): Invalid expression in %s on line %d
bool(false)
array(0) {
}
