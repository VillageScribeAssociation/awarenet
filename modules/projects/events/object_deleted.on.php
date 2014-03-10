<?php

//--------------------------------------------------------------------------------------------------
//|	fired when an object is removed from the database
//--------------------------------------------------------------------------------------------------
//arg: module - name of a kapenta module [string]
//arg: model - type of object which was deleted [string]
//arg: UID - UID of object which was deleted [string]
//arg: data - dict of deleted object's properties [string]

function projects__cb_object_deleted($args) {
	global $cache;
	global $kapenta;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('module', $args)) { return false; }
	if (false == array_key_exists('model', $args)) { return false; }
	if (false == array_key_exists('UID', $args)) { return false; }
	if (false == array_key_exists('data', $args)) { return false; }

	$data = $args['data'];

	//----------------------------------------------------------------------------------------------
	//	check if deleted object was owned by a projects_project object
	//----------------------------------------------------------------------------------------------

	if (
		(true == array_key_exists('refUID', $data)) &&
		(true == array_key_exists('refModel', $data)) &&
		('projects_project' == $data['refModel'])
	) {
		//------------------------------------------------------------------------------------------
		//	clear cached views of this gallery
		//------------------------------------------------------------------------------------------
		$cache->clear('projects-summary-' . $data['refUID']);
		$cache->clear('projects-summarynav-' . $data['refUID']);
	}

	if ('projects_membership' == $args['model']) {
		$cache->clear('projects-summary-' . $data['projectUID']);
		$cache->clear('projects-summarynav-' . $data['projectUID']);		
		$cache->clear('projects-membersnav-' . $data['projectUID']);

		//------------------------------------------------------------------------------------------
		//	relationships between projects beased on user membership need to change, clear cache
		//------------------------------------------------------------------------------------------
		$conditions = array("userUID='" . $kapenta->db->addMarkup($data['userUID']) . "'");
		$range = $kapenta->db->loadRange('projects_membership', '*', $conditions);

		foreach($range as $item) {
			$cache->clear('projects-samemembersnav-' . $item['projectUID']);
		}

	}

}

?>
