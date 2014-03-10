<?php

	require_once($kapenta->installPath . 'modules/polls/models/question.mod.php');
	require_once($kapenta->installPath . 'modules/polls/models/answer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	temporary script to add a set of polls to a forum post
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$refModule = 'forums';
	$refModel = 'forums_thread';
	$refUID = '205588145632739989';

//	Rate these facilities in terms of how they are needed for a school to function and provide 
//	an adequate level of education. Mark your selection for each facility by clicking in the 
//	appropriate box.

	$questions = array(
		'179601723062897919' => '01. A reliable source of electricity',
		'134318015620902969' => '02. Drinking water, hygienic toilets and sinks',
		'862863669137659036' => '03. Weather-proof and structurally sound buildings',
		'147462281117487983' => '04. A desk and a chair for each learner',
		'208086298510479181' => '05. A telephone connection',
		'541839799348466791' => '06. Text books for each learner to take home',
		'751574107219197922' => '07. A school hall',
		'695935807110497444' => '08. A caretaker for the school buildings and grounds',
		'221188471147468150' => '09. A black or whiteboard in each class room',
		'374509522828906784' => '10. A computer laboratory for learners and teachers with Internet access',
	);

//	They can choose to click on the following 4 boxes:

	$answers = array(
		'not necessary at all',
		'nice but not necessary',
		'important but not essential',
		'absolutely essential',
	);

//--------------------------------------------------------------------------------------------------
//	clear the questions first if necessary
//--------------------------------------------------------------------------------------------------

	if ((true == array_key_exists('clear', $kapenta->request->args)) && ('yes' == $kapenta->request->args['clear'])) {
		foreach($questions as $UID => $txt) {
			$kapenta->db->query("DELETE FROM `polls_vote` WHERE question='$UID'");
			$kapenta->db->query("DELETE FROM `polls_answer` WHERE question='$UID'");
			$kapenta->db->query("DELETE FROM `polls_question` WHERE UID='$UID'");
		}
		echo "Deleted $UID := $txt and answers.<br/>\n";
	}

//--------------------------------------------------------------------------------------------------
//	add the questions
//--------------------------------------------------------------------------------------------------

	foreach($questions as $UID => $text) {
		$question = new polls_question($UID);
		if (false == $question->loaded) {

			$question->UID = $UID;
			$question->refModule = $refModule;
			$question->refModel = $refModel;
			$question->refUID = $refUID;
			$question->content = $text;
			$report = $question->save();

			if ('' == $report) {
				echo "Saved question: $UID := $text<br/>\n";
			} else {
				echo "Error: $UID := $text<br/>\n$report<br/>\n";
			}

			foreach($answers as $i => $answertxt) {
				$answer = new polls_answer();

				$answer->question = $UID;
				$answer->weight = $i;
				$answer->content = $answertxt;

				$report = $answer->save();
				if ('' == $report) {
					echo "Saved answer to question: $UID $i := $answertxt<br/>\n";
				} else {
					echo "Error: $UID $i := $answer<br/>\n$report<br/>\n";
				}
			}
		} else {
			echo "Question exists: $UID $text <br/>\n";
		}
	}

?>
