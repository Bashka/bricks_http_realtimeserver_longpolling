# Введение

Данный пакет служит для реализации сервера с поддержкой long polling.

Метод `listen` класса _Server_ позволяет пользователю выполнить "ожидающий" 
запрос событий у сервера. Схематически это выглядит следующим образом:

![Схема "ожидающих" запросов 
пользователя](http://plantuml.com:80/plantuml/png/SqmkoIzIA2rEBUBY0f4AkdOAGAeCoB9IoCaiBadDqqWkBIfApIi9oSnDrUHABafDB4bLA0PgI-M2gWJf0V61Ml9Iox68fgUMe9e2aG2gI4vCpW2fIClCIu698ZiXhpGdXwe6kfO1zKJ7miq4oe7zZP211GDK6CaQ1Lk5Wlm23QX2JRsapAAIZ9J4IW00)

Если на указанную в параметре метода дату отсутствуют новые события, метод 
заблокирует запрос до появления таковых.

Пример реализации сервера с поддержкой long polling:

```php
use Bricks\Http\RealtimeServer\LongPolling\Store\FileStore;
use Bricks\Http\RealtimeServer\LongPolling\Server;

$store = new FileStore('storage/pl_store.txt');
$server = new Server($store);
$events = $server->listen($_GET['time']); // Возможна блокировка выполнения.
echo json_encode($events);
```

Приведенная реализация сервера позволяет оповещать пользователя о новых событиях 
системы, но для регистрации событий, создаваемых самим пользователем необходима 
реализация клиента. Схематически это выглядит следующим образом:

![Схема регистрации событий 
пользователя](http://plantuml.com:80/plantuml/png/SqmkoIzIA2rEBUBY0f4AkdOAoJcPgNab2bOAhcL0cWlA1Kga9045Yu4QKf44mNoWU45fSOO6M8Sc5z2WCf1PG6cmeL2ZecC1)

Здесь в качестве связующего звена между клиентом, регистрирующим события 
пользователя и сервером выступает реализация интерфейса _Store\Store_. Она 
должна использовать разделяемую область памяти (на пример внешний кеш, файл или 
базу данных), что позволило бы серверу незамедлительно реагировать на появление 
новых событий.

Пример реализации клиента для обработки событий пользователя:

```php
use Bricks\Http\RealtimeServer\LongPolling\Store\ArrayStore;
use Bricks\Http\RealtimeServer\LongPolling\Event;

$store = new FileStore('storage/pl_store.txt');
$db = ...; // Адаптер базы данных.

// Инициализация хранилища последними событиями.
if(!file_exists('storage/pl_store.txt')){
  $store->init($db->select(...));
}

switch($_GET['action']){
  case 'message':
    $event = new Event($_GET['time']);
    $event->setData([
      'name' => 'message',
      'user' => getCurrentUser(),
      'content' => htmlspecialchars($_GET['content']),
    ]);
    $db->store($event); // Сохранение сообщения в постоянном хранилище.
    $store->push($event); // Регистрация сообщения в памяти сервера.
    break;
  // Обработка других типов событий.
  default:
    return;
}
```
