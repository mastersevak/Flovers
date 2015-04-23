<?$this->beginContent('//layouts/prepare')?>
	
	<div id="popup-login">
		<div class="overlay"></div>
		<?$this->renderPartial('core.modules.user.views.back.login', 
			['model' => new User('blogin'), 'action' => url('/core/user/back/ajaxlogin')])?>
	</div>

	<?$this->renderPartial('//layouts/parts/_header')?>
	
		<div class="page-content">

			<div class="page-header clearfix">
				<?$this->renderPartial('//layouts/parts/_breadcrumbs')?>
				<div class="page-title">
					<?if($this->pageTitle) : ?>
						<h3><?=Yii::app()->format->custom('normal<semibold>', $this->pageTitle)?></h3>
					<?endif ?>

					<?if($this->pageDesc):?>
					 	<p class="mb20"><?=$this->pageDesc?></p>
					<?endif?>
				</div>

				<?=$this->filters?>
			</div>

			<div class="content">
			
				<?=$content?>

			</div>
		</div>

<?$this->endContent()?>

<a href="#" class="scrollup"></a>
