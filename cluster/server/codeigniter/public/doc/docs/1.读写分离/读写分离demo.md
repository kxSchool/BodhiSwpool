---
---
### 数据库读写分离微服务demo参数说明
```
该demo演示swooleMVC框架如何操作数据库集群服务器
```
---

######  入参说明
```
提交方式 : GET
URL :    /env_rewrite
例 :    http://www.cluster.com:11211/test?act=debug
```
|  参数英文名称 |  参数中文名称 | 是否必须    | 类型  | 参数说明 |
| ------------------ | ------------------- | ------------------- | ------------------ |----------------|
|act  | 调试状态 |  否  |  varchar |act=debug|



#### 返回数据
**正确示例**

```
{
	user_id: "2",
    email: "vip@ecshop.com",
    user_name: "slave2",
    password: "232059cb5361a9336ccf1b8c2ba7657a",
    question: "",
    answer: "",
    sex: "0",
    birthday: "1949-01-01",
    user_money: "0.00",
    frozen_money: "0.00",
    pay_points: "0",
    rank_points: "0",
    address_id: "0",
    reg_time: "1505271600",
    last_login: "0",
    last_time: "0000-00-00 00:00:00",
    last_ip: "",
    visit_count: "0",
    user_rank: "0",
    is_special: "0",
    ec_salt: null,
    salt: "0",
    parent_id: "0",
    flag: "0",
    alias: "",
    msn: "",
    qq: "",
    office_phone: "",
    home_phone: "",
    mobile_phone: "",
    is_validated: "0",
    credit_line: "0.00",
    passwd_question: null,
    passwd_answer: null,
    runtime: 0.005,
    mem_usage: 455192
}
```

######  出参说明

|  参数英文名称 |  参数中文名称| 数据类型  |长度| 
| ------------  | ------------- | ------------- | ------------- |
| user_name | users库中记录 | varchar  |30| 
| runtime | 程序执行时间(秒) | int  |11| 
| mem_usage | 内存耗用(字节) | varchar  |30|


---
---
