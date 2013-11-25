<?

require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
$c = new Code();

$c->data['UID'] = $kapenta->createUID();
$c->data['project'] = 'Kapenta 3b';
$c->data['parent'] = 'none';
$c->data['type'] = 'folder';
$c->data['title'] = '/';
$c->data['version'] = '3';
$c->data['revision'] = '0';
$c->data['description'] = '';
$c->data['content'] = '';
$c->data['author'] = $user->d['UID'];
$c->data['createdOn'] = mysql_datetime();
$c->data['recordalias'] = 'kapenta3b';
//$c->save();

?>
