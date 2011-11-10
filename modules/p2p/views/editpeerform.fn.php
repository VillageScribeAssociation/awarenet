<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form for editing a peer record
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a P2P_Peer object [string]

function p2p_editpeerform($args) {
	global $theme;
	global $user;

	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new P2P_Peer($args['UID']);
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/p2p/views/editpeerform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
