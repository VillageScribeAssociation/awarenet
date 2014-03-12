<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	history summary formatted for nav // TODO: pagination
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Project object [string]
//opt: num - number of recent entries to show, default is 30 (int) [string]
//opt: pageNo - results page to show, from 1, default is 1 (int)  [string]

function projects_history($args) {
		global $kapenta;
		global $kapenta;
		global $theme;

	$pageNo = 1;			//%	default results page to start from (first) [int]
	$num = 30;				//%	default number of items to show per page [int]
	$totalItems = 0;		//%	total number of revisions to this item [int]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }

	$model = new Projects_Project($args['UID']);
	if (false == $model->loaded) { return '(no such article)'; }
	if (false == $kapenta->user->authHas('projects', 'projects_project', 'show', $model->UID)) { return ''; }
	//TODO: more permission options

	if ($num < 1) { $num = 1; }
	if ($pageNo < 1) { $pageNo = 1; }

	//----------------------------------------------------------------------------------------------
	//	count all revisions to this article
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "projectUID='" . $kapenta->db->addMarkup($model->UID) . "'";
	//TODO: add any other conditions here (namespace, etc)

	$totalItems = $kapenta->db->countRange('projects_revision', $conditions);
	$totalPages = ceil($totalItems / $num);
	if ($pageNo > $totalPages) { $pageNo = $totalPages; }
	$start = (($pageNo - 1) * $num);

	//----------------------------------------------------------------------------------------------
	//	load a page of results from the database and make the block
	//----------------------------------------------------------------------------------------------

	$range = $kapenta->db->loadRange('projects_revision', '*', $conditions, 'editedOn DESC', $num, $start);
	$block = $theme->loadBlock('modules/projects/views/revisionsummary.block.php');

	//	$sql = "select * from Projects_Revision "
	//		 . "where refUID='" . sqlMarkup($args['UID']) . "' "
	//		 . "order by editedOn DESC limit $num";

	foreach ($range as $row) {
		$revision = new Projects_Revision();
		$revision->loadArray($row);
		$ext = $revision->extArray();
		//projects module does not use a reason field
		//if (trim($ext['reason']) != '') { $ext['reason'] = "<i>(none given)</i>"; }
		$html .= $theme->replaceLabels($ext, $block);
	}

	//----------------------------------------------------------------------------------------------
	//	add meta and navigation elements
	//----------------------------------------------------------------------------------------------
	$link = '%%serverPath%%projects/history/' . $model->alias . '/';	
	$pagination = "[[:theme::pagination::page=$pageNo::total=$totalPages::link=$link:]]\n";
	$html = $pagination . "<b>Total revisions:</b> $totalItems <br/>\n" . $html . $pagination;

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
