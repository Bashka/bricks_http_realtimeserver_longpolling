<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
use Bricks\Http\RealtimeServer\LongPolling\Store\ArrayStore;
require_once('Store/Store.php');
require_once('Store/ArrayStore.php');
require_once('Event.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class ArrayStoreTest extends \PHPUnit_Framework_TestCase{
  /**
	 * @var ArrayStore Тестируемый объект.
	 */
	private $store;

	public function setUp(){
		$this->store = new ArrayStore;
		$events = [];
		$events[0] = new Event(1);
		$events[0]->setData(1);
		$events[1] = new Event(3);
		$events[1]->setData(3);
		$events[2] = new Event(5);
		$events[2]->setData(5);
		$this->store->init($events);
  }

	public function testGet(){
		$events = $this->store->get(0);
		$this->assertEquals(1, $events[0]->getBirthday());
		$this->assertEquals('1', $events[0]->getData());
		$this->assertEquals(3, $events[1]->getBirthday());
		$this->assertEquals('3', $events[1]->getData());
		$this->assertEquals(5, $events[2]->getBirthday());
		$this->assertEquals('5', $events[2]->getData());

		$events = $this->store->get(1);
		$this->assertEquals(3, $events[0]->getBirthday());
		$this->assertEquals('3', $events[0]->getData());
		$this->assertEquals(5, $events[1]->getBirthday());
		$this->assertEquals('5', $events[1]->getData());
	}

	public function testGet_shouldReturnEmptyArrayIfNewEventsNotFound(){
		$this->assertEquals([], $this->store->get(6));
	}

	public function testGet_shouldReturnEmptyArrayIfNotData(){
		$this->store = new ArrayStore;
		$this->assertEquals([], $this->store->get(0));
	}

	public function testInit(){
		$this->store = new ArrayStore;
		$events = [];
		$events[0] = new Event(2);
		$events[0]->setData(2);
		$this->store->init($events);

		$events = $this->store->get(1);
		$this->assertEquals(2, $events[0]->getBirthday());
		$this->assertEquals('2', $events[0]->getData());
	}

	public function testPush(){
		$event = new Event(7);
		$event->setData('7');
		$this->store->push($event);

		$events = $this->store->get(5);
		$this->assertEquals(7, $events[0]->getBirthday());
		$this->assertEquals('7', $events[0]->getData());
	}
}
