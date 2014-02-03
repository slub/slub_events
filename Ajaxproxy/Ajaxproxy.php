<?PHP

	$year = date('Y');
	$month = date('m');
	$link = urldecode($_GET['link']);
	$type = $_GET['type'];
	$start = $_GET['start'];
	$stop = $_GET['end'];
	$categories = $_GET['categories'];
	$detailPid = $_GET['detailPid'];

	$calfile = '../../../../typo3temp/tx_slubevents/calfile_'.$categories.'_'.$start.'_'.$stop;
	//~ $calfile = '/home/ab/public_html/t61/typo3temp/tx_slubevents/calfile_'.$categories.'_'.$start.'_'.$stop;
	//~ echo $calfile;
	$url = $link.'&categories='. $categories . '&start=' . $start . '&stop=' . $stop .'&detailPid=' . $detailPid;
	//~ echo $url;
	// if file exists and is not too old - take it
	if (file_exists($calfile)) {
		// if not older than an day:
		if ( (time() - filemtime($calfile) < 86400) ) {
			$fp = fopen($calfile, 'r');
			//~ sleep(1);
			fpassthru($fp);
			exit;
		}
	}

	// else make a new query...
	$out = file_get_contents($url);

	if (!empty($out) && $out != 'null') {
		$fp = fopen($calfile, 'w');
		fwrite($fp, $out);
		fclose($fp);
		echo $out;
	}

	return;

?>
