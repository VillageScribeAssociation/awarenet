<?
	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Post object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('moblog', 'moblog_post', 'new'))
		{ $kapenta->page->do403('You are not authorized to create new Posts.'); }

	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Moblog_Post();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'school':	$model->school = $utils->cleanString($value); break;
			case 'grade':	$model->grade = $utils->cleanString($value); break;
			case 'title':	$model->title = $utils->cleanString($value); break;
			case 'content':	$model->content = $utils->cleanString($value); break;
			case 'published':	$model->published = $utils->cleanString($value); break;
			case 'viewcount':	$model->viewcount = $utils->cleanString($value); break;
			case 'alias':	$model->alias = $utils->cleanString($value); break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created new blog post.<br/>', 'ok');
		$kapenta->page->do302('moblog/edit/' . $model->alias);

	} else {
		$session->msg('Could not create new Post:<br/>' . $report);
		$kapenta->page->do302('moblog/blog/' . $user->alias);
	}


?>
