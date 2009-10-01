<?

//--------------------------------------------------------------------------------------------------
//	view fragment for adding this helper to an edit form at design time
//--------------------------------------------------------------------------------------------------

// requires:
//	$fieldName - name of database field
//	$fieldValue - preset value of editor
//	$helper - the name of this helper (consider removing this, helpers know their own names)

	$refFieldName = $fieldName . 'JSEHR';

	$fragment = "
<input type='hidden' id='" . $fieldName . "-edit-hidden' name='" . $fieldName . "-loader' 
	value='" . doJSMarkup($fieldValue) . "' />
<script language='JavaScript' type='text/javascript' 
	src='" . $serverPath . "helper/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var $refFieldName = '';
" . $refFieldName . " = document.getElementById('" . $fieldName . "-edit-hidden');
" . $refFieldName . ".value = " 
. $refFieldName . ".value.replace(/--squote--/g, String.fromCharCode(39));
" . $refFieldName 
. ".value = " . $refFieldName . ".value.replace(/--dquote--/g, String.fromCharCode(34));

area = new HyperTextArea('" . $fieldName . "', " 
	. $refFieldName . ".value, 500, 600,'" . $serverPath. "helper/editor/');
//-->
</script>
	";
		    
?>
