<? /*
<form name='editAbstract' method='POST' action='/projects/save/'>
<input type='hidden' name='action' value='saveAbstract' />
<input type='hidden' name='UID' value='%%UID%%' />

<h1>%%projectTitle%% (Abstract)</h1>

<form name='editProject' method='POST' action='/projects/save/'>
<input type='hidden' name='action' value='saveChangeTitle' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Project Title:</b></td>
    <td><input type='text' name='title' value='%%projectTitle%%' size='40' /></td>
    <td><input type='submit' value='Change Title' /></td>
  </tr>
</table>
</form>
<br/>

<form name='editAbstract' method='POST' action='/projects/save/'>
<input type='hidden' name='action' value='saveAbstract' />
<input type='hidden' name='UID' value='%%UID%%' />

<input type='hidden' id='abstract-edit-hidden' name='content-loader' value='%%abstractJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var $refFieldName = '';
contentJSEHR = document.getElementById('abstract-edit-hidden');
contentJSEHR.value = contentJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
contentJSEHR.value = contentJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('abstract', contentJSEHR.value, 570, 400,'/modules/editor/');
//-->
</script><br/>

<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='Save Changes' />
    </form>
   </td>
   <td>
   <form name='cancelEdit' method='GET' action='%%editUrl%%'>
   <input type='submit' value='Cancel' />
   </form>
   </td>
 </tr>
</table>

<h2>Images</h2>
[[:images::uploadmultiple::refModule=projects::refUID=%%UID%%:]]
<br/>UID:%%UID%%<br/>
*/ ?>
