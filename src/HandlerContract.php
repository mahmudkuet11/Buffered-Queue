<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\BufferedQueue;


interface HandlerContract {
    /**
     * @param $items array
     *
     * @return mixed
     */
    public function handle($items);
}