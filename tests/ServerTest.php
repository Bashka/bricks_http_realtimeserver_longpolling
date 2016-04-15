<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
require_once('Store/Store.php');
require_once('tests/MockStore.php');
require_once('Server.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class ServerTest extends \PHPUnit_Framework_TestCase{
	private function createMockStore(array $history = []){
		$mockStore = new MockStore;
		$mockStore->setHistory($history);

		return $mockStore;
	}


	public function testConstructor_shouldSetAttemptsLimitWithMaxExecutionTime(){
		ini_set('max_execution_time', 3);
		$server = new Server($this->createMockStore());

		$attemptsLimit = $server->getAttemptsLimit();

		$this->assertEquals(3, $attemptsLimit, 'Формирование attemptsLimit в конструкторе на основании опции интерпретатора max_execution_time');
	}

	public function testListen(){
		$server = new Server($this->createMockStore([[], [], ['test']]));
		$server->setDelay(0.1);
		$server->setAttemptsLimit(3);

		$events = $server->listen(time());

		$this->assertEquals(['test'], $events, 'Ожидание актуальных событий');
	}

	public function testListen_shouldDieIfAttemptsLimit(){
		$server = new Server($this->createMockStore([[], [], ['test']]));
		$server->setDelay(0.1);
		$server->setAttemptsLimit(2);

		$events = $server->listen(time());

		$this->assertEquals([], $events, 'Завершение работы при достижении attemptsLimit');
	}
}
