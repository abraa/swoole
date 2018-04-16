<?php
 /**
 * ====================================
 * thinkphp5
 * ====================================
 * Author: 1002571
 * Date: 2018/4/12 11:50
 * ====================================
 * File: Swoole.php
 * ====================================
 */
namespace abraa\swoole\traits;



trait Swoole {

    /**
     * tick定时器，可以自定义回调函数。此函数是swoole_timer_tick的别名。
        worker进程结束运行后，所有定时器都会自动销毁
        tick/after定时器不能在swoole_server->start之前使用
     */
    public function tick()
    {
//        $server->tick(1000, function() use ($server, $fd) {
//            $server->send($fd, "hello world");
//        });
    }

    /**在指定的时间后执行函数，需要swoole-1.7.7以上版本。*/
    public function after(){
//        swoole_server->after(int $after_time_ms, mixed $callback_function);
    }

    /**延后执行一个PHP函数。Swoole底层会在EventLoop循环完成后执行此函数。
     * 此函数的目的是为了让一些PHP代码延后执行，程序优先处理IO事件。
     * 底层不保证defer的函数会立即执行，如果是系统关键逻辑，需要尽快执行，请使用after定时器实现。*/
    public function defer(){
//        swoole_server->defer(callable $callback);
    }

    /** 清除tick/after定时器，此函数是swoole_timer_clear的别名。*/
    public function clearTimer(){
//        $timer_id = $server->tick(1000, function ($id) use ($server) {
//            $server->clearTimer($id);
//        });
    }

    /**
     *向客户端发送数据，
     * $data，发送的数据，TCP协议最大不得超过2M，可修改 buffer_output_size 改变允许发送的最大包长度
    UDP协议不得超过65507，UDP包头占8字节, IP包头占20字节，65535-28 = 65507
    UDP服务器使用$fd保存客户端IP，$extraData保存server_fd和port
    发送成功会返回true
    发送失败会返回false，调用$server->getLastError()方法可以得到失败的错误码
     */
    public function send(){
//        bool swoole_server->send(int $fd, string $data, int $extraData = 0);
    }

    /**
     *发送文件到TCP客户端连接
     * $filename 要发送的文件路径，如果文件不存在会返回false
    $offset 指定文件偏移量，可以从文件的某个位置起发送数据。默认为0，表示从文件头部开始发送
    $length 指定发送的长度，默认为文件尺寸。
    操作成功返回true，失败返回false
     */
    public function sendfile(int $fd, string $filename, int $offset =0, int $length = 0){
//        bool swoole_server->sendfile(int $fd, string $filename, int $offset =0, int $length = 0);
    }

    /**
     *  向任意的客户端IP:PORT发送UDP数据包。
     * $ip为IPv4字符串，如192.168.1.102。如果IP不合法会返回错误
    $port为 1-65535的网络端口号，如果端口错误发送会失败
    $data要发送的数据内容，可以是文本或者二进制内容
    $server_socket 服务器可能会同时监听多个UDP端口，此参数可以指定使用哪个端口发送数据包
     */
    public function sendto(string $ip, int $port, string $data, int $server_socket = -1){
//        woole_server->sendto(string $ip, int $port, string $data, int $server_socket = -1);
//        $server->sendto('220.181.57.216', 9502, "hello world");
    }

    /**
     * 阻塞地向客户端发送数据。
     *     有一些特殊的场景，Server需要连续向客户端发送数据，而swoole_server->send数据发送接口是纯异步的，大量数据发送会导致内存发送队列塞满。
     * sendwait目前仅可用于SWOOLE_BASE模式
    sendwait建议只用于本机或内网通信，外网连接请勿使用sendwait
     */
    public function sendwait(int $fd, string $send_data){
//        bool swoole_server->sendwait(int $fd, string $send_data);
    }


    /**
     * 此函数可以向任意worker进程或者task进程发送消息。在非主进程和管理进程中可调用。收到消息的进程会触发onPipeMessage事件
     */
    public function sendMessage(mixed $message, int $dst_worker_id){
//        bool swoole_server->sendMessage(mixed $message, int $dst_worker_id);
    }

    /**检测fd对应的连接是否存在。*/
    public function exist($fd){
//        bool function swoole_server->exist(int $fd)
    }

    /**
     * 停止接收数据。
     * pause方法仅可用于BASE模式
     * @param int $fd
     */
    public function pause($fd){
//        function swoole_server->pause(int $fd);
    }

    /**
     * 恢复数据接收。与pause方法成对使用
     * @param $fd
     */
    public function resume($fd){
//        function swoole_server->resume(int $fd);
    }

    /**
     * swoole_server->getClientInfo函数用来获取连接的信息，别名是swoole_server->connection_info
     * @param $fd
     */
    public function getClientInfo($fd,  int $extraData, bool $ignoreError = false){
//        function swoole_server->getClientInfo(int $fd, int $extraData, bool $ignoreError = false)
    }

    /**
     *用来遍历当前Server所有的客户端连接，Server::getClientList方法是基于共享内存的，不存在IOWait，遍历的速度很快。
     * 另外getClientList会返回所有TCP连接，而不仅仅是当前Worker进程的TCP连接。
     * 此函数接受2个参数，第1个参数是起始fd，第2个参数是每页取多少条，最大不得超过100。
     */
    public function getClientList(){
//        swoole_server::getClientList(int $start_fd = 0, int $pagesize = 10);
    }

    /**
     * 将连接绑定一个用户定义的UID，可以设置dispatch_mode=5设置以此值进行hash固定分配。可以保证某一个UID的连接全部会分配到同一个Worker进程。
     * 使用bind机制，网络通信协议需要设计握手步骤。客户端连接成功后，先发一个握手请求，之后客户端不要发任何包
     * 。在服务器bind完后，并回应之后。客户端再发送新的请求。
     * @param int $fd
     * @param int $uid
     */
    public function bind(int $fd, int $uid){
//        同一个连接只能被bind一次，如果已经绑定了UID，再次调用bind会返回false
//      可以使用$serv->connection_info($fd) 查看连接所绑定UID的值
//        bool swoole_server::bind(int $fd, int $uid)
    }

    /**
     * 得到当前Server的活动TCP连接数，启动时间，accpet/close的总次数等信息。
     */
    public function stats(){
//        swoole_server->stats();
    }

    /**
     *  在task中调用触发 Server设置onFinish回调函数
     * 在onTask回调函数中return字符串，等同于调用finish
     */
    public function finish(){
//        $serv->finish("response");
//        这个里面有个坑（v1.7.21） 如果在task里调用 $serv->finish("response"); 后接着还要运行一些代码，
//实际上会受 worker 里使用 $serv->taskwait() 和 $serv->task() 表现不一致
// 如果在worker 里调用的是 $serv->taskwait() ，则会立即收到 $serv->finish("response"); 的反馈
//而如果在 worker 里调用的是 $serv->task()，则会等到整个 onTask 全部执行完毕，onFinish 才会收到 $serv->finish("response"); 的反馈，
//而不是在代码位置的时候收到，此时和代码结束出 return 'response' 一样。

//        另外一个差异就是，如果在 onTask 里先调了 finish('ok1') 再 return 'ok2' 的话，
// 如果在worker里调的是 $serv->task() 则在onFinish 里会分别收到2次回调 而如果在worker里调的是 $serv->taskwait() 则只会立即返回 ok1 ，
//ok2 则会被丢弃了
    }


    /**
     * 认连接，与enable_delay_receive或wait_for_bind配合使用。当客户端建立连接后，并不监听可读事件。
     * 仅触发onConnect事件回调，在onConnect回调中执行confirm确认连接，这时服务器才会监听可读事件，接收来自客户端连接的数据。
     * @param $fd
     */
    public function confirm($fd){
//        return $this->swoole->confirm( $fd);
    }

    /**
     * 设置客户端连接为保护状态，不被心跳线程切断。
     * @param int $fd
     * @param bool $value
     * @return mixed
     */
    public function protect( $fd,  $value = 1){
//        return $this->swoole->protect($fd, $value);
    }
}