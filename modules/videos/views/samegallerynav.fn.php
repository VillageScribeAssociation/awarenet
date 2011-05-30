<?

	require_once($kapenta->installPath . 'modules/videos/models/video.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show videos from the same gallery as this one
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Videos_Video object [string]
//opt: videoUID - replaces raUID if present [string]

function videos_samegallerynav($args) {
	global $db, $user, $theme;
	$html = '';		//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('videoUID', $args)) { $args['raUID'] = $args['videoUID']; }
	if (false == array_key_exists('raUID', $args)) { return ''; }

	$model = new Videos_Video($args['raUID']);
	if (false == $model->loaded) { return ''; }
	//TODO: check permissions here

	//----------------------------------------------------------------------------------------------
	//	load siblings from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModel='" . $db->addMarkup($model->refModel) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($model->refUID) . "'";

	$range = $db->loadRange('videos_video', '*', $conditions, 'weight ASC');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { 
		// NVM
	} else {
		$html .= "[[:theme::navtitlebox::label=Also In This Gallery::toggle=divSameGallery:]]\n"
			  . "<div id='divSameGallery'>\n";
	
		foreach($range as $row) 
			{ $html .= "[[:videos::videosummarynav::raUID=" . $row['UID'] . ":]]\n"; }

		$html .= "</div>\n<br/>\n";
	}

	return $html;
}

?>
