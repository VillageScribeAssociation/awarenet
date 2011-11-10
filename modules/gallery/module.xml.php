<? /*
<module>
    <modulename>gallery</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>User image galleries.</description>
    <core>no</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema></dbschema>
    <search>no</search>
	<dependencies>
	</dependencies>
    <models>
      <model>
        <name>gallery_gallery</name>
		<description>User picture gallery.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>owner</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		public:p|gallery|gallery_gallery|show

		student:p|gallery|gallery_gallery|new
		student:p|gallery|gallery_gallery|show
		student:p|gallery|gallery_gallery|images-show
		student:p|gallery|gallery_gallery|comments-add
		student:p|gallery|gallery_gallery|comments-show
		student:c|gallery|gallery_gallery|comments-retract|(if)|creator
		student:c|gallery|gallery_gallery|edit|(if)|creator
		student:c|gallery|gallery_gallery|images-add|(if)|creator
		student:c|gallery|gallery_gallery|images-remove|(if)|creator
		student:c|gallery|gallery_gallery|images-edit|(if)|creator
		student:c|gallery|gallery_gallery|delete|(if)|creator

		teacher:p|gallery|gallery_gallery|new
		teacher:p|gallery|gallery_gallery|show
		teacher:p|gallery|gallery_gallery|images-show
		teacher:p|gallery|gallery_gallery|comments-add
		teacher:p|gallery|gallery_gallery|comments-show
		teacher:c|gallery|gallery_gallery|comments-retract|(if)|creator
		teacher:c|gallery|gallery_gallery|edit|(if)|creator
		teacher:c|gallery|gallery_gallery|images-add|(if)|creator
		teacher:c|gallery|gallery_gallery|images-remove|(if)|creator
		teacher:c|gallery|gallery_gallery|images-edit|(if)|creator
		teacher:c|gallery|gallery_gallery|delete|(if)|creator
    </defaultpermissions></module>
*/ ?>
