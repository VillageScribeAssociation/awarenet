<?

//--------------------------------------------------------------------------------------------------
//*	removed from awareNet pending security review
//--------------------------------------------------------------------------------------------------

/*

include('DB.php');
$db = DB::connect("pgsql://colorjack@localhost(/tmp)");
session_start();
$session_id = session_id();

if($_REQUEST['s']) $session_id = ereg_replace('[^a-z0-9]','',$s); // FIXME: BAD
$id = array_key_exists('id',$_REQUEST) ? (int) $_REQUEST['id'] : 0;
$ip = array_key_exists('REMOTE_ADDR',$_SERVER) ? $_SERVER['REMOTE_ADDR'] : null;
$id_client = array_key_exists('id_client',$_SESSION) && (int) $_SESSION['id_client'] ? (int) $_SESSION['id_client'] : null;
$edits = array_key_exists('edits',$_REQUEST) ? (int) $_REQUEST['edits'] + 1 : 1;
$canvas_ext = array_key_exists('add',$_REQUEST) ? $_REQUEST['add'] : null;
$canvas_uri = array_key_exists('url',$_REQUEST) ? $_REQUEST['url'] : null;

if($id) {
 $r = $db->getRow("SELECT id_canvas,count_edit
        FROM cj_canvas JOIN cj_canvas_revision USING (id_canvas)
        WHERE id_canvas_revision = '$id'",DB_FETCHMODE_ORDERED);
 if($r) {
 $canvas = $r[0];
 $count_edit = $r[1];
 } else $id = 0;
}
if(!$id) {
  $count_edit = $edits;
  $id  = $db->nextId("cj_canvas_revision_id_canvas_revision");
  $canvas = $db->nextId('cj_canvas_id_canvas');
  $db->autoExecute("cj_canvas",array('id_canvas'=>$canvas));
  $db->autoExecute("cj_canvas_revision",array('id_canvas'=>$canvas,'id_canvas_revision'=>$id,'session_id'=>$session_id));
  // echo "id_canvas_revision = $id_prior;";
}

$edits --;
if($edits == $count_edit && !($canvas_ext || $canvas_uri)) exit;
$id_prior = $id; $id_canvas = $canvas;

if($canvas_ext || $canvas_uri) {
	$id  = $db->nextId("cj_canvas_revision_id_canvas_revision");
	$revision = array('id_canvas'=>$id_canvas,'id_canvas_revision'=>$id,
			'id_canvas_revision_prior'=>$id_prior,'session_id'=>$session_id);
	if($canvas_ext) $revision['canvas_ext'] = $canvas_ext;
	if($canvas_uri) $revision['canvas_uri'] = $canvas_uri;
	$db->autoExecute("cj_canvas_revision", $revision);
}

if(1) {
 $revisions = $db->query("SELECT id_canvas_revision,canvas_ext,canvas_uri
	FROM cj_canvas_revision
	WHERE id_canvas = '$id_canvas'
	AND id_canvas_revision > '$id_prior'
	AND id_canvas_revision <> '$id'
	AND session_id <> '$session_id'
	ORDER BY id_canvas_revision");

 $queue = array();
 while($revision = $revisions->fetchRow(DB_FETCHMODE_ORDERED)) {
   $id_prior = $revision[0];
   if($revision[1]) $queue[] = 'revision[revision.length]='.$revision[1];
   if(count($queue) > 50) {
	$queue[]="id_canvas_revision = $id_prior"; $queue[]='';
	echo join(";\n",$queue);
	$queue = array();
   }
 }
 if(count($queue)) {
	$queue[]="id_canvas_revision = $id_prior"; $queue[]='';
	echo join(";\n",$queue);
	$queue = null;
 }
}

*/

?>
