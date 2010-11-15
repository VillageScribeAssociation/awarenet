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
            <name>Videos_Gallery</name>
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
            <name>Videos_Video</name>
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
		student:p|videos|Videos_Gallery|new
		student:p|videos|Videos_Gallery|show
		student:p|videos|Videos_Gallery|images-show
		student:p|videos|Videos_Gallery|comments-add
		student:p|videos|Videos_Gallery|comments-show
		student:c|videos|Videos_Gallery|edit|(if)|creator
		student:c|videos|Videos_Gallery|images-add|(if)|creator
		student:c|videos|Videos_Gallery|images-remove|(if)|creator
		student:c|videos|Videos_Gallery|images-edit|(if)|creator
		student:c|videos|Videos_Gallery|videos-add|(if)|creator
		student:c|videos|Videos_Gallery|videos-remove|(if)|creator
		student:c|videos|Videos_Gallery|videos-edit|(if)|creator
		student:c|videos|Videos_Gallery|delete|(if)|creator

		teacher:p|videos|Videos_Gallery|new
		teacher:p|videos|Videos_Gallery|show
		teacher:p|videos|Videos_Gallery|images-show
		student:p|videos|Videos_Gallery|comments-add
		student:p|videos|Videos_Gallery|comments-show
		teacher:c|videos|Videos_Gallery|edit|(if)|creator
		teacher:c|videos|Videos_Gallery|images-add|(if)|creator
		teacher:c|videos|Videos_Gallery|images-remove|(if)|creator
		teacher:c|videos|Videos_Gallery|images-edit|(if)|creator
		teacher:c|videos|Videos_Gallery|videos-add|(if)|creator
		teacher:c|videos|Videos_Gallery|videos-remove|(if)|creator
		teacher:c|videos|Videos_Gallery|videos-edit|(if)|creator
		teacher:c|videos|Videos_Gallery|delete|(if)|creator


		student:c|videos|Videos_Video|comments-add
		student:c|videos|Videos_Video|comments-retract|(if)|creator
		student:c|videos|Videos_Video|comments-show


		teacher:c|videos|Videos_Video|comments-add
		teacher:c|videos|Videos_Video|comments-retract|(if)|creator
		teacher:c|videos|Videos_Video|comments-show
    </defaultpermissions>
</module>
*/ ?>
