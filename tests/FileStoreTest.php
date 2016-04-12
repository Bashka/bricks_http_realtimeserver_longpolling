<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
use Bricks\Http\RealtimeServer\LongPolling\Store\FileStore;
require_once('Store/Store.php');
require_once('Store/ArrayStore.php');
require_once('Store/FileStore.php');
require_once('Event.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class FileStoreTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var FileStore Тестируемый объект.
	 */
	private $store;

	public static function setUpBeforeClass(){
		$file = fopen(__DIR__ . '/filestore/store.txt', 'a');
		$events = [];
		$events[0] = new Event(1);
		$events[0]->setData(1);
		$events[1] = new Event(3);
		$events[1]->setData(3);
		$events[2] = new Event(5);
		$events[2]->setData(5);

		fwrite($file, serialize($events));
		fclose($file);
	}

	public static function tearDownAfterClass(){
		if(file_exists(__DIR__ . '/filestore/store.txt')){
			unlink(__DIR__ . '/filestore/store.txt');
		}
		if(file_exists(__DIR__ . '/filestore/not_exists_file.txt')){
			unlink(__DIR__ . '/filestore/not_exists_file.txt');
		}
	}

	public function setUp(){
		$this->store = new FileStore(__DIR__ . '/filestore/store.txt');
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

	public function testGet_shouldReturnEmptyArrayIfFileNotExists(){
		$this->store = new FileStore(__DIR__ . '/filestore/not_exists_file.txt');
		$this->assertEquals([], $this->store->get(1));
	}

	public function testInit(){
		$this->store = new FileStore(__DIR__ . '/filestore/not_exists_file.txt');
		$events = [];
		$events[0] = new Event(1);
		$events[0]->setData(1);
		$events[1] = new Event(3);
		$events[1]->setData(3);
		$events[2] = new Event(5);
		$events[2]->setData(5);

		$this->store->init($events);

		$this->assertTrue(file_exists(__DIR__ . '/filestore/not_exists_file.txt'));
		$events = $this->store->get(1);
		$this->assertEquals(3, $events[0]->getBirthday());
		$this->assertEquals('3', $events[0]->getData());
		$this->assertEquals(5, $events[1]->getBirthday());
		$this->assertEquals('5', $events[1]->getData());
	}

	public function testPush(){
		$this->store = new FileStore(__DIR__ . '/filestore/not_exists_file.txt');
		$events = [];
		$events[0] = new Event(1);
		$events[0]->setData('1');
		$events[1] = new Event(3);
		$events[1]->setData('3');
		$events[2] = new Event(5);
		$events[2]->setData('5');

		$this->store->init($events);

		$event = new Event(7);
		$event->setData('7');
		$this->store->push($event);

		$events = $this->store->get(5);
		$this->assertEquals(7, $events[0]->getBirthday());
		$this->assertEquals('7', $events[0]->getData());
	}
}
