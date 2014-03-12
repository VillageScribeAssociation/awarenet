<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	form for moving an image from user gallery to another
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of an Images_Image object [string]
//opt: imageUID - oveerrides raUID if present [string]

function gallery_movetogallery($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and current user (only creator can reshuffle galleries)
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('imageUID', $args)) { $args['raUID'] = $args['imageUID']; }
	if (false == array_key_exists('raUID', $args)) { return '(no raUID)'; }

	$model = new Images_Image($args['raUID']);
	if (false == $model->loaded) { return '(no such image)'; }
	if ($model->createdBy != $kapenta->user->UID) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load list of user galleries from database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "createdBy='" . $kapenta->db->addMarkup($model->createdBy) . "'";
	$conditions[] = "(UID != '" . $kapenta->db->addMarkup($model->refUID) . "')";

	$range = $kapenta->db->loadRange('gallery_gallery', '*', $conditions, 'title');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/gallery/views/movetogallery.block.php');

	$gals = '';
	foreach($range as $row) 
		{ $gals .= "\t<option value='" . $row['UID'] . "'>" . $row['title'] . "</option>\n"; }

	$labels = array('UID' => $model->UID, 'gals' => $gals);	
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
