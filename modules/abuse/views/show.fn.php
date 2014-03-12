<?
	
	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//!	show a single abuse report in its entirety
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an Abuse_Report object [string]

function abuse_show($args) {
		global $kapenta;
		global $theme;

	$html = '';														//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; } 
	if (false == array_key_exists('UID', $args)) { return ''; }
	$model = new Abuse_Report($args['UID']);
	if (false == $model->loaded) { return ''; }
	//TODO: permissions check here, to allow for moderator role, etc

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/abuse/views/show.block.php');
	$html = $theme->replaceLabels($model->extArray(), $block);

	return $html;
}

?>
