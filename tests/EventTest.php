<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
require_once('Event.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class EventTest extends \PHPUnit_Framework_TestCase{
	public function testConstructor(){
		$event = new Event(1, 'test');

		$this->assertEquals(1, $event->getBirthday(), 'Проверка конструктора');
		$this->assertEquals('test', $event->getData(), 'Проверка конструктора');
	}

	public function testGetBirthday(){
		$event = new Event(1, 'test');

		$this->assertEquals(1, $event->getBirthday(), 'Проверка getter birthday');
	}

	public function testGetSetData(){
		$event = new Event(1, 'test');

		$event->setData('new test');
		$this->assertEquals('new test', $event->getData(), 'Проверка getter/setter data');
	}
}
