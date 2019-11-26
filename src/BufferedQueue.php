<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmudkuet11@gmail.com>.
 */

namespace Mahmud\BufferedQueue;

class BufferedQueue {
    
    /**
     * @var array BufferedQueue[]
     */
    protected static $instances = [];
    /**
     * @var array
     */
    protected $all_data;
    /**
     * @var integer
     */
    protected $max_items_in_queue;
    /**
     * @var \Closure|HandlerContract
     */
    protected $handler;
    
    /**
     * BufferedQueue constructor.
     *
     * @param $handler \Closure|HandlerContract
     * @param $max_items_in_queue
     */
    public function __construct($handler, $max_items_in_queue) {
        $this->all_data = [];
        $this->max_items_in_queue = $max_items_in_queue;
        $this->handler = $handler;
    }
    
    public static function make($key, $handler, $max_items_in_queue) {
        if (array_key_exists($key, self::$instances)) {
            return self::$instances[$key];
        }
        
        $instance = new self($handler, $max_items_in_queue);
        self::$instances[$key] = $instance;
        
        return $instance;
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
    
    /**
     * @return $this
     * @throws \Exception
     */
    public function run() {
        if (count($this->all_data) > 0) {
            try {
                $this->callHandler();
            } catch (\Exception $e) {
                throw $e;
            } finally {
                $this->all_data = [];
            }
        }
        
        return $this;
    }
    
    /**
     * @return mixed
     * @throws \Exception
     */
    protected function callHandler() {
        if ($this->handler instanceof \Closure) {
            return call_user_func($this->handler, $this->all_data);
        }
        
        if ($this->handler instanceof HandlerContract) {
            return $this->handler->handle($this->all_data);
        }
        
        throw new \Exception("Handler is not supported. Must be a valid closure or instance of " . HandlerContract::class);
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
