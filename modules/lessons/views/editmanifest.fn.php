<?php

	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a manifest inline
//--------------------------------------------------------------------------------------------------
//arg: manifestUID - UID of an installed course package [string]

function lessons_editmanifest($args) {
	global $kapenta;
	global $user;
	global $theme;

	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('manifestUID', $args)) { return false; }

	$model = new Lessons_Course($args['manifestUID']);
	if (false == $model->loaded) { return false; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$block = $theme->loadBlock('modules/lessons/views/editmanifestform.block.php');
	$html = $theme->replaceLabels($model->toArray(), $block);

	//----------------------------------------------------------------------------------------------
	//	add forms for editing documents
	//----------------------------------------------------------------------------------------------

	foreach($model->documents as $document) {
		$fields = '';

		$makeImgUrl = 'lessons/makecover/manifest_' . $model->UID . '/document_' . $document['uid'] . '/';
		$makeImgLink = "<a href='%%serverPath%%" . $makeImgUrl . "'>[generate images]</a>";

		if (false == $kapenta->fs->exists($document['file'])) {
			$makeImgLink = '(x)';
		}

		foreach($document as $pname => $pvalue) {
			$fieldName = $document['uid'] . 'XX' . $pname;
			$fieldType = 'txt';

			if ('description' == $pname) { $fieldType = 'ta'; }

			switch($fieldType) {
				case 'txt':
					$fields .= ''
					 . "\t<tr>\n"
					 . "\t\t<td><b>$pname</b></td>\n"
					 . "\t\t<td><input type='text' name='$fieldName' value='$pvalue' style='width: 100%;' /></td>\n"
					 . "\t</tr>\n";
					break;		//..................................................................

				case 'ta':
					$fields .= ''
					 . "\t<tr>\n"
					 . "\t\t<td><b>$pname</b></td>\n"
					 . "\t\t<td><textarea name='$fieldName' style='width: 100%;' rows='3'>$pvalue</textarea></td>\n"
					 . "\t</tr>\n";
					break;		//..................................................................

			}
		}

		$html .= ''
		 . "<div class='block'>\n"
		 . "[[:theme::navtitlebox::label=Document:]]\n"
		 . "<form name='editDoc' method='POST' action='%%serverPath%%lessons/savedocument/'>\n"
		 . "<input type='hidden' name='manifestUID' value='" . $model->UID . "' />\n"
		 . "<input type='hidden' name='documentUID' value='" . $document['uid'] . "' />\n"
		 . "<table noborder style='width: 100%;'>\n"
		 . $fields
		 . "</table>\n"
		 . "<input type='submit' value='Save' /> $makeImgLink\n"
		 . "</form>\n"
		 . "<img src='%%serverPath%%" . $document['cover'] . "' />"
		 . "<img src='%%serverPath%%" . $document['thumb'] . "' />"
		 . "</div>\n"
		 . "<div class='spacer'></div>\n";
	}

	$html .= $theme->loadBlock('modules/lessons/views/adddocumentform.block.php');

	return $html;
}

?>
