--TEST--
setCompressionName and setCompressionIndex methods
--SKIPIF--
<?php
/* $Id$ */
if (!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php
$tmpfile = dirname(__FILE__) . '/__tmp_oo_set_compression.zip';

if (file_exists($tmpfile)) {
	unlink($tmpfile);
}

// generate the ZIP file
$zip = new ZipArchive;
if ($zip->open($tmpfile, ZipArchive::CREATE) !== TRUE) {
	exit('failed');
}

$zip->addFromString('entry1.txt', 'entry #1');
$zip->addFromString('entry2.txt', 'entry #2');
$zip->addFromString('dir/entry3.txt', 'entry #3');
$zip->addFromString('entry4.txt', 'entry #4');
$zip->addFromString('entry5.txt', 'entry #5');
$zip->addFromString('entry6.txt', 'entry #6');
$zip->addFromString('entry7.txt', 'entry #7');

var_dump($zip->setCompressionName('entry2.txt', ZipArchive::CM_DEFAULT));
var_dump($zip->setCompressionName('dir/entry3.txt', ZipArchive::CM_STORE));
var_dump($zip->setCompressionName('entry4.txt', ZipArchive::CM_DEFLATE));

var_dump($zip->setCompressionIndex(4, ZipArchive::CM_STORE));
var_dump($zip->setCompressionIndex(5, ZipArchive::CM_DEFLATE));
var_dump($zip->setCompressionIndex(6, ZipArchive::CM_DEFAULT));

if (!$zip->close()) {
	exit('failed');
}


// check the ZIP file
$zip = zip_open($tmpfile);
if (!is_resource($zip)) {
	exit('failed');
}

while ($e = zip_read($zip)) {
	echo zip_entry_name($e) . ': ' . zip_entry_compressionmethod($e) . "\n";
}
zip_close($zip);
?>
--CLEAN--
<?php
$tmpfile = dirname(__FILE__) . '/__tmp_oo_set_compression.zip';
unlink($tmpfile);
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
entry1.txt: deflated
entry2.txt: deflated
dir/entry3.txt: stored
entry4.txt: deflated
entry5.txt: stored
entry6.txt: deflated
entry7.txt: deflated
