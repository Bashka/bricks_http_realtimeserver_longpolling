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
	private function createFullStore(array $events){
		assert('count($events) > 0');

		$file = fopen(__DIR__ . '/filestore/full_store.txt', 'a');
		fwrite($file, serialize($events));
		fclose($file);

		return new FileStore(__DIR__ . '/filestore/full_store.txt');
	}

	private function createEmptyStore(){
		return new FileStore(__DIR__ . '/filestore/empty_store.txt');
	}

	public function tearDown(){
		if(file_exists(__DIR__ . '/filestore/full_store.txt')){
			unlink(__DIR__ . '/filestore/full_store.txt');
		}
		if(file_exists(__DIR__ . '/filestore/empty_store.txt')){
			unlink(__DIR__ . '/filestore/empty_store.txt');
		}
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

	public function testGet_shouldReturnEmptyArrayIfFileNotExists(){
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

		$this->assertTrue(file_exists(__DIR__ . '/filestore/empty_store.txt'), 'Создание файла хранилища при инициализации');
		$this->assertEquals([
			new Event(2, 2)
		], $events, 'Инициализация хранилища');
	}

	public function testInit_shouldRewriteFileStore(){
		$store = $this->createFullStore([
			new Event(1, 1),
			new Event(3, 3),
			new Event(5, 5),
		]);

		$store->init([
			new Event(2, 2)
		]);
		$events = $store->get(0);

		$this->assertTrue(file_exists(__DIR__ . '/filestore/full_store.txt'), 'Перезапись файла хранилища при инициализации');
		$this->assertEquals([
			new Event(2, 2)
		], $events, 'Переинициализация хранилища');
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
