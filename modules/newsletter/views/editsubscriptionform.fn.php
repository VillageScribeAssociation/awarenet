<?

	require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to edit a Subscription object
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Newsletter_Subscription object [string]
//opt: subscriptionUID - UID of a Newsletter_Subscription object, overrides UID [string]

function newsletter_editsubscriptionform($args) {
	global $kapenta;
	global $utils;
	global $theme;

	$html = '';					//% return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and args
	//----------------------------------------------------------------------------------------------
	$raUID = '';
	if (true == array_key_exists('UID', $args)) { $raUID = $args['UID']; }
	if (true == array_key_exists('raUID', $args)) { $raUID = $args['raUID']; }
	if (true == array_key_exists('subscriptionUID', $args)) { $raUID = $args['subscriptionUID']; }
	if ('' == $raUID) { return ''; }

	$model = new Newsletter_Subscription($raUID);	//% the object we're editing [object:Newsletter_Subscription]

	if (false == $model->loaded) { return ''; }
	if (false == $kapenta->user->authHas('newsletter', 'newsletter_subscription', 'edit', $model->UID)) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	load the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/editsubscriptionform.block.php');
	$labels = $model->extArray();
	//$labels['description64'] = $utils->b64wrap($labels['description']);
	// ^ add any labels, block args, etc here

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
