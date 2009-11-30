<?

//-------------------------------------------------------------------------------------------------
//	creates a shell script for uploading to subversion
//-------------------------------------------------------------------------------------------------


	$mods = listModules();
	foreach($mods as $mod) {
		if ($mod != 'uploader') {

			echo "svn import awarenet2/modules/" . $mod . " \
 https://awarenet.svn.sourceforge.net/svnroot/awarenet/trunk/modules/" . $mod . "/ \
 -m  \"Uploading re-organised awarenet sources\"

";

		}
	}

?>
