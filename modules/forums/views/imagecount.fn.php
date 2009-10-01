<?

	require_once($installPath . 'modules/forums/models/forum.mod.php');
	require_once($installPath . 'modules/forums/models/forumreply.mod.php');
	require_once($installPath . 'modules/forums/models/forumthread.mod.php');

//--------------------------------------------------------------------------------------------------
//	count the number of images in a forums <--- NOT USED YET
//--------------------------------------------------------------------------------------------------
// * $args['forumUID'] = UID of a forum (required)

function forums_imagecount($args) {
	if (array_key_exists('forumUID', $args) == false) { return false; }

	$sql = "select count(UID) as numRecords from images "
		 . "where refModule='forums' and refUID='" . sqlMarkup($args['forumUID']) . "'";

	$result = dbQuery($sql);
	$row = sqlRMArray(dbFetchAssoc($result));
	return $row['numRecords'];
}

//--------------------------------------------------------------------------------------------------

?>