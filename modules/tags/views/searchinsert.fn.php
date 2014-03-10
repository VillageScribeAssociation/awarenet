<?php

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');
	require_once($kapenta->installPath . 'modules/tags/models/index.mod.php');

//--------------------------------------------------------------------------------------------------
//| search tags and return insertable items
//--------------------------------------------------------------------------------------------------
//arg: q64 - base64 encoded search string
//arg: hta - name of a HyperTextArea on client page [string]
//arg: display - comma separated list of modules to display from [string]

function tags_searchinsert($args) {
	global $user;
	global $kapenta;
	global $theme;

	$display = 'images_image,videos_video,files_file,gallery_gallery';
	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { return ''; }
	if (false == array_key_exists('q64', $args)) { return '(query not given)'; }
	if (false == array_key_exists('hta', $args)) { return '(hta not given)'; }
	if (true == array_key_exists('display', $args)) { $display = $args['display']; }

	$displaySet = explode(',', $display);

	//----------------------------------------------------------------------------------------------
	//	search for tags
	//----------------------------------------------------------------------------------------------
	$q = base64_decode($args['q64']);
	$q = str_replace(' ', '-', $q);
	$html .= "Searching '" . htmlentities($q) . "'... \n";	
	$q = strtolower($q);

	$conditions = array();
	$conditions[] = "INSTR(namelc, '" . $kapenta->db->addMarkup($q) . "') > 0";
	$conditions[] = "objectCount <> '0'";
	$range = $kapenta->db->loadRange('tags_tag', '*', $conditions, 'namelc', 500);

	if (0 == count($range)) {
		$html .= "no matches.";
	}

	$html .= "<br/>\n";

	foreach($range as $item) {
		$label = str_replace($q, '<b>' . $q . '</b>', $item['namelc']);
		$html .= ''
		 . "<a href=\"javascript:Tags_Set('" . $item['namelc'] . "');\">"
		 . $label . "</a> ";
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	if (count($range) > 0) {
		$html .= ''
		 . "<hr/>\n"
		 . "[[:tags::searchresults"
		 . "::q64=" . $args['q64']
		 . "::display=". $display
		 . "::hta=". $args['hta']
		 . ":]]";
	}

	return $html;
}

?>
