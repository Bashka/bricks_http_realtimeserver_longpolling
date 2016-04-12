<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
require_once('Event.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class EventTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var Event Тестируемый объект.
	 */
	private $event;

	public function setUp(){
		$this->event = new Event(1);
	}

	public function testGetBirthday(){
		$this->assertEquals(1, $this->event->getBirthday());
	}

	public function testGetSetData(){
		$this->event->setData('test');
		$this->assertEquals('test', $this->event->getData());
	}
}
