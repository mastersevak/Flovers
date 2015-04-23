<?php 


/**
 * Это класс, для сохранения данных модели 
 * в этом классе, для последующего доступа к данным 
 * 
 * $model->oldAttributes->propertyName или 
 * $model->oldAttributes['propertyName']
 *
 * это делается для того, чтобы если данные в модели изменились, 
 * мы могли бы узнать старое значение и сравнить с новым
 */
class IteratableArray implements IteratorAggregate, ArrayAccess{

	public $attributes;

	public function __get($name){
		return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
	}

	/**
	 * Returns all attribute values.
	 * @param array $names list of attributes whose value needs to be returned.
	 * Defaults to null, meaning all attributes as listed in {@link attributeNames} will be returned.
	 * If it is an array, only the attributes in the array will be returned.
	 * @return array attribute values (name=>value).
	 */
	public function getAttributes($names=null)
	{
		$values=array();
		foreach($this->attributeNames() as $name)
			$values[$name]=$this->$name;

		if(is_array($names))
		{
			$values2=array();
			foreach($names as $name)
				$values2[$name]=isset($values[$name]) ? $values[$name] : null;
			return $values2;
		}
		else
			return $values;
	}

	/**
	 * Returns an iterator for traversing the attributes in the model.
	 * This method is required by the interface IteratorAggregate.
	 * @return CMapIterator an iterator for traversing the items in the list.
	 */
	public function getIterator()
	{
		$attributes=$this->getAttributes();
		return new CMapIterator($attributes);
	}

	/**
	 * Returns whether there is an element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed $offset the offset to check on
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return property_exists($this,$offset);
	}

	/**
	 * Returns the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param integer $offset the offset to retrieve element.
	 * @return mixed the element at the offset, null if no element is found at the offset
	 */
	public function offsetGet($offset)
	{
		return $this->$offset;
	}

	/**
	 * Sets the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param integer $offset the offset to set element
	 * @param mixed $item the element value
	 */
	public function offsetSet($offset,$item)
	{
		$this->$offset=$item;
	}

	/**
	 * Unsets the element at the specified offset.
	 * This method is required by the interface ArrayAccess.
	 * @param mixed $offset the offset to unset element
	 */
	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}
}