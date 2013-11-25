<?

	include "../../../shinit.php";

//--------------------------------------------------------------------------------------------------
//*	make list of youtube videos to download from Khan Academy pages
//--------------------------------------------------------------------------------------------------

	$tempDir = $kapenta->installPath . 'modules/videos/shell/temp/'; 
	$d = dir($tempDir);

	$videoIDs = '';
	$videoIDFile = 'modules/videos/shell/videoids.txt';

	while (false !== ($entry = $d->read())) {
		echo $entry . " -- ";
		$raw = implode(file($tempDir . $entry));
		$startPos = strpos($raw, "http://www.youtube.com/v/");
		if (false != $startPos) {
			$endPos = strpos($raw, "\"", $startPos);
			$url = substr($raw, $startPos, $endPos - $startPos);

			$ytID = str_replace("http://www.youtube.com/v/", '', $url);
			$endPos = strpos($ytID, "&");
			$ytID = substr($ytID, 0, $endPos);

			echo $url . ' -- ' . $ytID;

			$videoIDs .=  $ytID . "\n";

		}
		echo "\n";
	}

	$d->close();

	echo $videoIDs;
	$kapenta->fs->put($videoIDFile, $videoIDs, false, false);

?>
