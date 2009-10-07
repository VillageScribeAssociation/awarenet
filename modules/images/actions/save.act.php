<?

	require_once($installPath . 'modules/images/models/image.mod.php');
	require_once($installPath . 'modules/images/inc/images__weight.inc.php');

//--------------------------------------------------------------------------------------------------
//	save changes to an image
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('action', $_POST) == true)
	   AND ($_POST['action'] == 'saveImage')
	   AND (array_key_exists('UID', $_POST) == true) ) {

		//------------------------------------------------------------------------------------------
		//	check reference and authorisation
		//------------------------------------------------------------------------------------------
	   
		$i = new Image($_POST['UID']);
		if ($i->data['fileName'] == '') { do404(); }
		
		$authArgs = array('UID' => $i->data['refUID']);
		if (authHas($i->data['refModule'], 'images', '') == false) { return false; }

		//------------------------------------------------------------------------------------------
		//	make the changes
		//------------------------------------------------------------------------------------------
		
		$i->data['title'] = $_POST['title'];
		$i->data['caption'] = $_POST['caption'];
		$i->data['licence'] = $_POST['licence'];
		$i->data['attribName'] = $_POST['attribName'];
		$i->data['attribURL'] = $_POST['attribURL'];
		if (is_numeric($_POST['weight']) == true) {	$i->data['weight'] = $_POST['weight']; }
		
		$i->save();
		images__checkWeight($i->data['refModule'], $i->data['refUID']);
		
		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------
		
		if (array_key_exists('return', $_POST)) {
			if ($_POST['return'] == 'uploadmultiple') {
				$retUrl = 'images/uploadmultiple/refModule_' . $i->data['refModule'] 
					. '/refUID_' . $i->data['refUID'] . '/';
				do302($retUrl);
			}
		}
		do302('images/edit/' . $i->data['recordAlias']);
	 
	}

//--------------------------------------------------------------------------------------------------
//	increment image weight (bump image one place up the list)
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('inc', $request['args']) == true)
	   AND (dbRecordExists('images', $request['args']['inc']) == true) ) {

		//------------------------------------------------------------------------------------------
		//	check reference and authorisation
		//------------------------------------------------------------------------------------------
	   
		$model = new Image($request['args']['inc']);
		if ($model->data['fileName'] == '') { do404(); }
		
		$authArgs = array('UID' => $model->data['refUID']);
		if (authHas($model->data['refModule'], 'images', '') == false) { return false; }

		//------------------------------------------------------------------------------------------
		//	make the changes
		//------------------------------------------------------------------------------------------
		
		$refModule = $model->data['refModule'];
		$refUID = $model->data['refUID'];
		$nextHeaviest = images__getNextHeaviest($refModule, $refUID, $model->data['weight']);
		if ($nextHeaviest != false) {
			$model->data['weight'] = $model->data['weight'] + 1;	// increment this one
			$model->save();

			$model->load($nextHeaviest);
			$model->data['weight'] = $model->data['weight'] - 1;	// decrement the one above
			$model->save();
		}
		
		images__checkWeight($refModule, $refUID);	// make sure they're in order

		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------
		
		if (array_key_exists('return', $request['args'])) {
			if ($request['args']['return'] == 'uploadmultiple') {
				$retUrl = 'images/uploadmultiple/refModule_' . $model->data['refModule'] 
					. '/refUID_' . $model->data['refUID'] . '/';
				do302($retUrl);
			}
		}
		do302('images/edit/' . $model->data['recordAlias']);
	 
	}


//--------------------------------------------------------------------------------------------------
//	decrement image weight (bump one place down the list)
//--------------------------------------------------------------------------------------------------

	if ( (array_key_exists('dec', $request['args']) == true)
	   AND (dbRecordExists('images', $request['args']['dec']) == true) ) {

		//------------------------------------------------------------------------------------------
		//	check reference and authorisation
		//------------------------------------------------------------------------------------------
	   
		$model = new Image($request['args']['dec']);
		if ($model->data['fileName'] == '') { do404(); }
		
		$authArgs = array('UID' => $model->data['refUID']);
		if (authHas($model->data['refModule'], 'images', '') == false) { return false; }

		//------------------------------------------------------------------------------------------
		//	make the changes
		//------------------------------------------------------------------------------------------
		
		$refModule = $model->data['refModule'];
		$refUID = $model->data['refUID'];
		$nextLightest = images__getNextLightest($refModule, $refUID, $model->data['weight']);
		if ($nextLightest != false) {
			$model->data['weight'] = $model->data['weight'] - 1;	// decrement this one
			$model->save();

			$model->load($nextLightest);
			$model->data['weight'] = $model->data['weight'] + 1;	// increment the one below
			$model->save();
		}
		
		images__checkWeight($refModule, $refUID);	// make sure they're in order

		//------------------------------------------------------------------------------------------
		//	redirect back
		//------------------------------------------------------------------------------------------
		
		if (array_key_exists('return', $request['args'])) {
			if ($request['args']['return'] == 'uploadmultiple') {
				$retUrl = 'images/uploadmultiple/refModule_' . $model->data['refModule'] 
					. '/refUID_' . $model->data['refUID'] . '/';
				do302($retUrl);
			}
		}
		do302('images/edit/' . $model->data['recordAlias']);
	 
	}

?>
