<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//|	shows a form for restoring a deleted item to the active shared database
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Revisions_Deleted obejct [string]

function revisions_restoreform($args) {
	global $user;
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { ''; }
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }	

	$model = new Revisions_Deleted($args['UID']);
	if (false == $model->loaded) { return '(deleted item not found)'; }
	if ('delete' != $model->status) { return '(item restored)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/revisions/views/restoreform.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);
	return $html;
}

?>
