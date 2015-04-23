<?php

class m150325_132136_insert_send_statuses_in_lookup_table extends EDbMigration
{
	public function up()
	{
		$sendStatus = ['0' => 'Новый', '1' => 'Отправлен', '2' => 'Ошибка', '3' => 'В очереди']; 
		
		$lastPos = Yii::app()->db->createCommand()->select("MAX(pos)")->from('lookup')->queryScalar() + 1;

		foreach ($sendStatus as $code => $status) {
			$this->insert('{{lookup}}', array('type' => 'SendStatus', 'code' => $code, 'pos' => $lastPos));
			$lastId = Yii::app()->db->createCommand()->select("MAX(id)")->from('{{lookup}}')->queryScalar();
			foreach(param('languages') as $key => $value){
				$this->insert('{{lookup_lang}}', array(
					'id_lookup' => $lastId,
					'language' => $key,
					'name' => $status,
				));
			}

			$lastPos++;
		}
	}

	public function down()
	{
		$type = 'SendStatus';
		$codes = ['0', '1', '2', '3'];
		foreach ($codes as $code) {
			$id = Yii::app()->db->createCommand('Select id from {{lookup}} where type = "'.$type.'" and code = "'.$code.'"')->queryScalar();
			$this->delete('{{lookup}}', 'id = :id', [':id' => $id]);
			$this->delete('{{lookup_lang}}', 'id_lookup = :id', [':id' => $id]);
		}
	}
}