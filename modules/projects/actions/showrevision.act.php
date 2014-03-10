<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show current and adjacent versions of a project document
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$model = new Projects_Revision($kapenta->request->ref);
	if (false == $model->loaded) {$kapenta->page->do404(); }
	if (false == $user->authHas('projects', 'projects_revision', 'show', $model->UID)) { $kapenta->page->do403(); }

	$project = new Projects_Project($model->projectUID);
	if (false == $project->loaded) {$kapenta->page->do404(); };
	if (false == $user->authHas('projects', 'projects_project', 'show', $project->UID)) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------

	$project->title = $model->title;
	$project->content = $model->content;
	//$project->nav = $model->nav;
	//$article->wikicode->source = $model->content;
	//$article->expandWikiCode();

	$extArray = $project->extArray();

	$kapenta->page->load('modules/projects/actions/showrevision.page.php');
	$kapenta->page->blockArgs['currentRevision'] = $model->UID;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['revisionDate'] = $model->createdOn;
	$kapenta->page->blockArgs['projectUID'] = $project->UID;
	$kapenta->page->blockArgs['projectTitle'] = $project->title;
	//foreach($extArray as $key => $value) {  $kapenta->page->blockArgs[$key] = $value; }		// messy
	$kapenta->page->render();

?>
