<? /*
<module>
    <modulename>moblog</modulename>
    <version>1.0</version>
    <revision>For producing the moblog (user blogs agregated by school, group, etc).</revision>
    <description>For producing the moblog (user blogs agregated by school, group, etc).</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <models>
      <model>
        <name>moblog_post</name>
		<description>Individual blog posts.</description>
        <permissions>
          <permission>show</permission>
          <permission>list</permission>
          <permission>edit</permission>
          <permission>new</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		public:p|moblog|moblog_post|show

		student:p|moblog|moblog_post|new
		student:p|moblog|moblog_post|show
		student:p|moblog|moblog_post|list
		student:p|moblog|moblog_post|comments-add
		student:p|moblog|moblog_post|comments-show
		student:p|moblog|moblog_post|images-show
		student:c|moblog|moblog_post|edit|(if)|creator
		student:c|moblog|moblog_post|images-add|(if)|creator
		student:c|moblog|moblog_post|images-remove|(if)|creator
		student:c|moblog|moblog_post|images-edit|(if)|creator
		student:c|moblog|moblog_post|tags-manage|(if)|creator
		student:c|moblog|moblog_post|delete|(if)|creator

		teacher:p|moblog|moblog_post|new
		teacher:p|moblog|moblog_post|show
		teacher:p|moblog|moblog_post|list
		teacher:p|moblog|moblog_post|comments-add
		teacher:p|moblog|moblog_post|comments-show
		teacher:p|moblog|moblog_post|images-show
		teacher:c|moblog|moblog_post|edit|(if)|creator
		teacher:c|moblog|moblog_post|images-add|(if)|creator
		teacher:c|moblog|moblog_post|images-remove|(if)|creator
		teacher:c|moblog|moblog_post|images-edit|(if)|creator
		teacher:c|moblog|moblog_post|tags-manage|(if)|creator
		teacher:c|moblog|moblog_post|delete|(if)|creator
    </defaultpermissions>
</module>
*/ ?>
