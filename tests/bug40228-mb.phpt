--TEST--
Bug #40228 (extractTo does not create recursive empty path)
--SKIPIF--
<?php if (!extension_loaded("zip")) print "skip"; ?>
--FILE--
<?php
$dest = __DIR__;
$src_name = $dest . "/bug40228.zip";
$arc_name = $dest . "/bug40228私はガラスを食べられます.zip";
copy($src_name, $arc_name);

$zip = new ZipArchive;
$zip->open($arc_name, ZIPARCHIVE::CREATE);
$zip->extractTo($dest);
if (is_dir($dest . '/test/empty')) {
    echo "Ok\n";
    rmdir($dest . '/test/empty');
    rmdir($dest . '/test');
} else {
    echo "Failed.\n";
}

@unlink($arc_name);
echo "Done\n";
?>
--EXPECT--
Ok
Done
