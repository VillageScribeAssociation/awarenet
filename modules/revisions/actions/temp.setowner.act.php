<?

//--------------------------------------------------------------------------------------------------
//*	testing/temporary action to set owner of deleted obejcts
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	administrators only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]', '');
	echo "<h1>Setting ownership of deleted objects.</h1>";

	//----------------------------------------------------------------------------------------------
	//	iterate through all deleted items
	//----------------------------------------------------------------------------------------------
	$sql = "select * from revisions_deleted";
	$result = $kapenta->db->query($sql);

	while ($row = $kapenta->db->fetchAssoc($result)) {
		$item = $kapenta->db->rmArray($row);
		echo "Object: " . $item['UID'] . "<br/>\n";

		$model = new Revisions_Deleted();
		$model->loadArray($item);

		if (true == $model->hasProperty('refUID')) {
			if ($model->owner != $model->fields['refUID']) {
				$model->owner = $model->fields['refUID'];
				$report = $model->save();
				if ('' == $report) {
					echo "Owner set to " . $model->owner . "<br/>\n";
				} else {
					echo "Could not set owner of delete dobject: " . $model->UID . "<br/>";
				}
			} else {
				echo "Owner index field already macthes stored value.<br/>\n";
			}
		} else {
			echo "Does not have refUID property.<br/>\n";
			foreach($model->fields as $key => $value) {
				echo "$key := $value<br/>\n";
			}
		}

	}

	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]', '');

?>
