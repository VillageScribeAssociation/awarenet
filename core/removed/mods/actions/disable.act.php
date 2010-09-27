<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');		

//--------------------------------------------------------------------------------------------------
//*	disable a module
//--------------------------------------------------------------------------------------------------
//role: admin - only administrators may do this
//post: action - must be 'disableModule' (no quotes) [string]
//post: modulename - name of a kapenta module [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if (false == array_key_exists('action', $_POST)) { $page->do404('no action specified'); }
	if ('disableModule' != $_POST['action']) { $page->do404('action not supported'); }

	//----------------------------------------------------------------------------------------------
	//	disable the module
	//----------------------------------------------------------------------------------------------
	$model = new KModule($_POST['modulename']);
	$model->enabled = 'no';
	$model->save();
	$session->msg("Module " . $_POST['modulename'] . " disabled.", 'ok');

	//----------------------------------------------------------------------------------------------
	//	return to module console
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/module/' . $model->name);

?>
