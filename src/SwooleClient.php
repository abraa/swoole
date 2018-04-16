<?php
 /**
 * ====================================
 * thinkphp5
 * ====================================
 * Author: 1002571
 * Date: 2018/4/14 14:14
 * ====================================
 * File: SwooleClient.php
 * ====================================
 */

namespace abraa\swoole;


class SwooleClient extends Client{

    /**
     * @param int $sock_type  $sock_type表示socket的类型，如TCP/UDP
     * @param int $is_sync $is_sync表示同步阻塞还是异步非阻塞，默认为同步阻塞
     * @param string $key $key用于长连接的Key，默认使用IP:PORT作为key。相同key的连接会被复用
     */
    function __construct( $sock_type = SWOOLE_SOCK_TCP,  $is_sync = SWOOLE_SOCK_SYNC,  $key =null){
        switch($is_sync){
            case SWOOLE_SOCK_SYNC:
                //        php-fpm/apache环境下只能使用同步客户端
                //apache环境下仅支持prefork多进程模式，不支持prework多线程
                if(is_null($key)){
                    $this->client = new \swoole_client($sock_type, $is_sync);
                }else{
                    $this->client = new \swoole_client($sock_type, $is_sync, $key);
                }
                if (!$this->client->connect('127.0.0.1', 9501, -1))
                {
                    exit("connect failed. Error: {$this->client->errCode}\n");
                }
            case SWOOLE_SOCK_ASYNC:
//                异步客户端只能使用在cli命令行环境
//                一种比较典型的使用场景就是你的后端服务器前面挡了一个网关服务器，
//                网关和后端之间是通过内网TCP长链接方式通信，网关对所有前端实现http协议，
//                那么，异步的swoole client此时就可以在网关服务器上得到价值实现。具体来说，
//                就是使用swoole http server实现一个常驻内存级的http服务器，然后在swoole http server中使用异步client连接后端服务器。
                $this->client = new \swoole_client($sock_type, $is_sync);
        }

    }

    /**
     * 设置swoole配置选项
     * @param array $arr 配置选项
     */
    public function set($arr = []){
        if(!empty($arr)){
            static::setSetting($arr);
        }
        $this->client->set(static::getSetting());
    }

    /**
     * 设置swoole回调函数
     * @param string $event
     * @param mixed $callback
     */
    public function on( $event, mixed $callback){
        $this->client->on( $event,  $callback);
    }

    /**
     * 连接到远程服务器，
     * @param string $host
     * @param int $port
     * @param float $timeout
     * @param int $flag
     * @return bool
     */
    public function connect( $host,  $port,  $timeout = 0.5,  $flag = 0){
        return $this->client->connect( $host,  $port,  $timeout ,  $flag );
    }

    /**
     * 检查是否连接到远程server
     * @return bool
     */
    public function isConnected(){
        return $this->client->isConnected();
    }

    /**
     * 返回socket资源句柄
     * 此方法需要依赖PHP的sockets扩展，并且编译swoole时需要开启--enable-sockets选项
     * 使用socket_set_option函数可以设置更底层的一些socket参数。
     * @return mixed
     */
    public function getSocket(){
        return $this->client->getSocket();
    }

    /**
     * @return mixed 调用成功返回一个数组，如：array('host' => '127.0.0.1', 'port' => 53652)
     */
    public function getsockname(){
        return $this->client->getsockname();
    }

    /**
     * 获取对端socket的IP地址和端口，仅支持SWOOLE_SOCK_UDP/SWOOLE_SOCK_UDP6类型的swoole_client对象
     * 此函数必须在$client->recv() 之后调用
     * @return mixed
     */
    public function getPeerName(){
        return $this->client->getpeername();
    }


    /**
     * 获取服务器端证书信息。(必须ssl握手完成)
     * 可以使用openssl扩展提供的openssl_x509_parse函数解析证书的信息
     * @return mixed 执行失败返回false
     */
    public function getPeerCert(){
        return $this->client->getPeerCert();
    }

    /**
     * 发送数据到远程服务器
     * @param string $data 支持二进制数据
     * @return mixed 失败返回false，并设置$swoole_client->errCode
     */
    public function send($data){
        return $this->client->send($data);
    }

    /**
     * 用于从服务器端接收数据
     * @param int $size
     * @param int $flags
     * @return string
     */
    public function recv( $size = 65535,  $flags = 0){
        return $this->client->recv( $size  ,  $flags  );
    }

    /**
     * 睡眠停止接收服务端返回数据(一般在回调中使用)
     */
    public function sleep(){
        $this->client->sleep();
    }

    /**
     * 唤醒 (一般在回调中使用)
     */
    public function wakeup(){
        $this->client->wakeup();
    }

    /**
     * 动态开启SSL隧道加密。(一般在回调中使用)
     * 同步:if ($client->enableSSL())
     * 异步:$cli->enableSSL(function($client) {
    //握手完成，此时发送和接收的数据是加密的
    $client->send("hello");
    })
     * @return bool
     */
    public function enableSSL(){
        return $this->client->enableSSL();
    }

    /**
     * 关闭连接 (一般在回调中使用)
     * @return bool
     */
    public function close(){
        return $this->client->close();
//        配合使用onBufferEmpty，等待发送队列为空时进行close操作
//协议设计为onReceive收到数据后主动关闭连接，发送数据时对端主动关闭连接
//        $client->on("receive", function(swoole_client $cli, $data){
//            $cli->send(str_repeat('A', 1024*1024*4)."\n");
//        });
//        $client->on("bufferEmpty", function(swoole_client $cli){
//            $cli->close();
//        });
    }
}