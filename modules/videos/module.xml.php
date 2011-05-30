<? /*
<?xml version="1.0" encoding="UTF-8" ?>
<module>
    <modulename>videos</modulename>
    <version>0</version>
    <revision>0</revision>
    <description>describe your module here</description>
    <core>no</core>
    <dependencies>
    </dependencies>
    <models>
        <model>
            <name>videos_gallery</name>
            <description>Container object which owns images.</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
        <model>
            <name>videos_video</name>
            <description>Represents a single uploaded flash or MP4 video.</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
                <export>videos-show</export>
                <export>videos-add</export>
                <export>videos-remove</export>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
    </models>
    <defaultpermissions>
		student:p|videos|videos_gallery|new
		student:p|videos|videos_gallery|show
		student:p|videos|videos_gallery|images-show
		student:p|videos|videos_gallery|comments-add
		student:p|videos|videos_gallery|comments-show
		student:c|videos|videos_gallery|edit|(if)|creator
		student:c|videos|videos_gallery|images-add|(if)|creator
		student:c|videos|videos_gallery|images-remove|(if)|creator
		student:c|videos|videos_gallery|images-edit|(if)|creator
		student:c|videos|videos_gallery|videos-add|(if)|creator
		student:c|videos|videos_gallery|videos-remove|(if)|creator
		student:c|videos|videos_gallery|videos-edit|(if)|creator
		student:c|videos|videos_gallery|delete|(if)|creator
		teacher:p|videos|videos_gallery|videos-show

		teacher:p|videos|videos_gallery|new
		teacher:p|videos|videos_gallery|show
		teacher:p|videos|videos_gallery|images-show
		student:p|videos|videos_gallery|comments-add
		student:p|videos|videos_gallery|comments-show
		teacher:c|videos|videos_gallery|edit|(if)|creator
		teacher:c|videos|videos_gallery|images-add|(if)|creator
		teacher:c|videos|videos_gallery|images-remove|(if)|creator
		teacher:c|videos|videos_gallery|images-edit|(if)|creator
		teacher:c|videos|videos_gallery|videos-add|(if)|creator
		teacher:p|videos|videos_gallery|videos-show
		teacher:c|videos|videos_gallery|videos-remove|(if)|creator
		teacher:c|videos|videos_gallery|videos-edit|(if)|creator
		teacher:c|videos|videos_gallery|delete|(if)|creator

		student:p|videos|videos_video|comments-add
		student:p|videos|videos_video|comments-retract|(if)|creator
		student:p|videos|videos_video|comments-show

		student:c|videos|videos_video|images-add|(if)|creator
		student:c|videos|videos_video|images-edit|(if)|creator

		teacher:c|videos|videos_video|images-add|(if)|creator
		teacher:c|videos|videos_video|images-edit|(if)|creator

		teacher:p|videos|videos_video|comments-add
		teacher:p|videos|videos_video|comments-retract|(if)|creator
		teacher:p|videos|videos_video|comments-show
    </defaultpermissions>
</module>
*/ ?>
