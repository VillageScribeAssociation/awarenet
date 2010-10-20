<?
	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Article object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('wiki', 'Wiki_Article', 'new'))
		{ $page->do403('You are not authorized to create new Articles.'); }


	//----------------------------------------------------------------------------------------------
	//	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Article();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'title':	$model->title = $utils->cleanString($value); break;
			case 'content':	$model->content = $utils->cleanString($value); break;
			case 'nav':	$model->nav = $utils->cleanString($value); break;
			case 'locked':	$model->locked = $utils->cleanString($value); break;
			//case 'namespace':	$model->namespace = $utils->cleanString($value); break;
			case 'alias':	$model->alias = $utils->cleanString($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$session->msg('Created New Article<br/>', 'ok');
		$page->do302('wiki/edit/' . $model->alias);
	} else {
		$session->msg('Could not create new Article:<br/>' . $report, 'bad');
		$page->do302('wiki/');
	}



?>
