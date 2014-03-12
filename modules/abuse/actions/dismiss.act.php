<?

	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	dismiss an abuse report
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404(); }

	$model = new Abuse_Report($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	delete the report and redirect back to the abuse list
	//----------------------------------------------------------------------------------------------
	$model->delete();
	$kapenta->page->do302('abuse/list/');

?>
