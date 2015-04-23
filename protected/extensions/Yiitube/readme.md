$this->widget('ext.Yiitube', array('v' => 'fYa0y4ETFVo'));

You can set up to 3 parameters in this widget:

player: is the player you want to use. Possible values are: youtube, megavideo, vimeo, veoh. Defaults to youtube.

v: is the video code on the player. You can either put just the video code or the whole url. The widget will take care of parsing the entire string looking for the video code.

hd: is a boolean value. This parameter is ignored by every player but youtube. With this value you can decide if you want youtube to display the video in high definition or not. Defaults to false.

size: you can decide the size of your video thanks to this parameter. Size supports 4 different values: small, normal, big, huge. Defaults to normal.