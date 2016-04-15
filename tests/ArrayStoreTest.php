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
	private function createFullStore(array $events){
		assert('count($events) > 0');

		$store = new ArrayStore;
		$store->init($events);

		return $store;
	}

	private function createEmptyStore(){
		$store = new ArrayStore;
		$store->init([]);

		return $store;
	}


	public function testGet(){
		$store = $this->createFullStore([
			new Event(1, 1),
			new Event(3, 3),
			new Event(5, 5),
		]);

		$allEvents = $store->get(0);
		$partEvents = $store->get(1);
		$notEvents = $store->get(6);

		$this->assertEquals([
			new Event(1, 1),
			new Event(3, 3),
			new Event(5, 5),
		], $allEvents, 'Выбор при наличии только актуальных событий в хранилище');
		$this->assertEquals([
			new Event(3, 3),
			new Event(5, 5),
		], $partEvents, 'Выбор только актуальных событий в хранилище');
		$this->assertEquals([
		], $notEvents, 'Выбор при отсутствии актуальных событий в хранилище');
	}

	public function testGet_shouldReturnEmptyArrayIfNotData(){
		$store = $this->createEmptyStore();

		$events = $store->get(0);

		$this->assertEquals([
		], $events, 'Выбор при отсутствии событий в хранилище');
	}

	public function testInit(){
		$store = $this->createEmptyStore();

		$store->init([
			new Event(2, 2)
		]);
		$events = $store->get(0);

		$this->assertEquals([
			new Event(2, 2)
		], $events, 'Инициализация хранилища');
	}

	public function testPush(){
		$store = $this->createFullStore([
			new Event(1, 1),
		]);

		$store->push(new Event(2, 2));
		$events = $store->get(1);

		$this->assertEquals([
			new Event(2, 2)
		], $events, 'Добавление события в хранилище без необходимости сортировки');
	}

	public function testPush_shouldSortingStore(){
		$store = $this->createFullStore([
			new Event(1, 1),
			new Event(3, 3),
		]);

		$store->push(new Event(2, 2));
		$events = $store->get(1);

		$this->assertEquals([
			new Event(2, 2),
			new Event(3, 3),
		], $events, 'Добавление события в хранилище с необходимостью сортировки');
	}

	public function testPush_shouldPushIfStoreEmpty(){
		$store = $this->createEmptyStore();

		$store->push(new Event(1, 1));
		$events = $store->get(0);

		$this->assertEquals([
			new Event(1, 1)
		], $events, 'Добавление события в пустое хранилище');
	}
}
