<?php
namespace Bricks\Http\RealtimeServer\LongPolling;
use Bricks\Http\RealtimeServer\LongPolling\Store\Store;

/**
 * Сервер, реализующий Long Polling соединение.
 *
 * @author Artur Sh. Mamedbekov
 */
class Server{
	/**
	 * Время задержки между запросами к хранилищу, используемое по умолчанию.
	 */
	const DEFAULT_DELAY = 1;

	/**
	 * @var Store Используемое хранилище.
	 */
	private $store;
	/**
	 * @var float Время задержки между запросами к хранилищу в секундах.
	 */
	private $delay;

	/**
	 * @param Store $store Используемое хранилище.
	 */
	public function __construct(Store $store){
		$this->store = $store;
		$this->setDelay(self::DEFAULT_DELAY);
	}

	/**
	 * Устанавливает время задержки между запросами к хранилищу.
	 *
	 * @param float $delay Время задержки в секундах.
	 */
	public function setDelay($delay){
		$this->delay = $delay;
	}

	/**
	 * Пытается получить очередные данные из хранилища, время создания которых 
	 * больше указанного. Метод не возвращает управление пока не получит данные.
	 *
	 * @param int $time Временная метка, определяющая время актуальности данных.	
	 * Все данные, созданные после данной метки считаются актуальными и будут 
	 * включены в ответ.
	 *
	 * @return Event[] Массив событий, актуальных для данной временной метки.
	 */
	public function listen($time){
		while(empty($data = $this->store->get($time))){
			sleep($this->delay);
		}

		return $data;
	}
}
