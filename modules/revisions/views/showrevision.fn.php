<?php

	require_once($kapenta->installPath . 'modules/revisions/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the contents of a revision
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Revisions_Revision object [string]
//opt: revisionUID - overrides UID if present [string]

function revisions_showrevision($args) {
	global $db;
	global $user;
	global $theme;	

	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	$model = new Revisions_Revision($args['UID']);
	if (false == $model->loaded) { return '(revision not found)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/revisions/views/showrevision.block.php');

	$changes = $model->expandFields($model->content);
	$ext = $model->extArray();

	$table = array(array('Field', 'Value'));
	foreach($changes as $key => $value) {
		$value = str_replace('[[:', '[&#91;:', htmlentities($value));
		$table[] = array($key, $value);
	}
	$ext['changesTable'] = $theme->arrayToHtmlTable($table, true, true);

	$html = $theme->replaceLabels($ext, $block);

	return $html;
}

?>
