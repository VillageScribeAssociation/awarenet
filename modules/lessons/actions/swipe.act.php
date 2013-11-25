<?php

	require_once($kapenta->installPath . 'modules/lessons/inc/install.inc.php');
	require_once($kapenta->installPath . 'modules/lessons/models/course.mod.php');	

//--------------------------------------------------------------------------------------------------
//*	action to 'swipe' a package from another awarenet instance
//--------------------------------------------------------------------------------------------------
//:	NOTE - this is a quick and dirty script to meet a present need, will probably be refined
//:	NOTE - this is a security risk, hence restricted to admin users

	if ('admin' !== $user->role) { $page->do403(); }

	echo $theme->expandBlocks('[[:theme::ifscrollheader:]]');

	//----------------------------------------------------------------------------------------------
	//*	display form to list packages on another peer
	//----------------------------------------------------------------------------------------------

	$defaultUrl = 'http://awarenet.eu/';
	if (true == array_key_exists('url', $_POST)) { $defaultUrl = $_POST['url']; }

	echo ''
	 . "<div class='chatmessageblack'>\n"
	 . "<h2>Swipe a packge from another peer</h2>\n"
	 . "<form name='frmSwipe' method='POST'>"
	 . "<input type='hidden' name='action' value='list' />"
	 . "<b>peer:</b> <input type='text' name='url' value='$defaultUrl' />\n"
	 . "<b>category:</b>\n"
	 . "<select name='category'/>\n"
	 . "  <option value='textbooks'>Textbooks</option>"
	 . "  <option value='videolessons'>Video Lessons (english)</option>"
	 . "  <option value='videosxh'>Video Lessons (isiXhosa)</option>"
	 . "</select/>\n"
     . "<input type='submit' value='See what they have' />"
	 . "</form>\n"
	 . "</div>\n";

	//----------------------------------------------------------------------------------------------
	//*	list content packages installed on another peer
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('list' == $_POST['action'])) {
		$peer = $_POST['url'];
		$category = $_POST['category'];

		$url = $peer . 'data/lessons/' . $category . '.dat.php';
		$raw = implode(file($url));
		$data = unserialize($raw);

		foreach($data as $courseUid => $course) {
			$manifestUrl = $_POST['url'] . $course['fileName'];
			echo ''
			 . "<div class='chatmessagegreen'>"
			 . "<form name='frmSwipe' method='POST'>"
			 . "<input type='hidden' name='action' value='swipe' />"
			 . "<input type='hidden' name='url' value='" . $_POST['url'] . "' />"
			 . "<input type='hidden' name='uid' value='" . $courseUid . "' />"
			 . "<input type='hidden' name='manifest' value='" . $manifestUrl . "' />"
			 . "<b>Found:</b> <a href='" . $manifestUrl . "'>" . $course['title'] . "</a><br/>\n"
			 . "<input type='submit' value='Copy to this server &gt;&gt;' />"
			 . "</form>"
			 . "</div>";
		}

		echo "<div class='chatmessageblack'><h2>Raw Data</h2><pre>"; 
		print_r($data); 
		echo "</pre></div>\n";

	}

	//----------------------------------------------------------------------------------------------
	//*	swip a package
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('swipe' == $_POST['action'])) {
		$peer = $_POST['url'];
		$uid = $_POST['uid'];
		$manifest = $_POST['manifest'];
		$canonical = 'data/lessons/' . $uid . '/manifest.xml';

		$raw = implode(file($manifest));

		echo ''
		 . "<div class='chatmessageblack'>"
		 . "<h2>Raw Data</h2>"
		 . "<pre>" . htmlentities($raw) . "</pre>"
		 . "</div>\n";


		echo "<div class='chatmessageblack'>Making folder: $canonical</div>";
		$kapenta->fileMakeSubdirs($canonical);
		$kapenta->fileMakeSubdirs('data/lessons/' . $uid . '/documents/x.x');
		$kapenta->fileMakeSubdirs('data/lessons/' . $uid . '/covers/x.x');
		$kapenta->fileMakeSubdirs('data/lessons/' . $uid . '/thumbs/x.x');
		$kapenta->fs->put($canonical, $raw);

		$model = new Lessons_Course($uid);
		foreach($model->documents as $doc) {
			lessons_swipeDn($peer, $doc['file']);
			lessons_swipeDn($peer, $doc['cover']);
			lessons_swipeDn($peer, $doc['thumb']);
		}

		$report = lessons_rebuild_index();
		echo "<div class='chatmessageblack'><h2>Rebuilding index</h2>$report</div>\n";
	}
	echo $theme->expandBlocks('[[:theme::ifscrollfooter:]]');

	function lessons_swipeDn($peer, $fileName) {
		global $kapenta;

		if (true == $kapenta->fs->exists($fileName)) {
			echo "<div class='chatmessagegreen'>File exists: " . $fileName . "</div>\n";
			return;
		}

		$dn = implode(file($peer . $fileName));
			if ('' != $dn) {

				$kapenta->fs->put($fileName, $dn);
				echo ''
				 . "<div class='chatmessagegreen'>"
				 . "Downloaded: " . $fileName . " (" . strlen($dn) . " bytes)"
				 . "</div>\n";
				flush();

			} else {

				echo "<div class='chatmessagered'>Download failed: " . $fileName . "</div>\n";
				flush();

			}
	}

?>
