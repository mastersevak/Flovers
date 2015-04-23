<?php 


class SForm extends CForm
{


	//переопределил функцию, чтобы поменять класс row, на control-group
	public function renderElement($element)
	{
		if(is_string($element))
		{
			if(($e=$this[$element])===null && ($e=$this->getButtons()->itemAt($element))===null)
				return $element;
			else
				$element=$e;
		}
		if($element->getVisible())
		{
			if($element instanceof CFormInputElement)
			{
				//меняем layout
				$element->layout = "{label}\n<span class='field'>\n{input}\n{hint}\n{error}\n</span>";

				if($element->type==='hidden')
					return "<div style=\"visibility:hidden\">\n".$element->render()."</div>\n";
				else
					return "<div class=\"control-group clearfix field_{$element->name}\">\n".$element->render()."</div>\n";
			}
			elseif($element instanceof CFormButtonElement)
				return $element->render()."\n";
			else
				return $element->render();
		}
		return '';
	}
	

	public function renderButtons(){
		$buttons = $this->getButtons();

		if(count($buttons) > 0){
			$params = [];
			foreach($buttons as $key => $button){
				$params[$key] = !empty($button->attributes) ? $button->attributes : $button->content;
			}

			ob_start();
			Yii::app()->controller->widget('UIButtons', $params);
			$buttons = ob_get_contents();
			ob_end_clean();

			return "<div class='buttons mt20'>{$buttons}</div>";
		}
		
		
	}
}
