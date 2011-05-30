<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show current and adjacent versions of a project document
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$model = new Projects_Revision($req->ref);
	if (false == $model->loaded) {$page->do404(); }
	if (false == $user->authHas('projects', 'projects_revision', 'show', $model->UID)) { $page->do403(); }

	$project = new Projects_Project($model->projectUID);
	if (false == $project->loaded) {$page->do404(); };
	if (false == $user->authHas('projects', 'projects_project', 'show', $project->UID)) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------

	$project->title = $model->title;
	$project->content = $model->content;
	//$project->nav = $model->nav;
	//$article->wikicode->source = $model->content;
	//$article->expandWikiCode();

	$extArray = $project->extArray();

	$page->load('modules/projects/actions/showrevision.page.php');
	$page->blockArgs['currentRevision'] = $model->UID;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->UID;
	$page->blockArgs['revisionDate'] = $model->createdOn;
	$page->blockArgs['projectUID'] = $project->UID;
	$page->blockArgs['projectTitle'] = $project->title;
	//foreach($extArray as $key => $value) {  $page->blockArgs[$key] = $value; }		// messy
	$page->render();

?>
