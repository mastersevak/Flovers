<?$url = $this->createUrl('site/news', ['slug' => $data->slug]);?>
<li>
<div class="col-sm-6 col-md-4 post category4">
	<div class="container-paper-table">

		<div class="title">
			<h2>
				<?=CHtml::link($data->title, $this->createUrl('site/news', ['slug' => $data->slug]))?>
			</h2>
		</div>

		<div class="post-container">

			<?=CHtml::link($data->getThumbnail('small', false, false, false, ['class' => 'img-responsive animate scale animated']), $url)?>

			<div class="text">

				<ul class="list-info">
					<li>
						<?if($data->date):?>
							<span class="icon icon-clock-3"></span>
							<?=app()->format->date($data->date, 'j M Y')?>
						<?endif?>
					</li>
				</ul>

				<div class="divider-sm clearfix"></div>

				<p><?=substr($data->content, 0, 400)?></p>

				<?=CHtml::link(t('front', 'Читать далее') .'  <span class="icon icon-arrow-right-5"></span>',
					$this->createUrl('site/news', ['slug' => $data->slug]),['class'=>'btn btn-mega'])?>

				<div class="divider-sm"></div>

				<ul class="list-info">
					<li>
						<span class="icon icon-eye"></span>
						<?=($data->visits == null)? 0 : $data->visits?>
					</li>
				</ul>

				<div class="divider-xs clearfix"></div>
			</div>
		</div>
	</div>
</div>
</li>
