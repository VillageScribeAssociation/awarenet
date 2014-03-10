<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of files associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of item which owns these files [string]

function files_filesetdetail($args) {
		global $db;
		global $theme;
		global $kapenta;
		global $user;

	$html = '';						//%	return value [string:html]
	
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return '(refModule not given)'; }
	if (array_key_exists('refModel', $args) == false) { return '(refModel not given)'; }
	if (array_key_exists('refUID', $args) == false) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];
	
	if (false == $kapenta->moduleExists($refModule)) { return '(no such ref module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no such ref object)'; }

	if (false == $user->authHas($refModule, $refModel, 'files-add', $refUID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the list of files
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('files_file', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make table of files
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/files/listing.block.php');

	foreach($range as $item) {
		$model = new Files_File();
		$model->loadArray($item);
		
		$labels = $model->extArray();
		
		$labels['editUrl'] = '%%serverPath%%files/viewset/return_uploadmultiple/' . $model->alias;
		if (true == $user->authHas($refModule, $refModel, 'files-add', $refUID)) {
			$labels['editUrl'] = '%%serverPath%%files/edit/return_uploadmultiple/' . $model->alias;
		}

		$returnUrl = '%%serverPath%%files/uploadmultiple'
			. '/refModule_' . $model->refModule
			. '/refModuel_' . $model->refModel  
			. '/refUID_' . $model->refUID . '/';
		
		$labels['deleteForm'] = "
		<form name='deletefile' method='POST' action='%%serverPath%%files/delete/' >
		<input type='hidden' name='action' value='deletefile' />
		<input type='hidden' name='UID' value='" . $model->UID . "' />
		<input type='hidden' name='return' value='" . $returnUrl . "' />
		<input type='submit' value='delete' />
		</form>
		";

		$labels['thumbUrl'] = '%%serverPath%%themes/%%defaultTheme%%/images/icons/arrow_down.png';

		$html .= $theme->replaceLabels($labels, $block);
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
