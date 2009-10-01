<? /*
<br/>
<form name='editPost' method='POST' action='/moblog/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title: </b></td>
    <td><input type='text' name='title' value='%%mbTitle%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Published: </b></td>
    <td>
      <select name='published'>
        <option value='yes'>yes</option>
        <option value='no'>no</option>
      </select>
    </td>
  </tr>
</table>
<br/>
<b>Post Body: </b><small>(use special {fold} marker at end of introduction)
<input type='hidden' id='content-edit-hidden' name='content-loader' value='%%contentJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var $refFieldName = '';
contentJSEHR = document.getElementById('content-edit-hidden');
contentJSEHR.value = contentJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
contentJSEHR.value = contentJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('content', contentJSEHR.value, 500, 400,'/modules/editor/');
//-->
</script><br/>
<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='Save' />
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
[[:images::uploadmultiple::refModule=moblog::refUID=%%UID%%:]]
*/ ?>