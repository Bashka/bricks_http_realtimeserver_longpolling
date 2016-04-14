<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
require_once('Store/Store.php');
require_once('tests/MockStore.php');
require_once('Server.php');

/**
 * @author Artur Sh. Mamedbekov
 */
class ServerTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var Store Используемое хранилище.
	 */
	private $store;

	/**
	 * @var Server Тестируемый объект.
	 */
	private $server;

	public function setUp(){
		$this->store = new MockStore('test');
		$this->server = new Server($this->store);
	}

	public function testConstructor_shouldSetAttemptsLimitWithMaxExecutionTime(){
		ini_set('max_execution_time', 3);
		$this->server = new Server($this->store);
		$this->assertEquals(3, $this->server->getAttemptsLimit());
	}

	public function testListen(){
		$this->store->setHistory([[], [], ['test']]);
		$this->server->setDelay(0.1);
		$this->server->setAttemptsLimit(3);

		$this->assertEquals(['test'], $this->server->listen(time()));
	}

	public function testListen_shouldDieIfAttemptsLimit(){
		$this->store->setHistory([[], [], ['test']]);
		$this->server->setDelay(0.1);
		$this->server->setAttemptsLimit(2);
		$this->assertEquals([], $this->server->listen(time()));
	}
}
