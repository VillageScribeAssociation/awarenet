<?

//--------------------------------------------------------------------------------------------------
//*	temp action to scan mindsetlearn.co.za
//--------------------------------------------------------------------------------------------------

	$tempPath = 'data/exampapers/tmp/';

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	download all pages of listing (crappy code, quick/dirty)
	//----------------------------------------------------------------------------------------------
	//209
	for ($i = 0; $i < 209; $i++) {
		$outFile = $tempPath . 'msl' . strrev(substr(strrev('000' . $i), 0, 4)) . '.html';
		echo $outFile . "<br/>\n";
		if (false == $kapenta->fileExists($outFile)) { 
			$url = 'http://www.mindset.co.za/learn/exam?page=' . $i;
			echo "downloading: $url <br/>\n"; flush();
			$raw = $utils->curlGet($url);
			//echo "<textare rows='10' cols='90'>" . str_replace('</textarea', '<', $raw) . "</raw>";
			$check = $kapenta->filePutContents($outFile, $raw, false, false);
			if (true == $check) { echo "...done<br/>\n"; flush(); }
		}
	}

?>
