<?

	require_once($kapenta->installPath . 'modules/revisions/models/deleted.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show the details of a specific deleted object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Revisions_Deleted object [string]

function revisions_showdeleted($args) {
	global $user, $theme;
	$html = '';		//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check users role and args
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('UID', $args)) { return ''; }
	$model = new Revisions_Deleted($args['UID']);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/revisions/views/showdeleted.block.php');
	$ext = $model->extArray();

	$table = array();
	$table[] = array('Member', 'Value');
	foreach($model->fields as $member => $value) { $table[] = array($member, $value); }
	$ext['fieldsTable'] = $theme->arrayToHtmlTable($table, true, true);

	$restoreForm = "
	<form name='restroeDeletedItem'>
	<input type='hidden' name='action' value='restoreDeleted' />
	<input type='hidden' name='UID' value='" . $model->UID . "' />
	<input type='submit' value='Restore This Item &gt;&gt;' />
	</form>
	";

	$ext['restoreForm'] = '';
	if ('delete' == $model->status) { $ext['restoreForm'] = $restoreForm; }

	$html = $theme->replaceLabels($ext, $block);
	
	return $html;
}

?>
