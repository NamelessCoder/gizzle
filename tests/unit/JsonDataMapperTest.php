<?php

/**
 * This file belongs to the NamelessCoder/Gizzle package
 *
 * Copyright (c) 2014, Claus Due
 *
 * Released under the MIT license, of which the full text
 * was distributed with this package in file LICENSE.txt
 */

namespace NamelessCoder\Gizzle\Tests\Unit;

/**
 * Class JsonDataMapperTest
 */
class JsonDataMapperTest extends \PHPUnit_Framework_TestCase {

	public function testThrowsExceptionOnInvalidDataType() {
		$this->setExpectedException('RuntimeException', '', 1411216651);
		$mapper = $this->getMockForAbstractClass('NamelessCoder\\Gizzle\\JsonDataMapper', array(123, ''), '', TRUE);
	}

}
