<? /*
<div class='indent'>
<form name='editGallery' method='POST' action='/gallery/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%galleryTitle%%' size='50' /></td>
  </tr>
</table>
<br/>
<b>Description:</b> <small>what is this gallery about?</small><br/>
<input type='hidden' id='content-edit-hidden' name='content-loader' value='%%descriptionJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var refFieldName = '';
contentJSEHR = document.getElementById('content-edit-hidden');
contentJSEHR.value = contentJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
contentJSEHR.value = contentJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('description', contentJSEHR.value, 530, 300,'/modules/editor/');
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
</div>
*/ ?>