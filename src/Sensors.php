<?php
namespace Buqiu\Sensors;

use Illuminate\Support\Facades\Log;
class Sensors
{
    // 上报用户uuid
    public string $userUUID;
    // 事件名
    public string $event;
    // 自定义属性
    public array $property;
    // 用户行为
    public string $action;
    // 身份属性
    public array $identityList;
    // 公共属性
    public array $common;
    // 上传路径
    public string $logPath;

    public function __construct(string $action = '', array $identityList = [], array $property = [], array $common = [], string $event = '', string $logPath = '')
    {
        $this->init(action: $action,identityList:  $identityList,property:  $property,common:  $common, event: $event,logPath: $logPath);
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
                'profileSet'       => $sdk->profileSetById($this->userUUID, $this->property), // 属性设置
                'profileSetOnce'   => $sdk->profileSetOnceById($this->userUUID, $this->property), // 属性初始化
                'profileIncrement' => $sdk->profileIncrementById($this->userUUID, $this->property), // 属性自增
                'profileAppend'    => $sdk->profileAppendById($this->userUUID, $this->property), // 追加属性
                'bind'             => $sdk->bind($this->userUUID, $this->property), // 绑定
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
     * @param string $action
     * @param array $identityList
     * @param array $property
     * @param array $common
     * @param string $event
     * @param string $logPath
     * @author caoruidong
     */
    public function init(string $action = '', array $identityList =[], array $property =[], array $common =[], string $event ='',string $logPath =''): void
    {
        $this->action    = $action;
        $this->event     = $event;
        $this->property  = $property;
        $this->userUUID  = $identityList['user_uuid'] ?? null;
        $this->common    = $common;
        $this->getIdentityList($identityList);
        // 如果传入的 $logPath 不为空，则使用传入的 $logPath；否则使用配置文件中的默认路径
        $this->logPath = $logPath ?: config('sensors.log_path') . '/sensors.' . date('Y-m-d') . '.log';
    }

    /**
     * 获取用户身份属性
     * @param array $identityList
     * @return array
     */
    public function getIdentityList(array $identityList): array
    {
        foreach ($identityList as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'user_uuid':
                        $this->identityList['$identity_login_id'] = $value;
                        break;
                    case 'identity_mp_unionid':
                        $this->identityList['$identity_mp_unionid'] = $value;
                        break;
                    case 'identity_cookie_uuid':
                        $this->identityList['$identity_cookie_id'] = $value;
                        break;
                    default:
                        // 如果不是上述三个键，则直接设置到 $this->identityList
                        $this->identityList[$key] = $value;
                        break;
                }
            }
        }
        return $this->identityList;
    }
}