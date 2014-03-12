<?

	require_once($kapenta->installPath . 'modules/comments/models/comment.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//-------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function comments__cb_object_updated($args) {
		global $kapenta;
		global $kapenta;
		global $kapenta;
		global $kapenta;


	$kapenta->logLive('in comments callback: ' . $args['module']);

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	//---------------------------------------------------------------------------------------------
	//	pull triggers
	//---------------------------------------------------------------------------------------------
	if ('comments' == $args['module']) {
		$kapenta->logLive('in comments callback, setting triggers ');
		// $kapenta->page->doTrigger('comments', 'comment-any');

		if ((true == array_key_exists('refModel', $args['data']))
			&& (true == array_key_exists('refUID', $args['data']))) {
			$channel = 'comment-' . $args['data']['refModel'] . '-' . $args['data']['refUID'];
			$kapenta->logLive('setting trigger on channel: ' . $channel );
			// $kapenta->page->doTrigger('comments', $channel);
		}

		if (true == array_key_exists('createdBy', $args['data'])) {
			// $kapenta->page->doTrigger('comments', 'comment-by-' . $args['data']['createdBy']);
		}
	}

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
