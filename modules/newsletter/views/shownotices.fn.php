<?php

//--------------------------------------------------------------------------------------------------
//|	list all notices in an edition of the newsletter, arranged by category
//--------------------------------------------------------------------------------------------------
//arg: editionUID - UID of a Newsletter_Edition object [string]

function newsletter_shownotices($args) {
	global $theme;
	global $db;

	$html = '';								//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check edition UID
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('editionUID', $args)) { return '(editionUID not given)'; }

	$model = new Newsletter_Edition($args['editionUID']);
	if (false == $model->loaded) { return '(no such edition)'; }

	//----------------------------------------------------------------------------------------------
	//	load and sort categories
	//----------------------------------------------------------------------------------------------

	$categories = $db->loadRange('newsletter_category', '*', '', 'CAST(weight AS INTEGER)');

	//----------------------------------------------------------------------------------------------
	//	load and sort notices
	//----------------------------------------------------------------------------------------------

	$conditions = array("edition='" . $db->addMarkup($model->UID) . "'");
	$range = $db->loadRange('newsletter_notice', '*', $conditions, 'createdOn');

	//----------------------------------------------------------------------------------------------
	//	compile (assume n is pretty small)
	//----------------------------------------------------------------------------------------------
	$total = 0;

	foreach($categories as $cat) {
		//echo "category: " . $cat['name'] . " (" . $cat['UID'] . ")<br/>\n";
		$first = true;
		foreach($range as $item) {
			//echo "item: " . $item['title'] . " (" . $item['category'] . ")<br/>\n";
			if ($item['category'] == $cat['UID']) {

				//----------------------------------------------------------------------------------
				//	add section header before first item
				//----------------------------------------------------------------------------------
				if (true == $first) {
					$html .= ''
					 . "[[:theme::navtitlebox::label=" . $cat['name'] . ":]]\n"
					 . "<div class='spacer'></div>\n";
					$first = false;
				}

				//----------------------------------------------------------------------------------
				//	show the notice
				//----------------------------------------------------------------------------------

				$html .= ''
				 . "<div id='divNotice" . $item['UID'] . "'>\n"
				 . "[[:newsletter::shownotice::noticeUID=" . $item['UID'] . ":]]\n"
				 . "</div>";

				$total++;
			}
		}
	}

	if (0 == $total) { $html .= "<div class='block'><small>No notices yet added.</small></div>\n"; }

	return $html;
}

?>
