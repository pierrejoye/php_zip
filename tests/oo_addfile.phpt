--TEST--
ziparchive::addFile() function
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
var_dump($zip->lastId);
if (!$zip->addFile($dirname . 'utils.inc', 'test.php')) {
	echo "failed\n";
}
var_dump($zip->lastId);
if (!$zip->addFile($dirname . 'utils.inc', 'mini.txt', 12, 34)) {
	echo "failed\n";
}
var_dump($zip->lastId);
if (!$zip->addFile($dirname . 'utils.inc', 'other.txt', 0, 0, ZipArchive::FL_OPEN_FILE_NOW)) {
	echo "failed\n";
}
var_dump($zip->lastId);
$del = $dirname . '__tmp_oo_addfile.txt';
file_put_contents($del, 'foo');
if (!$zip->addFile($del, 'deleted.txt', 0, 0, ZipArchive::FL_OPEN_FILE_NOW)) {
	echo "failed\n";
}
unlink($del);
var_dump($zip->lastId);
if ($zip->status == ZIPARCHIVE::ER_OK) {
	dump_entries_name($zip);
	$zip->close();
} else {
	echo "failed\n";
}
if (!$zip->open($file)) {
	exit('failed');
}
var_dump(strlen($zip->getFromName('test.php')) == filesize($dirname . 'utils.inc'));
var_dump(strlen($zip->getFromName('mini.txt')) == 34);
var_dump(strlen($zip->getFromName('other.txt')) == filesize($dirname . 'utils.inc'));
var_dump($zip->getFromName('deleted.txt') === "foo");
@unlink($file);
?>
--EXPECTF--
int(-1)
int(4)
int(5)
int(6)
int(7)
0 bar
1 foobar/
2 foobar/baz
3 entry1.txt
4 test.php
5 mini.txt
6 other.txt
7 deleted.txt
bool(true)
bool(true)
bool(true)
bool(true)
