<?php

//--------------------------------------------------------------------------------------------------
//*	Utility methods to extract thumbnails and covers from documents
//--------------------------------------------------------------------------------------------------


	//----------------------------------------------------------------------------------------------
	//	check whether a document has thumbnail / cover
	//----------------------------------------------------------------------------------------------
	//arg: document - document properties [array:dict]
	//returns: true if cover exists and is correct, false if not [bool]

	function lessons_hasCover($document) {
		global $kapenta;
		if (false == $kapenta->fs->exists($document['cover'])) { return false; }
		if (false == $kapenta->fs->exists($document['cover'])) { return false; }
		//TODO: check validity of cover image
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	check that required tools are installed (which, mplayer and imagemagick)
	//----------------------------------------------------------------------------------------------
	//returns: empty on success, HTML report on failure [string]

	function lessons_checkTooks() {
		$report = '';							//%	return value [string]
		$mp = shell_exec('which mplayer');
		$im = shell_exec('which convert');
		if ('' == $mp) { $report .= "Please install mplayer on server.<br/>\n"; }
		if ('' == $im) { $report .= "Please install imagemagick on server.<br/>\n"; }
		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	create cover images from source document
	//----------------------------------------------------------------------------------------------
	//:	NOTE - return value will contain '<!-- success -->' or '<!-- fail -->' strings
	//arg: courseUID - UID fo a Lessons_Course object [string]
	//arg: document - document properties [array:dict]
	//returns: html report of generation action [bool]

	function lessons_extractImages($courseUID, $document) {

		$report = '';									//%	return value [string]

		echo "<div class='chatmessageblack'>\n<pre>"; print_r($document); echo "</pre></div>\n";

		switch($document['type']) {
			case 'flv':		//	deliberate fallthrough
			case 'mp4':


				//----------------------------------------------------------------------------------
				//	get video details
				//----------------------------------------------------------------------------------

				$detail = lessons_extractVideoDetails($document['file']);

				//--------------------------------------------------------------------------------------
				//	get video thumbnail
				//--------------------------------------------------------------------------------------

				$report .= lessons_extractVideoStill(
					$document['file'],
					$document['cover'],
					$document['thumb'],
					$detail
				);

				break;		//..................................................................

			//	TODO: other types and default

		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	extract video details
	//----------------------------------------------------------------------------------------------
	//arg: fileName - video file relative to $kapenta->installPath [string]
	//returns: HTML report including '<!-- success -->' or '<!-- fail -->' [string]

	function lessons_extractVideoDetails($fileName) {
		global $kapenta;

		$report = '';							//%	return value [string]
		$temp = $fileName . ".txt";				//%	temporary file [string]
		$shellCmd = ''
		 . "mplayer"
		 . " -vo null"
		 . " -ao null"
		 . " -identify"
		 . " -frames 0"
		 . " \"" . $kapenta->installPath . $fileName . "\""
		 . " > \"" . $kapenta->installPath . $temp . "\"";

		if ((false == $kapenta->fs->exists($temp)) || (0 == $kapenta->fs->size($temp))) { 

			$report .= ''
			 . "Extracting video information from " . $fileName . " <br/>\n"
			 . "Output to: $temp <br/>\n"
			 . "cmd: $shellCmd<br/>\n";

			shell_exec($shellCmd); 

		} else {
			$report .= "Metdata already extracted: $temp<br/>\n";
		}

		if ((true == $kapenta->fs->exists($temp)) || (0 !== $kapenta->fs->size($temp))) {
			$report .= $kapenta->fs->get($temp, true) . '<!-- success -->';
			$kapenta->fileDelete($temp, true);
		} else {
			$report .= '<!-- fail -->';
		}

		return $report;
	}

	//----------------------------------------------------------------------------------------------
	//	extract a still from a video
	//----------------------------------------------------------------------------------------------
	//arg: fileName - video file relative to $kapenta->installPath [string]
	//arg: detail - video properties [string]
	//returns: HTML report including '<!-- success ->' or '<!-- fail -->' [string]

	function lessons_extractVideoStill($fileName, $cover, $thumb, $detail) {
		global $kapenta;

		$report = '';												//%	return value [string]

		$length = (int)lessons_getVideoParam($detail, 'ID_LENGTH');
		$report .= "Video length: $length <br/>\n";
		$ss = floor($length / 2);

		$shellCmd = ''
		 . 'mplayer'
		 . ' -ss ' . $ss
		 . ' -nosound'
		 . ' -vo jpeg'
		 . ' -frames 1'
		 . ' "' . $kapenta->installPath . $fileName . '"';

		$report .= "Extracting video information from $fileName <br/>\n";
		$report .= "Output to: $cover <br/>\n";

		if (false == $kapenta->fs->exists($cover)) { 
			$report .= ''
			 . "Extracting video thumbnail from $fileName <br/>\n"
			 . "Output to: $cover <br/>\n";

			shell_exec($shellCmd); 

			$copyCmd = "cp ./00000001.jpg \"" . $kapenta->installPath . $cover . "\"";
			shell_exec($copyCmd);

			$thumbCmd = ''
			 . 'convert'
			 . ' "' . $kapenta->installPath . $cover . '"'
			 . ' -resize 100x100^'
			 . ' -gravity center'
			 . ' -extent 100x100'
			 . ' ' . $thumb;

			shell_exec($thumbCmd);

		} else {
			$report .= "Thumbnail already extracted: $cover<br/>\n";
		}

		return $report;
	}


	//----------------------------------------------------------------------------------------------
	//	get video parameter
	//----------------------------------------------------------------------------------------------
	//arg: $raw - output from player video properties listing [string]
	//arg: $param - the video property we want to examine [string]
	//returns: parameter if found, empty string on failure [string]

	function lessons_getVideoParam($raw, $param) {

		echo "<div class='chatmessageblack'><pre>" . htmlentities($raw) . "</pre></div>\n";

		$lines = explode("\n", $raw);
		foreach($lines as $line) {
			if ($param == substr($line, 0, strlen($param))) {
				echo "<div class='chatmessagegreen'>Found param: $param - $line </div>\n";
				$line = str_replace($param . ':', '', $line);
				$line = str_replace($param . '=', '', $line);
				$line = str_replace($param, '', $line);
				return $line;
			}
		}
		return '';
	}	

?>
