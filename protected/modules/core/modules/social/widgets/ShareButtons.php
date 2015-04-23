<?php

/**
* Social share buttons
*/
class ShareButtons extends CWidget
{
	public $icons = array();
	
	public function run(){
		cs()->registerCssFile($this->controller->assetsUrl.'/css/share.css', 'screen'); ?>

		<!-- AddThis Button BEGIN -->
		<div class="addthis_toolbox addthis_floating_style addthis_32x32_style">
			<ul>
				<li><a rel="nofollow" class="addthis_button_facebook"></a></li>
				<li><a rel="nofollow" class="addthis_button_google_plusone_share"></a></li>
				<li><a rel="nofollow" class="addthis_button_twitter"></a></li>
				<li><a rel="nofollow" class="addthis_button_odnoklassniki_ru"></a></li>
				<li><a rel="nofollow" class="addthis_button_vk"></a></li>
				<li><a rel="nofollow" class="addthis_button_mymailru"></a></li>
			</ul>
		</div>
		<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
		<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e40ed5c6ad2c718"></script>
		<!-- AddThis Button END -->

		<?
	}
}

