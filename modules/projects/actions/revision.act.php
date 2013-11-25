<?

	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//-------------------------------------------------------------------------------------------------
//*	display a project revision
//-------------------------------------------------------------------------------------------------

	if ('' == $kapenta->request->ref) { $page->do404('Revision not found.'); }
	$model = new Projects_Revision($kapenta->request->ref);
	if (false == $model->loaded) { $page->do404('Revision not found.'); }

	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
	echo $model->content;

?>
