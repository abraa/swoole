<?php
 /**
 * ====================================
 * thinkphp5
 * ====================================
 * Author: 1002571
 * Date: 2018/4/9 18:21
 * ====================================
 * File: SwooleServer.php
 * ====================================
 */

namespace abraa\swoole;


class SwooleServer extends Server{
    protected $eventList    = ['Start', 'ManagerStart', 'ManagerStop', 'PipeMessage', 'Task', 'Packet', 'Finish', 'Receive', 'Connect', 'Close', 'Timer', 'WorkerStart', 'WorkerStop', 'Shutdown', 'WorkerError'];

    public function __construct(string $host, int $port = 9501, int $mode = SWOOLE_PROCESS,
                                int $sock_type = SWOOLE_SOCK_TCP)
    {
        $this->host = $host;
        $this->port = $port;
        $this->mode = $mode;
        $this->sockType = $sock_type;
        parent::__construct();
        $this->swoole = new swoole_server($this->host, $this->port, $this->mode, $this->sockType);

    }
}