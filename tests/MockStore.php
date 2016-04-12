<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
use Bricks\Http\RealtimeServer\LongPolling\Store\Store;
use Bricks\Http\RealtimeServer\LongPolling\Event;
require_once(__DIR__ . '/../Store/Store.php');
require_once(__DIR__ . '/../Event.php');

/**
 * Тестовая реализация именованного хранилища.
 *
 * @author Artur Sh. Mamedbekov
 */
class MockStore implements Store{
	private $history;
	private $p;

	/**
	 * Метод определяет историю запросов хранилища.
	 *
	 * @param array $history Массив истории запросов. Каждый следующий вызов 
	 * метода get будет возвращать очередной элемент этого массива моделируя 
	 * обращение к реальному хранилищу.
	 *
	 * @return array|null Текущие данные.
	 */
	public function setHistory(array $history){
		$this->history = $history;
		$this->p = 0;
	}

	public function get($time){
		return $this->history[$this->p++];
	}

	public function init(array $events){
	}

	public function push(Event $event){
	}
}
