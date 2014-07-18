<?php

	$year = date('Y');
	$month = date('m');
	$link = urldecode($_GET['link']);
	$type = isset($_GET['type']) ? $_GET['type'] : 0;
	$start = strtotime($_GET['start']);
	$stop = strtotime($_GET['end']);
	$categories = $_GET['categories'];
	$disciplines = $_GET['disciplines'];
	$detailPid = $_GET['detailPid'];

	$calfile = PATH_site . 'typo3temp/tx_slubevents/calfile_'.md5($disciplines.$categories).'_'.$start.'_'.$stop;

	// if file exists and is not too old - take it
	if (file_exists($calfile)) {
		// if not older than one day:
		if ( (time() - filemtime($calfile) < 86400) ) {
			$fp = fopen($calfile, 'r');
			fpassthru($fp);
			exit;
		}
	}

	// else make a new query...
	$url = $link . '&categories=' . $categories . '&disciplines=' . $disciplines . '&start=' . $start . '&stop=' . $stop .'&detailPid=' . $detailPid;
//~ echo $url;
	$out = file_get_contents($url);

	if (!empty($out)) {
		$fp = fopen($calfile, 'w');
		if ($fp) {
			fwrite($fp, $out);
			fclose($fp);
		}
		echo $out;
	}

	return;
?>
