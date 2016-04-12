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

	public function testListen(){
		$this->store->setHistory([[], [], ['test']]);
		$this->server->setDelay(0.1);

		$this->assertEquals(['test'], $this->server->listen(time()));
	}
}
