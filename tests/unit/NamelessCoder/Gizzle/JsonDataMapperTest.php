<?php
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
