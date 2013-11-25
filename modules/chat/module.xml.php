<? /*
<module>
    <modulename>chat</modulename>
    <version>0</version>
    <revision>0</revision>
    <description>describe your module here</description>
    <core>no</core>
    <dependancy>
    </dependancy>
    <models>
      <model>
        <name>chat_room</name>
		<description>A local or global chat room.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
          <permission>list</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>member</relationship>
        </relationships>
      </model>
	</models>
    <defaultpermissions>
		student:p|chat|chat_room|list
		student:p|chat|chat_room|join
		student:p|chat|chat_room|show

		teacher:p|chat|chat_room|list
		teacher:p|chat|chat_room|join
		teacher:p|chat|chat_room|show
    </defaultpermissions>
</module>
*/ ?>
