<? /*
<form name='editschool' method='POST' action='/schools/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' value='%%name%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Country:</b></td>
    <td><input type='text' name='country' value='%%country%%' size='50' /></td>
  </tr>
</table>
<br/>
<b>Description of this school:</b><br/>

<input type='hidden' id='description-edit-hidden' name='description-loader' value='%%descriptionJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var $refFieldName = '';
descriptionJSEHR = document.getElementById('description-edit-hidden');
descriptionJSEHR.value = descriptionJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
descriptionJSEHR.value = descriptionJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('description', descriptionJSEHR.value, 500, 400,'/modules/editor/');
//-->
</script><br/>
<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='save' />
    </form>
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='Delete' />
   </form>
   </td>
 </tr>
</table>

<h2>Images</h2>
[[:images::uploadmultiple::refModule=schools::refUID=%%UID%%:]]
*/ ?>