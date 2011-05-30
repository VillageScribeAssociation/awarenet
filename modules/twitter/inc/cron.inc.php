<?

	require_once($kapenta->installPath . 'modules/twitter/models/tweet.mod.php');
	require_once($kapenta->installPath . 'modules/twitter/inc/send.inc.php');

//--------------------------------------------------------------------------------------------------
//*	processes run regularly to keep things tidy
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	ten minute cron
//--------------------------------------------------------------------------------------------------
//returns: HTML report of any actions taken [string]

function twitter_cron_tenmins() {
	global $db, $registry;
	$report = "<h2>twitter_cron_tenmins</h2>\n";	//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	send any unsent tweets if twitter is enabled on this node
	//----------------------------------------------------------------------------------------------

	if ('yes' == $registry->get('twitter.enabled')) {
		$conditions = array("status='new'");
		$range = $db->loadRange('twitter_tweet', '*', $conditions, 'createdOn ASC');

		foreach($range as $item) {
			$model = new Twitter_Tweet($item['UID']);
			$model->status = 'sent';
			$model->save();

			$report = twitter_send($model->content);
		}

		$sql = "update twitter_tweet set status='sent' where status='new'";
		$db->query($sql);

	} else {
		$report .= "Twitter not enabled on this node.<br/>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;

}

?>
