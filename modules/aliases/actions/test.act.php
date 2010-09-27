<?

	require_once($kapenta->installPath . 'modules/alias/models/alias.mod.php');

	echo "saving new alias...<br/>"; flush();

	$model = new Alias_Alias();
	$model->refModule = 'scaffold';
	$model->refModel = 'Scaffold_ModuleDefinition';
	$model->refUID = '157150205620517929';
	$model->alias = 'Scaffold';
	$model->aliaslc = 'scaffold';
	//$model->save();


	echo "saved new alias {$model->UID} <br/>";
?>
