<?php
namespace NamelessCoder\Gizzle\Tests\Unit;

use NamelessCoder\Gizzle\Branch;
use NamelessCoder\Gizzle\Commit;
use NamelessCoder\Gizzle\Entity;
use NamelessCoder\Gizzle\Payload;
use NamelessCoder\Gizzle\Repository;
use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\ErrorPlugin;
use NamelessCoder\Gizzle\Tests\Fixtures\GizzlePlugins\Plugin;

/**
 * Class PayloadTest
 */
class PayloadTest extends \PHPUnit_Framework_TestCase {

	public function testConstructorAcceptsFixtureJson() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array($data, ''));
	}

	public function testReadSignatureHeader() {
		// test: execution *without* this mocked request header will fail
		$_SERVER['HTTP_X_HUB_SIGNATURE'] = 'sha1=' . hash_hmac('sha1', '{}', '');
		$instance = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('isCommandLine'), array(), '', FALSE);
		$instance->expects($this->once())->method('isCommandLine')->will($this->returnValue(FALSE));
		$instance->__construct('{}', '');
	}

	public function testLoadPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate', 'loadPluginsFromPackage'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array()));
		$payload->loadPlugins('NamelessCoder\\Gizzle');
	}

	public function testLoadPluginsSupportsArray() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate', 'loadPluginsFromPackage'), array('{}', ''));
		$payload->expects($this->exactly(2))->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array()));
		$payload->loadPlugins(array('NamelessCoder\\Gizzle', 'NamelessCoder\\Gizzle'));
	}

	public function testLoadPluginsLoadsExpectedPlugins() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array('{}', ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle\\Tests\\Fixtures');
		$this->assertAttributeEquals(array(new Plugin()), 'plugins', $payload);
	}

	public function testLoadsAndMapsPayloadData() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array($data, ''));
		$payload->loadPlugins('NamelessCoder\\Gizzle\\Tests\\Fixtures');
		$result = $payload->process();
		$this->assertInstanceOf('NamelessCoder\\Gizzle\\Response', $result);
		$this->assertEquals(0, $result->getCode());
	}

	public function testProcessLoadsSettingsForConfiguredPackagesIfPluginListEmpty() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadSettings', 'loadPlugins'), array($data, $secret), '', FALSE);
		$payload->expects($this->once())->method('loadSettings')->will($this->returnValue(array('foo' => 'bar')));
		$payload->expects($this->once())->method('loadPlugins')->with(array('foo'))->will($this->returnValue(array()));
		$payload->process();
	}

	public function testValidate() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$hash = hash_hmac('sha1', $data, $secret);
		$payload = $this->getMock(
			'NamelessCoder\\Gizzle\\Payload',
			array('readSignatureHeader', 'isCommandLine'),
			array($data, $secret)
		);
		$payload->expects($this->once())->method('isCommandLine')->will($this->returnValue(FALSE));
		$payload->expects($this->once())->method('readSignatureHeader')->will($this->returnValue('sha1=' . $hash));
		$payload->__construct($data, $secret);
	}

	public function testValidateThrowsRuntimeExceptionOnHashMismatch() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$hash = hash_hmac('sha1', $data . 'appendforinvalidchecksum', $secret);
		$payload = $this->getMock(
			'NamelessCoder\\Gizzle\\Payload',
			array('readSignatureHeader', 'isCommandLine'),
			array($data, $secret),
			'',
			FALSE
		);
		$payload->expects($this->once())->method('isCommandLine')->will($this->returnValue(FALSE));
		$payload->expects($this->once())->method('readSignatureHeader')->will($this->returnValue('sha1=' . $hash));
		$this->setExpectedException('RuntimeException', '', 1411225210);
		$payload->__construct($data, $secret);
	}

	public function testReadSignatureHeaderReadsFromServerVariable() {
		$data = file_get_contents('tests/fixtures/sample-payload.json');
		$secret = 'dummysecret';
		$hash = hash_hmac('sha1', $data, $secret);
		$_SERVER['HTTP_X_HUB_SIGNATURE'] = 'sha1=' . $hash;
		$payload = new Payload($data, $secret);
		unset($_SERVER['HTTP_X_HUB_SIGNATURE']);
	}

	public function testResponseContainsErrorCodeAndErrorsWhenPluginsCauseErrors() {
		$errorPlugin = new ErrorPlugin();
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$payload->expects($this->once())->method('loadPluginsFromPackage')
			->with('NamelessCoder\\Gizzle')
			->will($this->returnValue(array($errorPlugin)));
		$payload->loadPlugins('NamelessCoder\\Gizzle');
		$result = $payload->process();
		$this->assertInstanceOf('NamelessCoder\\Gizzle\\Response', $result);
		$this->assertEquals(1, $result->getCode());
		$errors = $result->getErrors();
		$error = $errors[0];
		$this->assertInstanceOf('RuntimeException', $error);
		$this->assertEquals(1411238763, $error->getCode());
	}

	public function testHasResponseAfterProcessing() {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('validate'), array('{}', ''));
		$response = $payload->process();
		$result = $payload->getResponse();
		$this->assertEquals($response, $result);
	}

	/**
	 * @dataProvider getPropertyValueDataSets
	 * @param string $property
	 * @param mixed $value
	 */
	public function testGetterAndSetter($property, $value) {
		$payload = $this->getMock('NamelessCoder\\Gizzle\\Payload', array('loadPluginsFromPackage', 'validate'), array('{}', ''));
		$getter = 'get' . ucfirst($property);
		$setter = 'set' . ucfirst($property);
		$payload->$setter($value);
		$this->assertEquals($value, $payload->$getter());
	}

	/**
	 * @return array
	 */
	public function getPropertyValueDataSets() {
		return array(
			array('branches', array(new Branch(), new Branch())),
			array('parent', uniqid()),
			array('child', uniqid()),
			array('commits', array(new Commit(), new Commit())),
			array('comparisonUrl', uniqid()),
			array('context', uniqid()),
			array('created', TRUE),
			array('deleted', TRUE),
			array('forced', TRUE),
			array('head', new Commit()),
			array('sender', new Entity()),
			array('organization', new Entity()),
			array('ref', uniqid()),
			array('repository', new Repository()),
		);
	}

}
