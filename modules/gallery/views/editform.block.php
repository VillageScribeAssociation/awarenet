<? /*

<form name='editGallery' method='POST' action='%%serverPath%%/gallery/save/'>
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
%%descriptionJs64%%
[[:editor::base64::jsvar=descriptionJs64::name=description:]]
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

*/ ?>
