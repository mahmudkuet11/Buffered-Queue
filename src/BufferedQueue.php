<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmudkuet11@gmail.com>.
 */

namespace Mahmud\BufferedQueue;

class BufferedQueue {
    
    private $all_data;
    
    private $max_items_in_queue;
    
    private $callback;
    
    private static $instances = [];
    
    /**
     * BufferedQueue constructor.
     * @param $callback
     * @param $max_items_in_queue
     */
    public function __construct($callback, $max_items_in_queue) {
        $this->all_data = [];
        $this->max_items_in_queue = $max_items_in_queue;
        $this->callback = $callback;
    }
    
    public static function make($key, $callback, $max_items_in_queue) {
        if (array_key_exists($key, self::$instances)) {
            return self::$instances[$key];
        }
        
        $instance = new self($callback, $max_items_in_queue);
        self::$instances[$key] = $instance;
        
        return $instance;
    }
    
    /**
     * @return $this
     * @throws \Exception
     */
    public function run() {
        if (count($this->all_data) > 0) {
            try{
                call_user_func($this->callback, $this->all_data);
            }catch (\Exception $e){
                throw $e;
            }finally{
                $this->all_data = [];
            }
        }
        
        return $this;
    }
    
    /**
     * @param $data
     *
     * @return $this
     * @throws \Exception
     */
    public function push($data) {
        $this->all_data[] = $data;
        
        if (count($this->all_data) >= $this->max_items_in_queue) {
            $this->run();
        }
        
        return $this;
    }
    
    public function getItems() {
        return $this->all_data;
    }
    
    /**
     * @return $this
     * @throws \Exception
     */
    public function finish() {
        $this->run();
        
        return $this;
    }
    
    /**
     * @throws \Exception
     */
    public function __destruct() {
        $this->run();
    }
}
