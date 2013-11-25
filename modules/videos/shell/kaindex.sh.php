<?

	include "../../../shinit.php";

//--------------------------------------------------------------------------------------------------
//*	get a list of all videos on the Kahn Academy site
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	make list of all video pages
	//----------------------------------------------------------------------------------------------

	$sections = array();
	$vidPages = array();
	$currSection = '';
	$currSectionDesc = '';

	$listFile = 'list.html';
	$tempDir = $kapenta->installPath . 'modules/videos/shell/temp/';

	$raw = implode(file($listFile));
	//$raw = str_replace("<", "\n<", $raw);
	//$raw = str_replace(">", ">\n", $raw);
	$lines = explode("\n", $raw);

	foreach($lines as $line) {
		$line = trim($line);

		//------------------------------------------------------------------------------------------
		//	get section name
		//------------------------------------------------------------------------------------------
		if ("<A NAME=" == substr($line, 0, 8)) {
			$line = str_replace("\"", '', $line);
			$line = str_replace("<A NAME=", '', $line);
			$line = str_replace("</A>", '', $line);
			$line = str_replace(">", '', $line);
			$currSection = $line;
			echo "section: $currSection \n";
		}

		//------------------------------------------------------------------------------------------
		//	get playlist description
		//------------------------------------------------------------------------------------------
		if ("<p class='playlist-desc'>" == substr($line, 0, 25)) {
			$line = str_replace("", '', $line);
			$line = str_replace("<p class='playlist-desc'>", '', $line);
			$line = str_replace("</p>", '', $line);
			$currSectionDesc = $line;
			echo "desc: $currSectionDesc \n";
		}

		//------------------------------------------------------------------------------------------
		//	get playlist description
		//------------------------------------------------------------------------------------------
		if ("<A href=\"/video/" == substr($line, 0, 16)) {
			$startPos = strpos($line, 'href=') + 6;
			$endPos = strpos($line, "\"", $startPos + 1);
			$url = substr($line, $startPos, $endPos - $startPos);
			
			$startPos = strpos($line, "title=") + 7;
			$endPos = strpos($line, "\"", $startPos + 1);
			$title = substr($line, $startPos, $endPos - $startPos);

			$filename = str_replace('/video/', '', $url);
			$filename = str_replace('?', '-', $filename);
			$filename = str_replace('=', '-', $filename);
			$filename = str_replace('%201', '-', $filename);
			$filename = str_replace('%20', '-', $filename);

			echo "video url: $url \n";
			echo "video title: $title \n";

			if (false == array_key_exists($currSection, $sections)) {
				$sections[$currSection] = array(
					'name' => $currSection,
					'desc' => $currSectionDesc,
					'videos' => array()
				);
			}

			$video = array(
				'title' => $title,
				'url' => 'http://www.khanacademy.org' . $url,
				'filename' => $filename
			);

			$sections[$currSection]['videos'][] = $video;
			$vidPages[] = array(
				'filename' => $filename,
				'url' => 'http://www.khanacademy.org' . $url
			);

		}
	}
	
	print_r($sections);

	//----------------------------------------------------------------------------------------------
	//	download video pages in random order
	//----------------------------------------------------------------------------------------------
	echo "\n\nDOWLOADING VIDEO PAGES\n\n";

	shuffle($vidPages);
	foreach($vidPages as $idx => $vid) {
		echo "video: " . $vid['url'] . "\n";
		$raw = implode(file($vid['url']));
		echo $raw . "\n\n\n";

		$kapenta->fs->put($tempDir . $vid['filename'], $raw, false, false);

		sleep(4);
	}


?>
