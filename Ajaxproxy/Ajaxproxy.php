<?PHP

	$year = date('Y');
	$month = date('m');
	$link = urldecode($_GET['link']);
	$type = isset($_GET['type']) ? $_GET['type'] : 0;
	$start = $_GET['start'];
	$stop = $_GET['end'];
	$categories = $_GET['categories'];
	$detailPid = $_GET['detailPid'];

	$calfile = '../../../../typo3temp/tx_slubevents/calfile_'.$categories.'_'.$start.'_'.$stop;
	//~ echo $calfile;
	$url = $link.'&categories='. $categories . '&start=' . $start . '&stop=' . $stop .'&detailPid=' . $detailPid;
	//~ echo $url;
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
