<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function announcements__cb_object_updated($args) {
	global $kapenta, $db, $user, $page;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	//---------------------------------------------------------------------------------------------
	//	pull triggers
	//---------------------------------------------------------------------------------------------
	if ('announcements' == $args['module']) {
		$page->doTrigger('announcements', 'announcement-any');
		$page->doTrigger('announcements', 'announcement-' . $args['UID']);
		if (true == array_key_exists('refUID', $args['data'])) {
			$kapenta->logLive('firing: ' . 'announcements-refUID-' . $args['data']['refUID']);
			$page->doTrigger('announcements', 'announcements-refUID-' . $args['data']['refUID']);
		}
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
