<?php
 /**
 * ====================================
 * SplHeap数据结构需要指定一个compare方法来进行元素的对比，从而实现自动排序。
 * ====================================
 * Author: 1002571
 * Date: 2018/4/8 17:58
 * ====================================
 * File: MaxHeap.php
 * ====================================
 */

namespace abraa\swoole\data;


class MaxHeap extends \SplHeap{


    /**
     * 排序解析函数 $a-$b为负数是降序($a比$b小)
     * @param $a
     * @param $b
     * @return mixed
     */
    protected function compare($a, $b)
    {
        return $a - $b;
    }
}