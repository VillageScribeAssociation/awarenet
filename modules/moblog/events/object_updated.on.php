<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function moblog__cb_object_updated($args) {
	global $kapenta;
	global $db; 
	global $user;
	global $page;

	$kapenta->logLive('in moblog callback: ');

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	//---------------------------------------------------------------------------------------------
	//	pull triggers
	//---------------------------------------------------------------------------------------------
	if ('moblog' == $args['module']) {
		$kapenta->logLive('in moblog callback, setting triggers ');
		$page->doTrigger('moblog', 'post-any');
		$page->doTrigger('moblog', 'post-' . $args['UID']);
		if (true == array_key_exists('createdBy', $args['data'])) {
			$page->doTrigger('moblog', 'post-by-' . $args['data']['createdBy']);
		}
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
