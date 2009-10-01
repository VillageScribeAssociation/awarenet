<?

//--------------------------------------------------------------------------------------------------
//	installer for wiki module: creates wiki (articles), wikicategories, wikicatindex, wikirevisions
//	and wikidelete tables
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/wiki/models/wiki.mod.php');
require_once($installPath . 'modules/wiki/models/wikirevision.mod.php');

function install_wiki_module() {
	global $installPath;
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }
	$report = '';

	//----------------------------------------------------------------------------------------------
	//	create wiki (articles) table
	//----------------------------------------------------------------------------------------------
	$model = new Wiki();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	create wikicategories table
	//----------------------------------------------------------------------------------------------
	// TODO

	//----------------------------------------------------------------------------------------------
	//	create wikicatindex table
	//----------------------------------------------------------------------------------------------
	// TODO

	//----------------------------------------------------------------------------------------------
	//	create wikirevisions table
	//----------------------------------------------------------------------------------------------
	$model = new WikiRevision();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	create wikideletions table (articles nominated for deletion)
	//----------------------------------------------------------------------------------------------
	// TODO

	return $report;
}

//--------------------------------------------------------------------------------------------------
//	unstall the wiki
//--------------------------------------------------------------------------------------------------

function isinstalled_wiki_module() {
	// TODO
}

//--------------------------------------------------------------------------------------------------
//	uninstall the wiki
//--------------------------------------------------------------------------------------------------
//	note: this will remove the wiki module, associated database tables and all data from the wiki,
//	including attached files and images.

function uninstall_wiki_module() {
	// TODO
}

?>
