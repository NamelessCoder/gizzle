<?php
namespace NamelessCoder\Gizzle;

/**
 * Class JsonDataMapper
 */
abstract class JsonDataMapper {

	/**
	 * @var array
	 */
	protected $propertyMap = array();

	/**
	 * @var array
	 */
	protected $propertyClasses = array();

	/**
	 * @param string $jsonData
	 */
	public function __construct($jsonData) {
		$this->map($jsonData);
	}

	/**
	 * @param mixed $jsonData
	 * @return void
	 */
	public function map($jsonData) {
		if (FALSE === is_array($jsonData)) {
			$jsonData = json_decode($jsonData, JSON_OBJECT_AS_ARRAY);
		}
		if (FALSE === is_array($jsonData)) {
			throw new \RuntimeException('Invalid JSON data received by ' . get_class($this), 1411216651);
		}
		foreach ($jsonData as $propertyName => $propertyValue) {
			$propertyName = TRUE === isset($this->propertyMap[$propertyName]) ? $this->propertyMap[$propertyName] : $propertyName;
			$propertyClass = TRUE === isset($this->propertyClasses[$propertyName]) ? $this->propertyClasses[$propertyName] : NULL;
			if (FALSE === isset($this->$propertyName)) {
				continue;
			} elseif (NULL === $propertyClass) {
				$this->$propertyName = $propertyValue;
			} elseif ('DateTime' === $propertyClass) {
				$this->$propertyName = new \DateTime($propertyValue);
			} elseif (FALSE === strpos($propertyClass, '[]')) {
				$this->$propertyName = new $propertyClass($propertyValue);
			} else {
				$className = substr($propertyClass, 0, -2);
				$values = array();
				foreach ($propertyValue as $childObjectData) {
					$values[] = new $className($childObjectData);
				}
				$this->$propertyName = $values;
			}
		}
	}

}
