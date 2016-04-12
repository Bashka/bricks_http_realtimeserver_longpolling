<?php
namespace Bricks\Http\RealtimeServer\LongPolling\Store;
use Bricks\Http\RealtimeServer\LongPolling\Event;

/**
 * Реализация хранилища в файловой системе.
 *
 * @author Artur Sh. Mamedbekov
 */
class FileStore extends ArrayStore{
	/**
	 * @var string Адрес файла.
	 */
	private $file;

	/**
	 * @param string $file Адрес файла, используемого в качестве хранилища.
	 */
	public function __construct($file){
		$this->file = $file;
	}

	/**
	 * @see Bricks\Http\RealtimeServer\LongPolling\Store\Store::init
	 */
	public function init(array $events){
		$resource = fopen($this->file, 'a');
		fwrite($resource, serialize($events));
		fclose($resource);
	}

	/**
	 * @see Bricks\Http\RealtimeServer\LongPolling\Store\Store::get
	 */
	public function get($time){
		if(!file_exists($this->file)){
			return [];
		}

		$resource = fopen($this->file, 'r');
		clearstatcache();
		parent::init(unserialize(fread($resource, filesize($this->file))));
		fclose($resource);

		return parent::get($time);
	}

	/**
	 * @see Bricks\Http\RealtimeServer\LongPolling\Store\Store::push
	 */
	public function push(Event $event){
		$resource = fopen($this->file, 'r+');
		parent::init(unserialize(fread($resource, filesize($this->file))));

		parent::push($event);
		
		ftruncate($resource, 0);
		fseek($resource, 0);
		fwrite($resource, serialize($this->store));
		fclose($resource);
	}
}
