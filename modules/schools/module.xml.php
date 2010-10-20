<? /*
<module>
    <modulename>schools</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Schools module, top of class heirarchy.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <search>no</search>
	<models>
      <model>
        <name>Schools_Schools</name>
		<description>Represents schools.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>member</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		public:p|schools|Schools_School|show
		student:p|schools|Schools_School|show
		student:c|schools|Schools_School|edit|(if)|creator
		student:p|schools|Schools_School|announcements-show

		teacher:p|schools|Schools_School|show
		teacher:c|schools|Schools_School|edit|(if)|creator
		teacher:p|schools|Schools_School|announcements-show
    </defaultpermissions>
</module>
*/ ?>
