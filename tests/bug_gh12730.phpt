--TEST--
Bug GH-12730 - extract lost permission on linux
--SKIPIF--
<?php
if (!extension_loaded('zip')) die('skip zip extension not available');
?>
--FILE--
<?php
include __DIR__ . '/utils.inc';


$zip = new ZipArchive();
$zip->open(__DIR__ . '/bug_gh12730.zip');
$zip->extractTo(__DIR__);
$zip->close();

var_dump(fileperms(__DIR__ . '/bug_gh12730/foo') == 0100644); // file
var_dump(fileperms(__DIR__ . '/bug_gh12730/bar') == 0100755); // executable
var_dump(fileperms(__DIR__ . '/bug_gh12730')     == 0040750); // dir
?>
Done
--CLEAN--
<?php
unlink(__DIR__ . '/bug_gh12730/foo');
unlink(__DIR__ . '/bug_gh12730/bar');
rmdir(__DIR__ . '/bug_gh12730');
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
Done
