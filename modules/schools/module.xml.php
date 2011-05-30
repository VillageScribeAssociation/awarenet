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
        <name>schools_schools</name>
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
		public:p|schools|schools_school|show
		student:p|schools|schools_school|show
		student:c|schools|schools_school|edit|(if)|creator
		student:p|schools|schools_school|announcements-show

		teacher:p|schools|schools_school|show
		teacher:c|schools|schools_school|edit|(if)|creator
		teacher:p|schools|schools_school|announcements-show
    </defaultpermissions>
</module>
*/ ?>
