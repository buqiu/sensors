# sensors

#### Description
神策数据SDK

#### Software Architecture
神策数据上报

## 添加 composer.json 

```
    "require": {
        "buqiu/sensors": "^1.0"
    }, 
    
    "repositories": [
        {
            "type": "git",
            "url": "https://gitee.com/buqiu-community/sensors.git"
        }
    ],
```
## 安装

```
composer require buqiu/sensors

```
## 或
```
composer install

```
## 配置 config/app.php

```
'providers' => [
    Buqiu\Sensors\SensorsProvider::class
],

```
## 发布配置
```
php artisan vendor:publish --tag=buqiu-sensors-config

```
## env配置上报日志的绝对路径 
```
SENSORS_LOG_PATH="/home/Helix/www/helixlife/v4/helixlife-platforms/blog/storage/logs"
```
## 参数含义
```
$action : 字符串类型，上报事件或上报用户属性；【track：事件记录追踪，profileSet：用户属性设置，profileSetOnce：用户属性初始化，profileIncrement：属性自增，profileAppend：追加属性，bind：绑定，unbind：解绑】
$identityList : 数组类型,上报唯一身份;【user_uuid:用户uuid(必填)，identity_mp_unionid： 微信的unionid (有就上报，无则置空），identity_cookie_id: 神策生成的唯一身份id (有就上报，无则置空）】
$properties:数组类型，事件属性，或用户属性
$common：数组类型，公共用户属性，（事件上报必填，用户属性上报可置空为数组）
$event:字符串类型，上报的事件名，（上报事件必填，用户属性上报则为空）
```

## 如何使用


## track 事件上报
```
use Buqiu\Sensors\Sensors;

$action ='track';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>''];
$properties=['city'=>'上海'];
$common=['ip'=>'111'];
$event='RegisterSuccess';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```

## profileSet 用户属性上报
```
use Buqiu\Sensors\Sensors;

$action ='profileSet';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>''];
$properties=['ip'=>'上海'];
$common=[];
$event='';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```

## profileSetOnce 属性初始化
```
use Buqiu\Sensors\Sensors;

$action ='profileSetOnce';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>''];
$properties=['source'=>'PC'];
$common=[];
$event='';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```


## profileIncrement 属性自增
```
use Buqiu\Sensors\Sensors;

$action ='profileIncrement';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>''];
$properties=['GamePlayed'=>1];
$common=[];
$event='';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```


## profileAppend 追加属性
```
use Buqiu\Sensors\Sensors;

$action ='profileAppend';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>''];
$properties=['Games'=>["Call of Duty", "Halo"]];
$common=[];
$event='';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```


## bind 绑定
```
use Buqiu\Sensors\Sensors;

$action ='bind';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>'','$identity_mobile'=>'123'];
$properties=[];
$common=[];
$event='';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```

## unbind 解绑
```
use Buqiu\Sensors\Sensors;

$action ='unbind';
$identityList=['user_uuid'=>'3b5513c8-552d-466b-b362-42de7b6bbf66245','identity_mp_unionid'=>'','identity_cookie_id'=>'','$identity_mobile'=>'123'];
$properties=[];
$common=[];
$event='';

$sensors = new Sensors($action, $identityList, $properties, $common, $event);
$sensors->executeAction();

```