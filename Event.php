<?php
namespace Bricks\Http\RealtimeServer\LongPolling;

/**
 * Регистрируемые события.
 *
 * @author Artur Sh. Mamedbekov
 */
class Event{
	/**
	 * @var int Временная метка возникновения события.
	 */
	private $birthday;
	/**
	 * @var mixed Данные события.
	 */
	private $data;

	/**
	 * @param int $birthday Временная метка возникновения события.
	 */
	public function __construct($birthday){
		$this->birthday = $birthday;
	}

	/**
	 * @return int Временная метка возникновения события.
	 */
	public function getBirthday(){
		return $this->birthday;
	}

	/**
	 * Устанавливает данные для события.
	 *
	 * @param mixed $data Данные события.
	 */
	public function setData($data){
		$this->data = $data;
	}
	
	/**
	 * @return mixed Данные события.
	 */
	public function getData(){
		return $this->data;
	}
}
