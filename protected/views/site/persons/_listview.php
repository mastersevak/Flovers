<?$url = $this->createUrl('site/person/',['id'=>$data->id]);?>
<? if($data->products) : ?>
<li>
	<div class="left">
		<div class="img"><?=CHtml::link($data->getThumbnail('custom', 228, 228), $url)?></div>
		
		<div class="bottom">
			<div>
				<a href="<?=$url?>" class="title"><?=Person::listData()[$data->id]; ?></a>
				<p>
					<?
					$address = [];
					if($data->profile->id_job && isset(Job::listdata()[$data->profile->id_job])) 
						$address[] = Job::listdata()[$data->profile->id_job];

					if($data->profile->address)
						$address[] = $data->profile->address;

					echo $address ? implode(', ', $address) : '';
					?>
				</p>
			</div>
			<div class="more">
				<p><?=$data->countproducts?> works</p>
			</div>
		</div>
	</div>

	<div class="right">
		<? 	foreach ($data->products as $index => $product){
				if($index < 5){
					echo CHtml::link($product->getMainPhoto('thumb', 44, 44).
								CHtml::tag('div', ['class' => 'big-img'], $product->getMainPhoto('custom', 228, 228)),
								['site/item', 'id'=>$product->id]);
				}
			}
		?>
	</div>
</li>
<? endif ?>