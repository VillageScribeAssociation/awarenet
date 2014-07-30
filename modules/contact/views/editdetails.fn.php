<?

//--------------------------------------------------------------------------------------------------
//|	display iframe for editing contact details
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have contact details associated with it [string]
//arg: refUID- UID of object which may have contact details associated with it [string]

function contact_editdetails($args) {
	global $kapenta, $db, $user, $theme;
	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $kapenta->moduleExists($args['refModule'])) { return '(bad refModule)'; }
	if (false == $db->objectExists($args['refModel'], $args['refUID'])) { return '(no owner)'; }

	if (false == $user->authHas($refModule, $refModel, 'contact-add', $refUID)) { 
		return '(no permission to edit contact details)'; 
	}	

	//----------------------------------------------------------------------------------------------
	//	make the iframe
	//----------------------------------------------------------------------------------------------

	$args['ifUrl'] = '%%serverPath%%contact/show'
		 . '/refModule_' . $refModule
		 . '/refModel_' . $refModel
		 . '/refUID_' . $refUID . '/';

	$block = $theme->loadBlock('modules/contact/views/editdetails.block.php');
	$html = $theme->replaceLabels($args, $block);

	return $html;
}

?>
