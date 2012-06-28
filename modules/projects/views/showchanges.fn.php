<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/change.mod.php');

//--------------------------------------------------------------------------------------------------
//|	displays list of project changes, one page at a time
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Projects_Project object [string]
//opt: projectUID - overrides raUID if present [string]
//opt: UID - overrides raUID if present [string]
//opt: pageNo - results page to show (int) [string]
//opt: num - number of changes to show per page (int) [string]

function projects_showchanges($args) {
	global $db;
	global $user;
	global $theme;
	global $session;

	$pageNo = 1;				//%	results page to show [int]
	$num = 20;					//%	number of results to show per page [int]
	$start = 0;					//%	offset in database [int]
	$html = "";					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['raUID'] = $args['projectUID']; }
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (false == array_key_exists('raUID', $args)) { return '(raUID not given)'; }

	$model = new Projects_Project($args['raUID']);
	if (false == $model->loaded) { return '(Project not found)'; }

	//TODO: permissions check here

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (true == array_key_exists('pageNo', $args)) { 
		$pageNo = (int)$args['pageNo'];
		$start = (($pageNo - 1) * $num);
	}

	//----------------------------------------------------------------------------------------------
	//	count changes
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "projectUID='" . $db->addMarkup($model->UID) . "'";

	$totalItems = $db->countRange('projects_change', $conditions);

	//----------------------------------------------------------------------------------------------
	//	load a page of results
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('projects_change', '*', $conditions, 'createdOn DESC', $num, $start);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/projects/views/showchange.block.php');

	foreach($range as $item) {
		$labels = $item;
		$labels['unl'] = "[[:users::namelink::userUID=" . $item['createdBy'] . ":]]";
		$labels['head'] = $labels['unl'] . ' ' . $item['message'];
		$labels['content'] = '<br/><br/><br/>';
		$labels['undo'] = '';

		$icon = "%%serverPath%%themes/%%defaultTheme%%/images/icons/undo.png";

		$undoUrl = '%%serverPath%%projects/revertto/' . $item['UID'];	//%	revert action [string]
		$undo = false;													//%	reversible? [bool]

		//------------------------------------------------------------------------------------------
		//	construct the change notice
		//------------------------------------------------------------------------------------------

		switch($item['changed']) {

			case 's.new':
				$undo = false;
				$labels['head'] = $labels['unl'] . " created new section";
				$labels['content'] = "<h3>" . $item['value'] . "</h3>";
				break;			

			case 's.del':
				$labels['head'] = $labels['unl'] . " deleted section";
				$labels['content'] = "<h3>" . $item['value'] . "</h3>";
				break;			

			case 's.title':
				$undo = true;
				$labels['head'] = ''
				 . $labels['unl'] . " changed title of section"
				 . " <small><tt>" . $item['sectionUID'] . "</tt></small> to";
				$labels['content'] = "<h3>" . $item['value'] . "</h3>";
				break;

			case 's.content':
				$undo = true;
				$labels['head'] = ''
				 . $labels['unl']  . " changed content of section"
				 . " <small><tt>" . $item['sectionUID'] . "</tt></small> to<br/><br/>";
				 $labels['content'] = $item['value'];
				break;

			case 's.weight':
				$undo = false;
				$labels['head'] = ''
				 . $labels['unl'] . " changed weighting of sections"
				 . " " . $item['sectionUID'] . " to " . $item['value'];
				break;

			case 'p.title':
				$undo = true;
				$labels['head'] = $labels['unl'] . " changed project title to:";
				$labels['content'] = "<h2>" . $item['value'] . "</h2>";
				break;

			case 'p.abstract':
				$undo = true;
				$labels['head'] = ''
				 . $labels['unl']  . " changed the project abstract to:<br/><br/>";
				 $labels['content'] = $item['value'];
				break;

			default:
				if ('' != trim($item['value'])) {
					$labels['content'] = "<blockquote>" . $item['value'] . "</blockquote>";				
				}
				break;
		}

		//------------------------------------------------------------------------------------------
		//	add undo button
		//------------------------------------------------------------------------------------------

		if (true == $undo) {
			$labels['undo'] = ''
 			 . "<a href='$undoUrl' title='Revert to this.'>"
			 . "<img src='$icon' width='24px' border='0'/></a>";
		}

		if ('open' != $model->status) { $labels['undo'] = ''; }	// locked or closed, can't revert

		//------------------------------------------------------------------------------------------
		//	fix any images, etc which may be too wide for the column
		//------------------------------------------------------------------------------------------

		$replacements = array(
			'images/widtheditor/' => 'images/s_width300/',
			'images/width570/' => 'images/s_width300/',
			'images/width560/' => 'images/s_width300/'
		);
	
		foreach($replacements as $from => $to) {
			$labels['content'] = str_replace($from, $to, $labels['content']);
		}

		//------------------------------------------------------------------------------------------
		//	add it
		//------------------------------------------------------------------------------------------
		$html .= $theme->replaceLabels($labels, $block);
	}

	if (0 == $totalItems) { $html .= "<div class='inlinequote'>No changes recorded.</div>"; }
	if (($num + $start) >= $totalItems) { $html .= "<!-- end of results -->"; }

	if (true == $session->get('mobile')) { $html = $theme->expandBlocks($html, 'mobile'); }
	else { $html = $theme->expandBlocks($html, 'indent'); }

	return $html;
}

?>
