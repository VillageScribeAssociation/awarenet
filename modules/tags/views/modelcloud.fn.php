<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');

//--------------------------------------------------------------------------------------------------
//|	show tags of a particular kind of object (eg, projects, blog posts) as a cloud
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - name of a kapenta object type [string]
//opt: num - maximum number of tags to show, default is 30 (int) [string]

function tags_modelcloud($args) {
	global $db;
	global $user;
	global $theme;
	global $cache;

	$html = '';
	$num = 30;

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }
	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];

	//----------------------------------------------------------------------------------------------
	//	check cache
	//----------------------------------------------------------------------------------------------

	$html = $cache->get($args['area'], $args['rawblock']);
	if ('' != $html) { return $html; }

	//----------------------------------------------------------------------------------------------
	//	load top n tags from the database by counting Tags_Index objects
	//----------------------------------------------------------------------------------------------
	//note: this is not great, a precache of this would be better

	$sql = "SELECT tagUID, count(UID) as weight "
		 . "FROM tags_index "
		 . "WHERE refModule='" . $db->addMarkup($refModule) . "' "
		 . "AND refModel='" . $db->addMarkup($refModel) . "' "
		 . "GROUP BY tagUID "
		 . "ORDER BY weight DESC "
	     . "LIMIT $num";

	//echo $sql;

	$result = $db->query($sql);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$tags = array();

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$model = new Tags_Tag($row['tagUID']);
		$tags[$model->name] = array(
			'name' => strtolower($model->name),
			'weight' => $row['weight'],
			'link' => '%%serverPath%%' . $refModule . '/tag/' . $model->name
		);
	}	

	asort($tags);

	$html = $theme->makeTagCloud($tags);

	//----------------------------------------------------------------------------------------------
	//	add to view cache
	//----------------------------------------------------------------------------------------------

	$html = $theme->expandBlocks($html, $args['area']);
	$cache->set('tags-modelcloud-' . $refModel, $args['area'], $args['rawblock'], $html);

	return $html;
}

?>
