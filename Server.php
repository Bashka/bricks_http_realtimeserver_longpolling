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
	const DEFAULT_DELAY = 1000000;

	/**
	 * @var Store Используемое хранилище.
	 */
	private $store;

	/**
	 * @var float Время задержки между запросами к хранилищу в секундах.
	 */
	private $delay;

	/**
	 * @var int Максимальное число попыток получения данных из хранилища.
	 */
	private $attemptsLimit;

	/**
	 * @param Store $store Используемое хранилище.
	 */
	public function __construct(Store $store){
		$this->store = $store;
		$this->setDelay(self::DEFAULT_DELAY);
		$this->setAttemptsLimit((int) ((int) ini_get('max_execution_time') * 1000000 / $this->delay));
	}

	/**
	 * Устанавливает время задержки между запросами к хранилищу.
	 *
	 * @param int $delay Время задержки в микросекундах (1 секунда = 1000000).
	 */
	public function setDelay($delay){
		$this->delay = $delay;
	}

	/**
	 * Устанавливает максимальное число попыток получения данных из хранилища.
	 * Максимальное время работы сервера будет равно произведению этого числа на 
	 * время задержки.
	 *
	 * @param int $attemptsLimit Максимальное число попыток получения данных из 
	 * хранилища.
	 */
	public function setAttemptsLimit($attemptsLimit){
		$this->attemptsLimit = $attemptsLimit;
	}

	/**
	 * @return int Максимальное число попыток получния данных от хранилища.
	 */
	public function getAttemptsLimit(){
		return $this->attemptsLimit;
	}

	/**
	 * Пытается получить очередные данные из хранилища, время создания которых 
	 * больше указанного. Метод не возвращает управление пока не получит данные.
	 *
	 * @param int $time Временная метка, определяющая время актуальности данных.	
	 * Все данные, созданные после данной метки считаются актуальными и будут 
	 * включены в ответ.
	 * @param callable|array $callback [optional] Функция или массив вида [объект, 
	 * имя_метода], которая будет вызываться после каждого обращения к хранилищу с 
	 * целью получения новых событий.
	 *
	 * @return Event[] Массив событий, актуальных для данной временной метки.
	 */
	public function listen($time, $callback = null){
		$i = 1;
		while(empty($data = $this->store->get($time))){
			if($i++ == $this->attemptsLimit){
				break;
			}
			if(!is_null($callback)){
				call_user_func($callback);
			}
			usleep($this->delay);
		}

		return $data;
	}
}
