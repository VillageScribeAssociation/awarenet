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
	global $db;

	if (false == array_key_exists('forumUID', $args)) { return ''; }

	$sql = "select count(UID) as numRecords from Images_Image "
		 . "where refModule='forums' and refUID='" . $db->addMarkup($args['forumUID']) . "'";

	$result = $db->query($sql);
	$row = $db->rmArray($db->fetchAssoc($result));
	return $row['numRecords'];
}

//--------------------------------------------------------------------------------------------------

?>
