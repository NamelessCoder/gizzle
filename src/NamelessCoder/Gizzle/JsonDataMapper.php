<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

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
	 * @param mixed $jsonData
	 */
	public function __construct($jsonData = array()) {
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
			if (FALSE === property_exists(get_class($this), $propertyName)) {
				continue;
			} elseif (NULL === $propertyClass) {
				$propertyValue = $propertyValue;
			} elseif ('DateTime' === $propertyClass) {
				$propertyValue = \DateTime::createFromFormat('U', $propertyValue);
			} elseif (FALSE === strpos($propertyClass, '[]')) {
				$propertyValue = new $propertyClass($propertyValue);
			} elseif (NULL !== $propertyClass) {
				$className = substr($propertyClass, 0, -2);
				$values = array();
				foreach ($propertyValue as $childObjectData) {
					$values[] = new $className($childObjectData);
				}
				$propertyValue = $values;
			}
			$this->$propertyName = $propertyValue;
		}
	}

}
