<?php
namespace Bricks\Http\RealtimeServer\LongPolling\Store;
use Bricks\Http\RealtimeServer\LongPolling\Event;

/**
 * Реализация хранилища в массиве.
 * Данная реализация не является перманентной.
 *
 * @author Artur Sh. Mamedbekov
 */
class ArrayStore implements Store{
	/**
	 * @var array Массив, выступающий в качестве хранилища.
	 */
	protected $store;

	public function __construct(){
		$this->store = [];
	}

	/**
	 * @see Bricks\Http\RealtimeServer\LongPolling\Store\Store::init
	 */
	public function init(array $events){
		$this->store = $events;
	}

	/**
	 * @see Bricks\Http\RealtimeServer\LongPolling\Store\Store::get
	 */
	public function get($time){
		$result = [];
		end($this->store);
		while(true){
			$current = current($this->store);
			if($current === false){
				break;
			}
			if($current->getBirthday() <= $time){
				break;
			}
			array_unshift($result, $current);
			if(prev($this->store) === false){
				break;
			}
		}

		return $result;
	}

	/**
	 * @see Bricks\Http\RealtimeServer\LongPolling\Store\Store::push
	 */
	public function push(Event $event){
		$pos = count($this->store) - 1;
		if($pos != -1){
			while($this->store[$pos]->getBirthday() > $event->getBirthday() && $pos != 0){
				$pos--;
			}
			$this->store = array_merge(array_slice($this->store, 0, $pos + 1), [$event], array_slice($this->store, $pos + 1));
		}
		else{
			$this->store[] = $event;
		}
	}
}
