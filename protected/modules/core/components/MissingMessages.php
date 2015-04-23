<?php
/**
 * Handle onMissingTranslation event
 */
class MissingMessages extends CApplicationComponent
{
	/**
	 * Add missing translations to the source table and 
	 * If we are using a different translation then the original one
	 * Then add the same message to the translation table.
	 */
	public static function toTable($event)
	{
		if(app()->controller->isFront){
			// Load the messages	
			$criteria = new SDbCriteria;
			$condition 	= "message = '".$event->message."' and ";
			$condition .= "category = '".$event->category."' and ";
			$condition .= "language = '".$event->language."'"	;
			$criteria->condition = $condition;

			$source = MissingTranslation::model()->find($criteria);
			
			// If we didn't find one then add it
			if( !$source )
			{
				// Add it
				$model = new MissingTranslation;
				
				$model->category = $event->category;
				$model->language = $event->language;
				$model->message = $event->message;
				$model->page = request()->pathInfo;
				$model->save();
				
				//$lastID = Yii::app()->db->lastInsertID;
			}
			
		}
		
	}

	/**
	 * Output missing translations to the log screen
	 */
	public static function toOutput ($event) {
 

		// event class for this event is CMissingTranslationEvent
		// so we can get some info about the message
		$text = implode("\n", array(
			'Missing Translation:',
			'---------------------',
			'Language: '.$event->language,
			'Category:'.$event->category,
			'Message:'.$event->message
		));
		lg($text, 'translation');
		// sending email
		//mail('admin@example.com', 'Missing translation', $text);
	}
}