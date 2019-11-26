<?php

namespace Mahmud\BufferedQueue\Tests;

use Mahmud\BufferedQueue\BufferedQueue;
use Mahmud\BufferedQueue\Tests\Dummy\QueueHandler;
use PHPUnit\Framework\TestCase;

class BufferedQueueTest extends TestCase {
    /**
     * @test
     */
    public function it_returns_cached_instance() {
        $queue_original = BufferedQueue::make('key1', function ($items) {
        }, 5);
        $queue_cached = BufferedQueue::make('key1', function ($items) {
        }, 5);
        
        $this->assertSame($queue_cached, $queue_original);
    }
    
    /**
     * @test
     */
    public function data_can_be_pushed() {
        $items = $this->getItems(2);
        
        $queue = BufferedQueue::make('key1', function ($items) {
        }, 5);
        $queue->push($items[0]);
        $queue->push($items[1]);
        
        $this->assertEquals($items, $queue->getItems());
        
        $queue->finish();
    }
    
    private function getItems($count = 10) {
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $items[] = ["foo{$i}" => "bar{$i}"];
        }
        
        return $items;
    }
    
    /**
     * @test
     */
    public function it_executes_callback_every_time_if_total_stored_items_exceeds_max_items_limit() {
        $items = $this->getItems();
        
        $total_executed = 0;
        
        $queue = BufferedQueue::make('key2', function ($items) use (&$total_executed) {
            $total_executed++;
        }, 3);
        
        
        foreach ($items as $item) {
            $queue->push($item);
        }
        
        $queue->finish();
        
        $this->assertEquals(4, $total_executed);
    }
    
    /**
     * @test
     */
    public function it_passes_items_as_callback_arguments() {
        $items = $this->getItems();
        
        $arguments = [];
        
        $queue = BufferedQueue::make('key3', function ($items) use (&$arguments) {
            $arguments[] = $items;
        }, 3);
        
        foreach ($items as $item) {
            $queue->push($item);
        }
        
        $queue->finish();
        
        $this->assertEquals($arguments[0], [$items[0], $items[1], $items[2]]);
        $this->assertEquals($arguments[1], [$items[3], $items[4], $items[5]]);
        $this->assertEquals($arguments[2], [$items[6], $items[7], $items[8]]);
        $this->assertEquals($arguments[3], [$items[9]]);
    }
    
    /**
     * @test
     */
    public function it_clears_previous_items_if_callback_has_exceptions() {
        $items = $this->getItems();
        
        $arguments = [];
        $queue = BufferedQueue::make('key4', function ($items) use (&$arguments) {
            try {
                $arguments[] = $items;
                throw new \Exception();
            } catch (\Exception $e) {
            
            }
        }, 3);
        
        foreach ($items as $item) {
            $queue->push($item);
        }
        
        $queue->finish();
        
        $this->assertEquals(3, count($arguments[0]));
        $this->assertEquals(3, count($arguments[1]));
        $this->assertEquals(3, count($arguments[2]));
        $this->assertEquals(1, count($arguments[3]));
    }
    
    /**
     * @test
     */
    public function it_accepts_instance_of_handler_contract_as_handler() {
        $items = $this->getItems();
        $queue = BufferedQueue::make('key5', new QueueHandler(), 3);
        
        foreach ($items as $item) {
            $queue->push($item);
        }
        $queue->finish();
        
        $this->assertEquals(4, QueueHandler::$callCount);
    }
    
    /**
     * @test
     */
    public function it_ensures_that_handle_method_is_called_with_proper_arguments() {
        $items = $this->getItems();
        $queue = BufferedQueue::make('key5', new QueueHandler(), 3);
        
        foreach ($items as $item) {
            $queue->push($item);
        }
        $queue->finish();
        
        $arguments = QueueHandler::$args;
        $this->assertEquals($arguments[0], [$items[0], $items[1], $items[2]]);
        $this->assertEquals($arguments[1], [$items[3], $items[4], $items[5]]);
        $this->assertEquals($arguments[2], [$items[6], $items[7], $items[8]]);
        $this->assertEquals($arguments[3], [$items[9]]);
    }
    
    protected function tearDown() {
        parent::tearDown();
    }
}
