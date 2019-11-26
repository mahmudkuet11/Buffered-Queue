# Buffered Queue

[![Latest Stable Version](https://poser.pugx.org/mahmud/buffered-queue/v/stable)](https://packagist.org/packages/mahmud/buffered-queue)
[![Build Status](https://travis-ci.org/mahmudkuet11/Buffered-Queue.svg?branch=master)](https://travis-ci.org/mahmudkuet11/Buffered-Queue)
[![License](https://poser.pugx.org/mahmud/buffered-queue/license)](https://packagist.org/packages/mahmud/buffered-queue)

A simple to use buffering queue as PHP package  (no dependencies), which flexibly buffers objects, strings, integers etc. until a maximum size is reached

# Installation
Via Composer

`composer require mahmud/buffered-queue`

# Usage

## Basic usage

```php
use Mahmud\BufferedQueue\BufferedQueue;

// Maximum size is 3
$queue = BufferedQueue::make('key', function($items){
    var_dump($items); // Output is mentioned below
}, 3);

$queue->push('item 1');
$queue->push('item 2');
$queue->push('item 3');
$queue->push('item 4');
$queue->push('item 5');
$queue->push('item 6');
$queue->push('item 7');

$queue->finish();

// Output

// First
[
    'item 1', 'item 2', 'item 3'
]

// Second
[
    'item 4', 'item 5', 'item 6'
]

// Third
[
    'item 7'
]
```

Also instance of Mahmud\BufferedQueue\HandlerContract can be passed.

```php
use Mahmud\BufferedQueue\HandlerContract;

class QueueHandler implements HandlerContract {

    public function handle($items) {
        // Do something
    }
}

$queue = BufferedQueue::make('key', new QueueHandler, 3);
```

## API

### make($key, $callback, $max_size)

```php
BufferedQueue::make($key, $callback, $max_size);
```

- `$key` is a unique string. For duplicated key this will return same instance.

```php
$queue1 = BufferedQueue::make('key1', $callback, $max_size);
$queue2 = BufferedQueue::make('key1', $callback, $max_size);

$queue1 === $queue2 // true
```

- `$callback` will receive array of items.
- `$max_size` determines the maximum size of the queue. When queue is full, callback is triggered with the buffered items.

### push($item)

`$item` can be anything. object, string, integer, boolean, array etc...

```php
$queue->push('item 1');
```

### finish()
Trigger callback with buffered items at any time. Generally used after pushing all items.

### run()
Force to run the callback with buffered items at any time. After running the callback buffer will be empty.

### getItems()
Get all items in buffer.

# Testing

`vendor/bin/phpunit`

## License

MIT license. Please see the [license file](license.md) for more information.