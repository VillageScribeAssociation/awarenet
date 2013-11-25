<?php

//--------------------------------------------------------------------------------------------------
//*	called when an object is saved to the database
//--------------------------------------------------------------------------------------------------
//arg: module - module which owned the deleted object [string]
//arg: model - type of deleted object [string]
//arg: UID - UID of deleted object [string]

function projects__cb_object_updated($args) {
	global $cache;
	global $db;

	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	$data = $args['data'];

	//----------------------------------------------------------------------------------------------
	//	clear cached summary and display when a projects_project object is saved
	//----------------------------------------------------------------------------------------------

	if ('projects_project' == $args['model']) {
		$cache->clear('projects-summary-' . $args['UID']);
		$cache->clear('projects-summarynav-' . $args['UID']);
		$cache->clear('projects-show-' . $args['UID']);
		$cache->clear('projects-all');
		//	^	add more channels here
	}

	//----------------------------------------------------------------------------------------------
	//	clear cached summary and member lists when a projects_membership object is updated
	//----------------------------------------------------------------------------------------------

	if ('projects_membership' == $args['model']) {
		if (false == array_key_exists('projectUID', $data)) { return false; }
		$cache->clear('projects-summary-' . $data['projectUID']);
		$cache->clear('projects-summarynav-' . $data['projectUID']);
		$cache->clear('projects-membersnav-' . $data['projectUID']);

		//------------------------------------------------------------------------------------------
		//	relationships between projects beased on user membership need to change, clear cache
		//------------------------------------------------------------------------------------------
		$conditions = array("userUID='" . $db->addMarkup($data['userUID']) . "'");
		$range = $db->loadRange('projects_membership', '*', $conditions);

		foreach($range as $item) {
			$cache->clear('projects-samemembersnav-' . $item['projectUID']);
		}

	}

	//----------------------------------------------------------------------------------------------
	//	clear affected memebrship lists when a use updates their info
	//----------------------------------------------------------------------------------------------

	if ('users_user' == $args['model']) {
		$conditions = "userUID='" . $db->addMarkup($data['UID']) . "'";
		$range = $db->loadRange('projects_membership', '*', $conditions);

		foreach($range as $item) {
			$cache->clear('projects-summary-' . $item['projectUID']);
			$cache->clear('projects-membersnav-' . $item['projectUID']);
		}

	}

	return true;
}

?>
