<?php
 /**
 * ====================================
 * thinkphp5 Swoole_Server基类
 * ====================================
 * Author: 1002571
 * Date: 2018/4/2 15:30
 * ====================================
 * File: Server.php
 * ====================================
 */

namespace abraa\swoole;

use swoole_http_server;
use swoole_server;
use swoole_websocket_server;
use abraa\swoole\traits\Swoole;
/**
 * Worker控制器扩展类
 */
abstract class Server extends Config
{
    use Swoole;
    /**
     * 几个问题:
     * 1. 我这个要解决的是文档混乱,代码能够大概了解swoole_Server的功能
     * 2. 能够兼容swoole的更新,因此功能已聚合为主,避免逻辑重写
     * 3. 解决swoole目前存在的一部分问题,比如内存泄漏等(逻辑处理因变量导致的内存泄漏)
     * 4. 能够简单的写swoole_Sercer代码贴合tp风格,适度封装原来的swoole.(即避免swoole原生代码在外部编写导致更新代码不兼容)
     * 5. ...
     *
     * 几个原则:
     * 1.独立性,符合tp阅读习惯,却不依赖于tp
     * 2. 可追踪性, 避免使用反射之类的构建,保持代码的可追踪阅读性
     * 3. 可扩展性, 多继承. 方便扩展功能,同时保持原有功能完整
     *
     * 架构方式:
     * 1. 参数配置有外部注入
     * 2. 不同功能使用不同子类实现
     * 3.
     */
    protected $swoole;
    protected $sockType;
    protected $mode;
    protected $host   = '0.0.0.0';
    protected $port   = 9501;
    protected $process = [];                //添加的自定义进程.
    protected $listener = [];                //添加的额外监听端口.

    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {

    }
    protected function init()
    {
    }

    public function getSwoole(){
        return $this->swoole;
    }

    /**清除变量**/
    public function clean(){
        static::$setting = [];
        $this->process = [];
        $this->listener = [];
    }

    /**
     * 魔术方法 有不存在的操作的时候执行
     * @access public
     * @param string $method 方法名
     * @param array $args 参数
     * @return mixed
     */
    public function __call($method, $args)
    {
        call_user_func_array([$this->swoole, $method], $args);
    }

    //服务端需要基础功能
    /** 日志记录(输出) */
    function log($msg){
        echo $msg;
    }

    /** swoole 原生函数调用 */
    public function start()
    {
        $this->swoole->start();
    }
    public function stop()
    {
        $this->swoole->stop();
    }

    /**
     * reload 重启所有worker进程。
     * @param bool $only_reload_taskworkrer 是否仅重启task进程
     */
    public function reload( $only_reload_taskworkrer = false){
        $this->swoole->reload($only_reload_taskworkrer);
    }
    public function shutdown(){
        $this->swoole->shutdown();
        $this->clean();
        exit;                   //直接结束
    }

    /**
     * 设置swoole配置选项
     * @param array $arr 配置选项
     */
    public function set($arr = []){
        if(!empty($arr)){
            static::setSetting($arr);
        }
        $this->swoole->set(static::getSetting());
    }

    /**
     * 设置swoole回调函数
     * @param string $event
     * @param mixed $callback
     */
    public function on( $event, mixed $callback){
        $this->swoole->on( $event,  $callback);
    }

    /**
     * 添加一个进程
     * @param $key      进程标识
     * @param mixed $callback   进程回调函数
     */
    public function addProcess($key,mixed $callback){
        $server = $this->swoole;
        $process = new \swoole_process($callback);
        if($server->addProcess($process)){
            $this->process[$key] = $process;
        }

    }

    /**
     * 返回一个已添加的自定义进程
     * @param $key  进程标识
     * @return mixed|null
     */
    public function getProcess($key){
        return isset($this->process[$key])? $this->process[$key] : null;
    }

    /**
     *  添加一个监听端口
     * @param string $key 监听端口自定义标识符
     * @param string $host  0.0.0.0
     * @param int $port    9999
     * @param $type
     */
    public function addListener($key, $host,  $port, $type = SWOOLE_SOCK_TCP){
        $listener = $this->swoole->addListener($host,$port,$type);
        if(false !== $listener){
            $this->listener[$key] = $listener;
        }
        return $listener;
    }
    /**
     * 返回一个已添加的监听端口
     * @param $key 端口标识
     * @return mixed|null
     */
    public function getListener($key){
        return isset($this->listener[$key])? $this->listener[$key] : null;
    }

    /**
     * 获取最近一次操作错误的错误码。业务代码中可以根据错误码类型执行不同的逻辑。
     * @return mixed
     */
    public function getLastError(){
        return $this->swoole->getLastError();
    }

    /**
     * 使用Task功能，必须先设置 task_worker_num，并且必须设置Server的onTask和onFinish事件回调函数
     * @param mixed $data
     * @param int $dst_worker_id
     * @param $callback      function (swoole_server $serv, $task_id, $data)
     * @return bool|int 返回false或task_id
     */
    public function task(mixed $data,  $dst_worker_id = -1,$callback=null){
        return $this->swoole->task($data, $dst_worker_id, $callback);
    }

    /**
     * taskwait与task方法作用相同，用于投递一个异步的任务到task进程池去执行。(阻塞)
     * @param mixed $data
     * @param $timeout
     * @param int $dst_worker_id
     * @return string | bool$result为任务执行的结果，由$serv->finish函数发出。如果此任务超时，这里会返回false。
     */
    public function taskwait(mixed $data,$timeout,  $dst_worker_id = -1){
        return $this->swoole->taskwait($data,$timeout, $dst_worker_id);
    }

    /**
     * 并发执行多个Task
     * 任务完成或超时，返回结果数组。结果数组中每个任务结果的顺序与$tasks对应，如：$tasks[2]对应的结果为$result[2]
     * 某个任务执行超时不会影响其他任务，返回的结果数据中将不包含超时的任务
     * @param array $tasks
     * @param double $timeout  单位为秒，默认为0.5
     */
    public function taskWaitMulti(array $tasks,  $timeout = 0.5){
        return $this->swoole->taskWaitMulti($tasks, $timeout);
    }

    /**
     * 并发执行Task并进行协程调度。仅用于2.0版本。
     * 在onFinish中收集对应的任务结果，保存到结果数组中。判断是否所有任务都返回了结果，如果为否，继续等待。如果为是，进行resume恢复对应协程的运行，并清除超时定时器
     * 在规定的时间内任务没有全部完成，定时器先触发，底层清除等待状态。将未完成的任务结果标记为false，立即resume对应协程
     * @param array $tasks
     * @param float $timeout
     * @return
     */
    public function taskCo(array $tasks, float $timeout = 0.5){
        return $this->taskCo( $tasks, $timeout);
    }

    /**
     * 检测服务器所有连接，并找出已经超过约定时间的连接。如果指定if_close_connection，则自动关闭超时的连接。未指定仅返回连接的fd数组。
     * 必须设置了 heartbeat_check_interval
     * @param bool $if_close_connection 是否关闭超时的连接
     * @return Array 返回一个连续数组，元素是已关闭(超时)的$fd。
     */
    public function heartbeat($if_close_connection = true){
       return $this->swoole->heartbeat($if_close_connection);
    }

    /**
     * 得到底层的socket句柄
     * 此方法需要依赖PHP的sockets扩展，并且编译swoole时需要开启--enable-sockets选项
     * socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)
     */
    public function getSocket(){
        $socket = $this->swoole->getSocket();
        return $socket;
    }




}