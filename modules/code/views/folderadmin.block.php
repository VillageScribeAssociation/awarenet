<? /*
[[:theme::navtitlebox::width=570::label=Admin Options:]]
<!--
<div class='indent'>
<h3>Add Subfolder</h3>
<form name='addSubFolder' method='POST' action='/code/save/'>
<input type='hidden' name='action' value='addSubFolder' />
<input type='hidden' name='UID' value='%%UID%%' />
<b>Subfolder: </b>
<input type='text' name='folder' value='' />
<input type='submit' value='create' />
</form>
</div> -->

<div class='indent'>
<h3>Add Document</h3>
<form name='addDocument' method='POST' action='/code/save/'>
<input type='hidden' name='action' value='addDocument' />
<input type='hidden' name='UID' value='%%UID%%' />
<table noborder>
  <tr>
    <td><b>Title</b></td>
    <td><input type='text' name='title' value='' /></td>
  </tr>

  <tr>
    <td><b>Type</b></td>
    <td>[[:code::selecttype::default=txt:]]</td>
  </tr>
</table>
<b>Description:</b><br/>
<textarea rows='6' cols='50' name='description'></textarea><br/>
<b>Content:</b><br/>
<textarea rows='6' cols='50' name='content'></textarea><br/>

<input type='submit' value='create' />
</form>
</div>
*/ ?>