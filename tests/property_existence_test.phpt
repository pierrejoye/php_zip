--TEST--
Property existence test can cause a crash
--SKIPIF--
<?php
if (!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php

$archive = new ZipArchive(__DIR__.'/property_existence.zip');
var_dump(array_column([$archive], 'lastId'));

?>
--CLEAN--
<?php
@unlink(__DIR__.'/property_existence.zip');
?>
--EXPECT--
array(1) {
  [0]=>
  int(-1)
}
