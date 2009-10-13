<?

//-------------------------------------------------------------------------------------------------
//	check to see if a page has any new notices
//-------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/notifications/models/pageclient.mod.php');
	if ($request['ref'] == '') { die(); }
	
	//---------------------------------------------------------------------------------------------
	//	load the client and check for messages
	//---------------------------------------------------------------------------------------------

	if (strpos(' ' . $request['ref'], 'Array') != false) {
		logErr('notifications', 'pagecheck', 'Array in UID');
	}

	$model = new PageClient($request['ref']);
	echo "#INBOX " . $model->data['UID'] . "\n";
	if ($model->data['inbox'] != '') { 
		echo $model->data['inbox'];
		$model->data['inbox'] = '';	
		$model->save();
	}

	//---------------------------------------------------------------------------------------------
	//	update the timestamp if record is getting old
	//---------------------------------------------------------------------------------------------
	if ($model->old == true) { $model->updateTimeStamp(); }

	//---------------------------------------------------------------------------------------------
	//	handle any data arriving by HTTP POST
	//---------------------------------------------------------------------------------------------
	if ((array_key_exists('action', $_POST) == true) && ($_POST['action'] == 'subscribe')) {
		$channels = explode("\n", $_POST['detail']);
		foreach($channels as $channel) {
			if (strlen(trim($channel)) > 3) {

				//---------------------------------------------------------------------------------
				//	check if user has permissions to subscribe to this channel
				//---------------------------------------------------------------------------------
				$parts = explode('-', $channel, 2);	// break in half at first hyphen
				$block = '[[:' . $parts[0] . '::channelauth::channel=' . $parts[1] . ':]]';			
				echo "#AUTHBLOCK $block \n";
				$auth = expandBlocks($block, '');

				if ('yes' == $auth) {

					$result = $model->subscribe($channel);
					if (true == $result) {
						echo "\n#SUBSCRIBED (new subscription)\n";	
					} else {
						echo "\n#SUBSCRIBED (subscription already exists)\n";	
					}

				} else {
					echo "#NOT AUTHORISED ON CHANNEL $channel\n";
				}
			}
		}
	}

	echo "\n#ENDOFMESSAGES\n";

	//---------------------------------------------------------------------------------------------
	//	clear out any dead page clients (have become too old/not checked)
	//---------------------------------------------------------------------------------------------
	$model->bringOutYourDead();
	
?>
