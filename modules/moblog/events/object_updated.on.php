<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//|	fired when an object is saved to database
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function moblog__cb_object_updated($args) {
	global $kapenta;
	global $notifications;
	global $session;
	global $cache;

	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	//----------------------------------------------------------------------------------------------
	//	check that this applies to a blog post
	//----------------------------------------------------------------------------------------------

    if ('moblog_post' !== $args['model']) { return false; }

	//----------------------------------------------------------------------------------------------
	//	defer this to moblog_notify event in background process
	//----------------------------------------------------------------------------------------------

    //  these fields are not needed and cannot be serialized
    $args['data'] = '';
    $args['changes'] = '';
    $args['dbSchema'] = '';
    
    $cbSetup = array(
        'target' => 'moblog',
        'event' => 'moblog_notify',
        'args64' => $kapenta->db->serialize($args)
    );

    $kapenta->raiseEvent('p2p', 'p2p_setcallback', $cbSetup);

	return true;
}

//-------------------------------------------------------------------------------------------------
?>
