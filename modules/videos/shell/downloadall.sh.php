<?

//--------------------------------------------------------------------------------------------------
//*	temporary script to download a bunch of youtube videos
//--------------------------------------------------------------------------------------------------

	$videoIDFile = 'videoids.txt';

	$raw = implode(file($videoIDFile));
	$lines = explode("\n", $raw);

	foreach($lines as $line) {
		$line = trim($line);
		if ('' != $line) {
			echo "downloading: " . $line . "\n";
			$OK = true;

			if (true == file_exists($line . ".flv")) { $OK = false; }
			if (true == file_exists($line . ".mp4")) { $OK = false; }
			if (true == file_exists($line . ".flv.part")) { $OK = false; }
			if (true == file_exists($line . ".mp4.part")) { $OK = false; }

			if (true == $OK) {
				$shellCmd = "./youtube-dl.py \"http://www.youtube.com/watch?v=" . $line . "\"";
				echo $shellCmd . "\n";
				$result = shell_exec($shellCmd);
				echo $result . "\n";

				$fh = fopen($line . '.log', 'w+');
				fwrite($fh, $result);
				fclose($fh);
			} else {
				echo "exists\n";
			}

		}
	}

?>
