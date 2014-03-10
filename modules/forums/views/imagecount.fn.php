<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/reply.mod.php');
	require_once($kapenta->installPath . 'modules/forums/models/thread.mod.php');

//--------------------------------------------------------------------------------------------------
//|	count the number of images in a forums <--- NOT USED YET
//--------------------------------------------------------------------------------------------------
//arg: forumUID - UID of a forum [string]
//TODO: move to images module or discard

function forums_imagecount($args) {
	global $kapenta;

	if (false == array_key_exists('forumUID', $args)) { return ''; }

	$sql = "select count(UID) as numRecords from images_image "
		 . "where refModule='forums' and refUID='" . $kapenta->db->addMarkup($args['forumUID']) . "'";

	$result = $kapenta->db->query($sql);
	$row = $kapenta->db->rmArray($kapenta->db->fetchAssoc($result));
	return $row['numRecords'];
}

//--------------------------------------------------------------------------------------------------

?>
