<?php
 /**
 * ====================================
 * 异步并发的服务器里经常使用队列实现生产者消费者模型，解决并发排队问题。PHP的SPL标准库中提供了SplQueue扩展内置的队列数据结构
 * ====================================
 * Author: 1002571
 * Date: 2018/4/8 18:08
 * ====================================
 * File: SplQueue.php
 * ====================================
 */

namespace abraa\swoole\data;


class SplQueue extends \SplQueue{

//$queue = new SplQueue;
////入队
//$queue->push($data);
////出队
//$data = $queue->shift();
////查询队列中的排队数量
//$n = count($queue);
}