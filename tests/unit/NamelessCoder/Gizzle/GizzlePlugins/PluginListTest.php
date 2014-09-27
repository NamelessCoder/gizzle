<?php
namespace NamelessCoder\Gizzle\Tests\Unit\GizzlePlugins;

use NamelessCoder\Gizzle\GizzlePlugins\PluginList;
use NamelessCoder\Gizzle\PluginListInterface;

/**
 * Class PluginListTest
 */
class PluginListTest extends \PHPUnit_Framework_TestCase {

	public function testInitializeSetsSettings() {
		$pluginList = new PluginList();
		$pluginList->initialize(array('foo' => 'bar'));
		$result = $this->getObjectAttribute($pluginList, 'settings');
		$this->assertEquals(array('foo' => 'bar'), $result);
	}

	public function testGetPluginClassNamesReturnsValidClasses() {
		$pluginList = new PluginList();
		$classes = $pluginList->getPluginClassNames();
		foreach ($classes as $class) {
			$implementsInterface = is_a($class, 'NamelessCoder\\Gizzle\\PluginInterface', TRUE);
			$this->assertTrue(class_exists($class), 'Class "' . $class . '" does not exist');
			$this->assertTrue($implementsInterface, 'Class "' . $class . '" is not a valid Gizzle plugin');
		}
	}

}
