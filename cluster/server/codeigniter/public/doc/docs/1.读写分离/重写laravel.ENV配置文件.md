---
---
### 重写Laravel配置文件.env参数说明
```
该接口完成从redis中取出集群的配置清单，并根据laravel的站点目录位置，备份原来的.env文件，将原来的.env中的CLUSTER_MASTER=172.17.0.3 和 CLUSTER_SLAVES=172.17.0.4,172.17.0.5,172.17.0.2 更换为redis中最新的主从服务器清单。
```
---

######  入参说明
```
提交方式 : GET
URL :    /env_rewrite
例 :    http://www.cluster.com:11211/env_rewrite?act=debug
```
|  参数英文名称 |  参数中文名称 | 是否必须    | 类型  | 参数说明 |
| ------------------ | ------------------- | ------------------- | ------------------ |----------------|
|act  | 调试状态 |  否  |  varchar |act=debug|



#### 返回数据
**正确示例**

```
{
	0: "APP_ENV=local ",
	1: "APP_DEBUG=true ",
	2: "APP_KEY=base64:IxkVvrRLqdJeU9h8vGu1W58OG3NVuDtkMWyC6nIT4qs= ",
	3: "APP_URL=http://localhost ",
	4: " ",
	5: "DB_CONNECTION=mysql ",
	6: "DB_HOST=127.0.0.1 ",
	7: "DB_PORT=3306 ",
	8: "DB_DATABASE_MASTER=ecshop4 ",
	9: "DB_DATABASE_LOG=ecshop4 ",
	10: "DB_USERNAME=root ",
	11: "DB_PASSWORD=cj781124 ",
	12: " ",
	13: "CACHE_DRIVER=file ",
	14: "SESSION_DRIVER=file ",
	15: "QUEUE_DRIVER=sync ",
	16: " ",
	17: "MEMCACHED_HOST=127.0.0.1 ",
	18: "MEMCACHED_PORT=11211 ",
	19: " ",
	20: "REDIS_HOST=127.0.0.1 ",
	21: "REDIS_PASSWORD=null ",
	22: "REDIS_PORT=6379 ",
	23: " ",
	24: "MAIL_DRIVER=smtp ",
	25: "MAIL_HOST=mailtrap.io ",
	26: "MAIL_PORT=2525 ",
	27: "MAIL_USERNAME=null ",
	28: "MAIL_PASSWORD=null ",
	29: "MAIL_ENCRYPTION=null ",
	30: " ",
	33: "CLUSTER_MASTER=172.17.0.3 ",
	34: "CLUSTER_SLAVES=172.17.0.4,172.17.0.5,172.17.0.2 ",
	runtime: 0.056,
	mem_usage: 501632
}
```

######  出参说明

|  参数英文名称 |  参数中文名称| 数据类型  |长度| 
| ------------  | ------------- | ------------- | ------------- |
| runtime | 程序执行时间(秒) | int  |11| 
| mem_usage | 内存耗用(字节) | varchar  |30|
| 1-n |Laravel配置文件清单  | tinyint  |2|
| CLUSTER_MASTER | Redis中主服务器ip | varchar  |30|
| CLUSTER_SLAVES | Redis中从服务器列表 | varchar  |30|


---
---
