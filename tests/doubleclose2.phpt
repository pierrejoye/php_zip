--TEST--
close() called twice
--SKIPIF--
<?php
if(!extension_loaded('zip')) die('skip');
if (PHP_VERSION_ID < 80000) die('skip PHP 8 only');
?>
--INI--
error_reporting=24575
--FILE--
<?php

echo "Procedural\n";
$zip = zip_open(__DIR__ . '/test.zip');
if (!is_resource($zip)) {
    die("Failure");
}
var_dump(zip_close($zip));
try {
    var_dump(zip_close($zip));
} catch (TypeError $e) {
    echo $e->getMessage(), "\n";
}

echo "Object\n";
$zip = new ZipArchive();
if (!$zip->open(__DIR__ . '/test.zip')) {
    die('Failure');
}
if ($zip->status == ZIPARCHIVE::ER_OK) {
    var_dump($zip->close());
    try {
        $zip->close();
    } catch (ValueError $err) {
        echo $err->getMessage(), PHP_EOL;
    }
} else {
    die("Failure");
}

?>
Done
--EXPECTF--
Procedural
NULL
zip_close(): supplied resource is not a valid Zip Directory resource
Object
bool(true)
Invalid or uninitialized Zip object
Done
