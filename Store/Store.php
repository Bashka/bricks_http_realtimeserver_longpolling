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
	 * Должен инициализировать хранилище данными.
	 *
	 * @param Event[] $events Доступные в хранилище события.
	 */
	public function init(array $events);

	/**
	 * Должен возвращать события, созданные после указанной временной метки.
	 *
	 * @param int $time Временная метка, определяющая актуальность запрашиваемых 
	 * событий.
	 *
	 * @return Event[] Массив актуальных событий, или пустой массив, если событий 
	 * еще нет.
	 */
	public function get($time);

	/**
	 * Должен регистрировать новое событие.
	 *
	 * @param Event $event Регистрируемое событие.
	 */
	public function push(Event $event);
}
