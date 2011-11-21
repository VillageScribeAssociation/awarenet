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
        <name>schools_school</name>
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
		student:p|schools|schools_school|contact-show

		teacher:p|schools|schools_school|show
		teacher:c|schools|schools_school|edit|(if)|creator
		teacher:c|schools|schools_school|edit|(if)|member
		teacher:p|schools|schools_school|announcements-add
		teacher:p|schools|schools_school|announcements-remove
		teacher:p|schools|schools_school|announcements-edit
		teacher:p|schools|schools_school|announcements-show
		teacher:p|schools|schools_school|contact-add
		teacher:p|schools|schools_school|contact-remove
		teacher:p|schools|schools_school|contact-new
		teacher:p|schools|schools_school|contact-edit
		teacher:p|schools|schools_school|contact-show
    </defaultpermissions>
</module>
*/ ?>
