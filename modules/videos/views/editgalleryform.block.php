<? /*
<div class='indent'>
<form name='editGallery' method='POST' action='%%serverPath%%/videos/savegallery/'>
<input type='hidden' name='action' value='saveGallery' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%galleryTitle%%' size='50' /></td>
  </tr>
</table>
<br/>
<b>Description:</b> <small>what is this gallery about?</small><br/>
<div class='HyperTextArea64' title='description' width='100%' height='400'>
%%description64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>
<br/>

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
