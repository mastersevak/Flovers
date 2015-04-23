<?php 
$basePathLength = strlen(Yii::app()->basePath);

if( isset($items['controllers']) && $items['controllers']!==array() ): ?>
	<table>
	
	<?php foreach( $items['controllers'] as $controllerName => $item ): ?>
		<?php if( isset($item['actions'])===true && $item['actions']!==array() ): 

				$controllerParts = explode('.', $controllerName);
				array_walk($controllerParts, function(&$item, $key){$item = ucfirst($item);});

				$controllerName = implode('.', $controllerParts);
				?>
			<?php $controllerName = isset($moduleName) ? ucfirst($moduleName).'.'.$controllerName : $controllerName; ?>
			<?php $controllerExists = isset($existingItems[ $controllerName.'.*' ]); ?>

			<thead>
				<tr class="controller-row <?php echo $controllerExists===true ? 'exists' : ''; ?>">
					<th class="checkbox-column checkbox">
						<?php if($controllerExists===false):?>
							<?=$form->checkBox($model, 'items['.$controllerName.'.*]');?>
							<label class="mb15 mr0" for="GenerateForm_items_<?=$controllerName?>.*"></label>
						<?endif?>
					</th>
					<th class="name-column"><?php echo $controllerName.'.*'; ?></th>
					<th class="path-column"><?php echo substr($item['path'], $basePathLength+1); ?></th>
				</tr>
			</thead>

			<tbody>
			<?php $i=0; foreach( $item['actions'] as $action ): ?>
				<?php $actionKey = $controllerName.'.'.ucfirst($action['name']); ?>
				<?php $actionExists = isset($existingItems[ $actionKey ]); ?>

				<tr class="action-row<?php echo $actionExists===true ? ' exists' : ''; ?><?php echo ($i++ % 2)===0 ? ' odd' : ' even'; ?>">
					<td class="checkbox-column checkbox">
						<?if($actionExists===false):?>
							<?=$form->checkBox($model, 'items['.$actionKey.']');?>
							<label class="mb15 mr0" for="GenerateForm_items_<?=$actionKey?>"></label>
						<?endif?>
					</td>
					<td class="name-column"><?php echo $action['name']; ?></td>
					<td class="path-column"><?php echo substr($item['path'], $basePathLength+1).(isset($action['line'])===true?':'.$action['line']:''); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		<?php endif; ?>
	<?php endforeach; ?>

	</table>
<?php else: ?>
	<div class="no-items-row"><?php echo Rights::t('core', 'No actions found.'); ?></div>
<?php endif; ?>

<?php if($showModules && isset($items['modules']) && $items['modules']!==array() ): ?>
	<ul class="<?if(isset($moduleName)) echo 'submodules'; ?>">
	<?php foreach( $items['modules'] as $_moduleName => $moduleItems ): ?>
		<li>
		<p class="submodule-title"><?php echo ucfirst($_moduleName).'Module'; ?></p>
		
		<?php $this->renderPartial('_generateItems', array(
			'model'=>$model,
			'form'=>$form,
			'items'=>$moduleItems,
			'showModules' => $showModules,
			'existingItems'=>$existingItems,
			'moduleName'=> (isset($moduleName) ? $moduleName . "." : "") . ucfirst($_moduleName),
		)); ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>