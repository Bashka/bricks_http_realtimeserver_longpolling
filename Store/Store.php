<?php
namespace Bricks\Http\RealtimeServer\LongPolling\Store;
use Bricks\Http\RealtimeServer\LongPolling\Event;

/**
 * Интерфейс, определяющий хранилище.
 *
 * @author Artur Sh. Mamedbekov
 */
interface Store{
	/**
	 * Инициализирует хранилище начальной коллекцией событий.
	 * Метод ожидает получить отсортированную по свойству birthday коллекцию 
	 * событий.
	 *
	 * @param Event[] $events Доступные в хранилище события.
	 */
	public function init(array $events);

	/**
	 * Возвращает события, созданные после указанной временной метки.
	 *
	 * @param int $time Временная метка, определяющая актуальность запрашиваемых 
	 * событий.
	 *
	 * @return Event[] Массив актуальных событий, или пустой массив, если событий 
	 * еще нет.
	 */
	public function get($time);

	/**
	 * Регистрирует новое событие.
	 * Предварительно инициализирует хранилище пустым массивом, если это не было 
	 * сделано к моменту вызова.
	 *
	 * @param Event $event Регистрируемое событие.
	 */
	public function push(Event $event);
}
