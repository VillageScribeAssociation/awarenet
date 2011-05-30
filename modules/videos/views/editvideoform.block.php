<? /*
[[:theme::navtitlebox::label=Preview:]]
[[:videos::player::videoUID=%%UID%%:]]
<br/>

[[:theme::navtitlebox::label=Video Details:]]
<form name='editVideo' method='POST' action='%%serverPath%%videos/savevideo/' >
<input type='hidden' name='action' value='saveVideo' />
<input type='hidden' name='UID' value='%%UID%%' />
<input type='hidden' name='refModule' value='%%refModule%%' />
<input type='hidden' name='refModel' value='%%refModel%%' />
<input type='hidden' name='refUID' value='%%refUID%%' />
<input type='hidden' name='return' value='%%return%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' size='50' name='title' value='%%title%%' /></td>
  </tr>
  <tr>
    <td><b>Caption:</b></td>
    <td>
      <textarea name='caption' rows='5' cols='47'>%%caption%%</textarea></td>
  </tr>
  <tr>
    <td><b>Licence:</b></td>
    <td>
      <select name='licence'>
        <option value='%%licence%%'>%%licence%%</option>
        <option value='Copyright'>Copyright</option>
        <option value='Public Domain'>Public Domain</option>
        <option value='GNU-GPL'>GNU-GPL</option>
        <option value='GNU-LGPL'>GNU-LGPL</option>
        <option value='AFL'>AFL</option>
        <option value='GFDL'>GFDL</option>
        <option value='CC-BY-NC-SA'>CC-BY-NC-SA</option>
        <option value='CC-BY-NC-ND'>CC-BY-NC-ND</option>
        <option value='CC-BY-SA'>CC-BY-SA</option>
        <option value='CC-BY-ND'>CC-BY-ND</option>
      </select>

      <b>Weight:</b>
      <input type='text' size='5' name='weight' value='%%weight%%' />

    </td>
  </tr>
  <tr>
    <td><b>Attribution:    </b></td>
    <td><input type='text' size='50' 
         name='attribName' value='%%attribName%%'/></td>
  </tr>
  <tr>
    <td><b>Source URL:</b></td>
    <td><input type='text' size='50' 
         name='attribURL' value='%%attribUrl%%' /></td>
  </tr>
  <tr>
    <td></td>
    <td><input type='submit' value='Save' /></td>
  </tr>
</table>
</form>
<br/>

[[:theme::navtitlebox::label=Video Thumbnail:]]
<iframe name='videoThumb' id='ifVideoThumb'
  src='%%serverPath%%/images/uploadsingle/refModule_videos/refModel_videos_video/refUID_%%UID%%/category_thumb/'
  width='100%' height='400' frameborder='0' ></iframe>

%%returnLink%%
*/ ?>
