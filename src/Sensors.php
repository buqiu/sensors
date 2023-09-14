<?php
namespace Buqiu\Sensors;

use Illuminate\Support\Facades\Log;
class Sensors
{
    public string $user_uuid;

    // 事件名
    public string $event;

    // 自定义属性
    public array $property;

    // 行为
    public string $action;
    // 身份属性
    public array $identityList;
    // 公共属性
    public array $common;

    public string $logPath;

    public function __construct(string $action, array $identityList, array $property = [], array $common = [], string $event = '')
    {
        $this->init($action, $identityList, $property, $common, $event);
        $this->logPath = config('sensors.log_path').'/sensors.'.date('Y-m-d').'.log';
    }

    /**
     * 事件上报.
     * @note executeAction
     * @author caoruidong
     */
    public function executeAction(): bool
    {
        $sdk = new SensorsSdk($this->logPath);
        if (!empty($this->common)) {
            $sdk->registerSuperProperties($this->common); // 注册公共属性
        }
        try {
             match ($this->action) {
                'profileSet'       => $sdk->profileSetById($this->user_uuid, $this->property), // 属性设置
                'profileSetOnce'   => $sdk->profileSetOnceById($this->user_uuid, $this->property), // 属性初始化
                'profileIncrement' => $sdk->profileIncrementById($this->user_uuid, $this->property), // 属性自增
                'profileAppend'    => $sdk->profileAppendById($this->user_uuid, $this->property), // 追加属性
                'bind'             => $sdk->bind($this->user_uuid, $this->property), // 绑定
                'unbind'           => $sdk->unbind($this->property), // 解绑
                default            => $sdk->trackById($this->identityList, $this->event, $this->property), // track 事件记录上报
            };
             return true;
        } catch (\SensorsAnalyticsIllegalDataException $e) {
            Log::channel('exception')->error('神策上报异常:', ['code' => $e->getCode(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'message' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * 初始化.
     * @note init
     * @param $action
     * @param $identityList
     * @param $property
     * @param $common
     * @param $event
     * @return bool
     * @author caoruidong
     */
    public function init($action, $identityList, $property, $common, $event): bool
    {
        $this->action    = $action;
        $this->event     = $event;
        $this->property  = $property;
        $this->user_uuid = $identityList['user_uuid'];
        $this->common    = $common;
        if (isset($identityList['user_uuid']) && $identityList['user_uuid']) {
            $this->identityList['$identity_login_id'] = $identityList['user_uuid'];
        }
        if (isset($identityList['identity_mp_unionid']) && $identityList['identity_mp_unionid']) {
            $this->identityList['$identity_mp_unionid'] = $identityList['identity_mp_unionid'];
        }
        if (isset($identityList['identity_cookie_uuid']) && $identityList['identity_cookie_uuid']) {
            $this->identityList['$identity_cookie_id'] = $identityList['identity_cookie_uuid'];
        }

        return true;
    }
}