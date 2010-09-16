<?
	
	require_once($installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//!	show a single abuse report in its entirety
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an Abuse_Report object [string]

function abuse_show($args) {
	global $user;
	$html = '';														//%	return value [string]
	if ('admin' != $user->data['ofGroup']) { return ''; } 
	if (false == array_key_exists('UID', $args)) { return ''; }

	$model = new Abuse_Report($args['UID']);
	if (false == $model->loaded) { return ''; }
	$html = replaceLabels($model->extArray(), loadBlock('modules/abuse/views/show.block.php'));
	return $html;
}

?>
