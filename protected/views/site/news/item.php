<?//=$this->renderPartial('_breadcrumbs');?>
<?//$this->renderPartial('products/_list', compact('modelProvider'))?>
<section class="container">
	<div class="row">

		<section class="col-sm-8 col-md-8 col-lg-9">

			<div class="blog-post container-paper">
				<div class="title">
					<h2><?=CHtml::link($model->title, $this->createUrl('site/news', ['id' => $model->id]))?></h2>
				</div>

				<div class="post-container">
					<?=$model->getThumbnail('big', false, false, false, ['class' => 'full img-responsive animate scale'])?>
					<div class="row">

						<div class="col-l col-md-6 col-lg-4">
							<ul class="list-info">
								<li>
									<?if($model->date):?>
										<span class="icon icon-clock-3"></span>
										<?=t('front', 'Опубликовано')?>
										<?=app()->format->date($model->date, 'j M Y')?>
									<?endif?>
								</li>
								<li>
									<span class="icon icon-eye"></span>
									<?=($model->visits == null)? 0 : $model->visits?><?=t('front', 'Посещение')?>
								</li>
							</ul>
						</div>

						<div class="col-r col-md-6 col-lg-8">
							<p><?=$model->content?></p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<aside class="col-sm-4 col-md-4 col-lg-3">
			<section class="posts-widget container-widget">

				<h3><?=t('front', 'Последние новости')?></h3>

				<ul>
					<?$models = DynamicList::getItems('poslednie-novosti', 'News');
					$models = $models ? $models->data : [];
					foreach ($models as $id => $model):?>
						<li>
							<!-- image -->
							<?=CHtml::link($model->getThumbnail('thumb', 77, 77), '#', ['class'=> 'img'])?>
							<!-- title -->
							<?=CHtml::link($model->title, $this->createUrl('site/news', ['id' => $model->id]), ['class'=>'title'])?>

							<span class="date">
								<?=app()->format->date($model->date, 'j M Y')?>
							</span>
						</li>
					<?endforeach?>
				</ul>
			</section>
		</aside>
	</div>
</section>