<?

//-------------------------------------------------------------------------------------------------
//*	test of procExecBackground
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	for ($i = 0; $i < 10; $i++) {

		$od = $kapenta->installPath . 'data/temp/' . $kapents->createUID() . '.jpg';

		$url = "http://upload.wikimedia.org/wikipedia/commons/thumb/9/96/"
			 . "Portrait_Of_A_Baboon.jpg/785px-Portrait_Of_A_Baboon.jpg"

		$slowCmd = "wget --output-document=$od $url";

		echo time() . "<br/>\n"; flush();
		$kapenta->procExecBackground($slowCmd);
	}

?>
