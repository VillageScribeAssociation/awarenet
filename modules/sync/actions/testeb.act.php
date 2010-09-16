<?

//-------------------------------------------------------------------------------------------------
//*	test of procExecBackground
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	for ($i = 0; $i < 10; $i++) {

		$od = $installPath . 'data/temp/' . $kapents->createUID() . '.jpg';
		$slowCmd = "wget --output-document=$od http://upload.wikimedia.org/wikipedia/commons/thumb/9/96/Portrait_Of_A_Baboon.jpg/785px-Portrait_Of_A_Baboon.jpg";

		echo time() . "<br/>\n"; flush();
		$kapenta->procExecBackground($slowCmd);
	}

?>
