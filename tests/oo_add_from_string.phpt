--TEST--
ziparchive::addFromString and FL_OVERWRITE
--SKIPIF--
<?php
/* $Id$ */
if(!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php

include __DIR__ . '/utils.inc';
$file = __DIR__ . '/__tmp_oo_addfromstring.zip';

copy(__DIR__ . '/test.zip', $file);

$zip = new ZipArchive();
if (!$zip->open($file)) {
        exit('failed');
}

// New file
var_dump($zip->addFromString('bar', __FILE__));
var_dump($zip->status == ZipArchive::ER_OK);
// Fails to add existing file
var_dump($zip->addFromString('entry1.txt', __FILE__, 0));
var_dump($zip->status == ZipArchive::ER_EXISTS);
// Overwrite
var_dump($zip->addFromString('entry1.txt', __FILE__, ZipArchive::FL_OVERWRITE));
var_dump($zip->status == ZipArchive::ER_OK);

if ($zip->status == ZipArchive::ER_OK) {
	dump_entries_name($zip);
	$zip->close();
} else {
	echo "failed\n";
}
@unlink($file);
?>
Done
--EXPECTF--
bool(true)
bool(true)
bool(false)
bool(true)
bool(true)
bool(true)
0 bar
1 foobar/
2 foobar/baz
3 entry1.txt
Done
