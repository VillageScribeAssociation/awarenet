<? /*
<form
	id='frmEditGallery%%UID%%'
	name='editGallery'
	method='POST'
	action='%%serverPath%%/videos/savegallery/'
	onSubmit="khta.updateAllAreas();"
>
<input type='hidden' name='action' value='saveGallery' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%galleryTitle%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Origin:</b></td>
    <td>[[:videos::selectorigin::default=%%origin%%:]]</td>
  </tr>
</table>
<br/>
<b>Description:</b> <small>what is this gallery about?</small><br/>
<div
	class='HyperTextArea64'
	title='description'
	width='-1'
	height='400'
	style='visibility: hidden; display: none'
	refModule='videos'
	refModel='videos_gallery'
	refUID='%%UID%%'
>
%%description64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>
</form>

<br/>
<table noborder>
  <tr>
    <td valign='top'>
      <input
        type='button'
        value='Save'
        onClick="$('#frmEditGallery%%UID%%').submit();"
      />
    </td>
    <td>
      [[:tags::editbutton::refModule=videos::refModel=videos_gallery::refUID=%%UID%%:]]
    </td>
    <td>
      <form name='cDelete' method='GET' action='%%delUrl%%'>
      <input type='submit' value='Delete' />
      </form>
    </td>
 </tr>
</table>
*/ ?>
