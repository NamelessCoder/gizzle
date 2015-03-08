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
	 * @var array
	 */
	protected $apiUrls = array();

	/**
	 * @param string $urlIdentifier
	 * @return string|NULL
	 */
	public function resolveApiUrl($urlIdentifier) {
		return TRUE === isset($this->apiUrls[$urlIdentifier]) ? $this->apiUrls[$urlIdentifier] : NULL;
	}

	/**
	 * Setter to fill each entity with API URLs which
	 * are recognised according to self::API_URL_*
	 * names in the $apiUrls array.
	 *
	 * @param array $apiUrls
	 * @return void
	 */
	public function setApiUrls(array $apiUrls) {
		$classReflection = new \ReflectionClass($this);
		$constants = $classReflection->getConstants();
		foreach ($apiUrls as $apiUrlIdentifier => $value) {
			if (TRUE === in_array($apiUrlIdentifier, $constants)) {
				unset($apiUrls[$apiUrlIdentifier]);
				$this->apiUrls[$apiUrlIdentifier] = $value;
			}
		}
		return $apiUrls;
	}

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
		$jsonData = $this->setApiUrls($jsonData);
		foreach ($jsonData as $propertyName => $propertyValue) {
			$propertyName = TRUE === isset($this->propertyMap[$propertyName]) ? $this->propertyMap[$propertyName] : $propertyName;
			$propertyClass = TRUE === isset($this->propertyClasses[$propertyName]) ? $this->propertyClasses[$propertyName] : NULL;
			if (FALSE === property_exists(get_class($this), $propertyName)) {
				continue;
			} elseif ('DateTime' === $propertyClass) {
				$propertyValue = \DateTime::createFromFormat('U', (integer) $propertyValue);
			} elseif (NULL !== $propertyClass && FALSE === strpos($propertyClass, '[]')) {
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
