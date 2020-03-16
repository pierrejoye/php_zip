<?php

function usage() {
	echo <<< EOT

usage : zipcmd  operation  archive  [ files_to_add ]

	Supported operations

	h: help, this page
	a: add to existing archive
	c: create a new archvive (truncate if exists)
	t: test an archive
	d: details of an archive content
	x: extract content


EOT;
}

function dump(ZipArchive $zip, bool $details) {
	$encr = [
		ZipArchive::EM_NONE => 'NONE',
		ZipArchive::EM_AES_128 => 'AES_128',
		ZipArchive::EM_AES_192 => 'AES_192',
		ZipArchive::EM_AES_256 => 'AES_256',
	];
	$comp = [
		ZipArchive::CM_STORE => 'STORE',
		ZipArchive::CM_SHRINK => 'SHRINK',
		ZipArchive::CM_REDUCE_1 => 'REDUCE_1',
		ZipArchive::CM_REDUCE_2 => 'REDUCE_2',
		ZipArchive::CM_REDUCE_3 => 'REDUCE_3',
		ZipArchive::CM_REDUCE_4 => 'REDUCE_4',
		ZipArchive::CM_IMPLODE => 'IMPLODE',
		ZipArchive::CM_DEFLATE => 'DEFLATE',
		ZipArchive::CM_DEFLATE64 => 'DEFLATE64',
		ZipArchive::CM_PKWARE_IMPLODE => 'PKWARE_IMPLODE',
		ZipArchive::CM_BZIP2 => 'BZIP2',
		ZipArchive::CM_LZMA => 'LZMA',
		ZipArchive::CM_LZMA2 => 'LZMA2',
		ZipArchive::CM_XZ => 'XZ',
		ZipArchive::CM_TERSE => 'TERSE',
		ZipArchive::CM_LZ77 => 'LZ77',
		ZipArchive::CM_WAVPACK => 'WAVPACK',
		ZipArchive::CM_PPMD => 'PPMD',
	];

	if ($details) {
		printf("Archive: %s\n", $zip->filename);
		printf("Comment: %s\n", $zip->comment);
	}
	for ($i=0 ; $i<$zip->numFiles ; $i++) {
		$s = $zip->statIndex($i);
		if ($details) {
			printf("\t%d:\tName: %s\n", $s['index'], $s['name']);
			printf("\t\tSize: %d/%d\n", $s['size'], $s['comp_size']);
			printf("\t\tCrc : %d\n", $s['crc']);
			printf("\t\tTime: %d = %s\n", $s['mtime'], date('r', $s['mtime']));
			printf("\t\tComp: %s\n", ($comp[$s['comp_method']] ?? '?'));
			printf("\t\tEncr: %s\n", ($encr[$s['encryption_method']] ?? '?'));

			if ($zip->getExternalAttributesIndex($i, $opsys, $attr) && $opsys==ZipArchive::OPSYS_UNIX) {
				printf("\t\tAttr: %o\n", $attr >> 16);
			}
		} else {
			echo $s['name'] . "\n";
		}
	}

}

function add(ZipArchive $zip, string $path) {
	$stat = stat($path);
	if (is_dir($path)) {
		if ($zip->addEmptyDir($path)) {
			$zip->setExternalAttributesIndex($zip->lastId, ZipArchive::OPSYS_UNIX, $stat['mode'] << 16);
			echo "dir  $path\n";
			foreach(glob("$path/*") as $sub) {
				if ($sub[0] == '.') continue;
				add($zip, $sub);
			}
		}
	} else if ($zip->addFile($path)) {
		$zip->setExternalAttributesIndex($zip->lastId, ZipArchive::OPSYS_UNIX, $stat['mode'] << 16);
		echo "file $path\n";
	}
}

function unzip(ZipArchive $zip) {
	for ($idx=0 ; $s = $zip->statIndex($idx) ; $idx++) {
		if ($zip->extractTo('.', $s['name'])) {
			if ($zip->getExternalAttributesIndex($idx, $opsys, $attr) 
				&& $opsys==ZipArchive::OPSYS_UNIX) {
					chmod($s['name'], ($attr >> 16) & 0777);
            }
			echo $s['name'] . "\n";
        }
	}
}

function close(ZipArchive $zip) {
	$zip->registerProgressCallback(0.05, function ($r) {
		printf("Saving: %d%%\r", $r * 100);
	});
    $zip->close();
	printf("Done        \n");
}

$op = ($_SERVER['argv'][1] ?? 'h');
$file = ($_SERVER['argv'][2] ?? false);
$zip = new ZipArchive();
if (!$file) {
	usage();
} else switch($op) {
	case 't':
	case 'd':
		if ($zip->open($file, ZipArchive::RDONLY)) {
			dump($zip, $op =='d');
		    $zip->close();
		}
		break;
	case 'x':
		if ($zip->open($file, ZipArchive::RDONLY)) {
			unzip($zip);
		    $zip->close();
		}
		break;
	case 'a':
	case 'c':
		if ($zip->open($file, ($op == 'c' ? ZipArchive::CREATE | ZipArchive::OVERWRITE: 0))) {
			for ($i=3 ; $i<$_SERVER['argc'] ; $i++) {
				add($zip, $_SERVER['argv'][$i]);
			}
			close($zip);
		}
		break;
	case 'h':
	default:
		usage();
		break;
}

