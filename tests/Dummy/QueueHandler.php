<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\BufferedQueue\Tests\Dummy;


use Mahmud\BufferedQueue\HandlerContract;

class QueueHandler implements HandlerContract {
    static $callCount = 0;
    
    static $args = [];
    
    public function handle($items) {
        self::$callCount++;
        
        self::$args[] = $items;
    }
}