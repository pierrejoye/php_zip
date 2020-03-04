--TEST--
ziparchive::addEmptyDir
--SKIPIF--
<?php
/* $Id$ */
if(!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php

$dirname = dirname(__FILE__) . '/';
include $dirname . 'utils.inc';
$file = $dirname . '__tmp_oo_addfile.zip';

copy($dirname . 'test.zip', $file);

$zip = new ZipArchive;
if (!$zip->open($file)) {
	exit('failed');
}

var_dump($zip->lastId); // -1 (nothing added)
$zip->addEmptyDir('emptydir');
var_dump($zip->lastId); // 4
$zip->addEmptyDir('emptydir');
var_dump($zip->lastId); // -1 (already exists)
$zip->addEmptyDir('emptydir', ZipArchive::FL_OVERWRITE);
var_dump($zip->lastId); // 4
if ($zip->status == ZipArchive::ER_OK) {
	dump_entries_name($zip);
	$zip->close();
} else {
	echo "failed\n";
}
@unlink($file);
?>
--EXPECTF--
int(-1)
int(4)
int(-1)
int(4)
0 bar
1 foobar/
2 foobar/baz
3 entry1.txt
4 emptydir/
