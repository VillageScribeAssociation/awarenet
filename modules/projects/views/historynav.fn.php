<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/change.mod.php');

//--------------------------------------------------------------------------------------------------
//|	history summary formatted for nav // TODO: pagination
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a project [string]
//opt: num - number of recent entries to show, default is 30 (int) [string]
//opt: pageNo - results page to show, from 1, default is 1 (int)  [string]
//opt: label - label of navtitlebox [string]

function projects_historynav($args) {
	global $user, $db, $theme;
	$pageNo = 1;			//%	default results page to start from (first) [int]
	$num = 30;				//%	default number of items to show per page [int]
	$totalItems = 0;		//%	total number of revisions to this item [int]
	$html = '';				//%	return value [string]
	$label = '';			//%	label of navtitlebox [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('UID', $args)) { return '(UID not given)'; }
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { $pageNo = (int)$args['pageNo']; }
	if (true == array_key_exists('label', $args)) { $label = $args['label']; }

	$model = new Projects_Project($args['UID']);
	if (false == $model->loaded) { return '(no such article)'; }
	if (false == $user->authHas('projects', 'projects_project', 'show', $model->UID)) { return ''; }
	//TODO: more permission options

	if ($num < 1) { $num = 1; }
	if ($pageNo < 1) { $pageNo = 1; }

	//----------------------------------------------------------------------------------------------
	//	count all revisions to this article
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "projectUID='" . $db->addMarkup($model->UID) . "'";
	//TODO: add any other conditions here (namespace, etc)

	$totalItems = $db->countRange('projects_revision', $conditions);
	$totalPages = ceil($totalItems / $num);
	if ($pageNo > $totalPages) { $pageNo = $totalPages; }
	$start = (($pageNo - 1) * $num);
	if ($start < 0) { $start = 0; }

	//----------------------------------------------------------------------------------------------
	//	load a page of results from the database and make the block
	//----------------------------------------------------------------------------------------------

	$range = $db->loadRange('projects_change', '*', $conditions, 'editedOn DESC', $num, $start);
	$block = $theme->loadBlock('modules/projects/views/revisionsummarynav.block.php');

	if (0 == count($range)) { return ''; }

	foreach ($range as $row) {
		$model = new Projects_Change();
		$model->loadArray($row);
		$ext = $model->extArray();
		//if ('' == trim($ext['reason'])) { $ext['reason'] = "<i>(no reason given)</i>"; }
		$html .= $theme->replaceLabels($ext, $block);
	}

	//----------------------------------------------------------------------------------------------
	//	add meta and navigation elements
	//----------------------------------------------------------------------------------------------
	//$linkAll = "<a href='%%serverPath%%projects/history/" . $model->projectUID . "'>[see all]</a>";
	//$html .= "<b>Total revisions:</b> $totalItems<br/>\n";

	// TODO: sanitize label
	if ('' != $label) { $html = $theme->ntb($html, $label, 'divHistoryNav', 'show'); }

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
