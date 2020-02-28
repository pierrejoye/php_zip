--TEST--
ZipArchive::addGlob() method
--CREDITS--
Sammy Kaye Powers <sammyk@sammykmedia.com>
w/Kenzo over the shoulder
#phptek Chicago 2014
--SKIPIF--
<?php
/* $Id$ */
if(!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php
$dirname = dirname(__FILE__) . '/';
include $dirname . 'utils.inc';
$file = $dirname . '__tmp_oo_addglob.zip';

copy($dirname . 'test.zip', $file);
touch($dirname . 'foo.txt');
touch($dirname . 'bar.baz');

$zip = new ZipArchive();
if (!$zip->open($file)) {
        exit('failed');
}
$options = array('add_path' => 'baz/', 'remove_all_path' => TRUE);
if (!$zip->addGlob($dirname . '*.{txt,baz}', GLOB_BRACE, $options)) {
        echo "failed 1\n";
}
if (!$zip->addGlob($dirname . '*.{txt,baz}', GLOB_BRACE, $options)) {
        echo "failed 1\n";
}
$options['flags'] = 0; // clean FL_OVERWRITE
if (!$zip->addGlob($dirname . '*.{txt,baz}', GLOB_BRACE, $options)) {
	var_dump($zip->getStatusString());
}
$options['flags'] = ZipArchive::FL_OVERWRITE;
if (!$zip->addGlob($dirname . '*.{txt,baz}', GLOB_BRACE, $options)) {
        echo "failed 2\n";
}
if ($zip->status == ZIPARCHIVE::ER_OK) {
        dump_entries_name($zip);
        $zip->close();
} else {
        echo "failed 3\n";
}
?>
--CLEAN--
<?php
$dirname = dirname(__FILE__) . '/';
unlink($dirname . '__tmp_oo_addglob.zip');
unlink($dirname . 'foo.txt');
unlink($dirname . 'bar.baz');
?>
--EXPECTF--
string(19) "File already exists"
0 bar
1 foobar/
2 foobar/baz
3 entry1.txt
4 baz/foo.txt
5 baz/bar.baz
