<?php
namespace Buqiu\Sensors;

use Illuminate\Support\Facades\Log;

class SensorsSdk
{
    /**
     *consumer.
     */
    public \FileConsumer $consumer;

    /**
     * 来构造 SensorsAnalytics 对象
     * @var \SensorsAnalytics
     */
    public \SensorsAnalytics $sa;

    public function __construct($path)
    {
        $this->consumer = new \FileConsumer($path);
        // 使用 Consumer 来构造 SensorsAnalytics 对象
        $this->sa = new \SensorsAnalytics($this->consumer);
    }

    public function __destruct()
    {
        $this->sa->close();
    }

    /**
     * 事件记录.
     * @note trackById
     * @param array  $identityList
     * @param string $eventName
     * @param array  $properties
     * @author caoruidong
     */
    public function trackById(array $identityList, string $eventName, array $properties)
    {
        $identity = new \SensorsAnalyticsIdentity($identityList);

        return $this->sa->track_by_id($identity, $eventName, $properties);
    }

    /**
     * 用户注册/登录；给用户绑定手机号或邮箱.
     * @note bind
     * @param  string                                $identityLoginId
     * @param  array                                 $profiles
     * @throws \SensorsAnalyticsIllegalDataException
     * @author caoruidong
     */
    public function bind(string $identityLoginId, array $profiles)
    {
        $identity = new \SensorsAnalyticsIdentity(array_merge(['$identity_login_id' => $identityLoginId], $profiles));

        return $this->sa->bind($identity);
    }

    /**
     * @note unbind
     * @param array $profiles
     * @author caoruidong
     */
    public function unbind(array $profiles)
    {
        $identity = new \SensorsAnalyticsIdentity($profiles);

        return $this->sa->unbind($identity);
    }

    /**
     * 记录用户属性；设置用户属性，如年龄、性别，会员等级等.
     * @note profileSetById
     * @param  string $identityLoginId
     * @param  array  $profiles
     * @return bool
     * @author caoruidong
     */
    public function profileSetById(string $identityLoginId, array $profiles): bool
    {
        $identity = $this->identityLoginId($identityLoginId);

        return $this->sa->profile_set_by_id($identity, $profiles);
    }

    /**
     * 记录初次设定的属性；适用于为用户设置首次激活时间、首次注册时间、用户渠道等属性.
     * @note profileSetOnceById
     * @param  string $identityLoginId
     * @param  array  $profiles
     * @return bool
     * @author caoruidong
     */
    public function profileSetOnceById(string $identityLoginId, array $profiles): bool
    {
        $identity = $this->identityLoginId($identityLoginId);

        return $this->sa->profile_set_once_by_id($identity, $profiles);
    }

    /**
     * 数值类型属性；常用于记录用户付费次数、付费额度、积分等属性.
     * @note profileIncrementById
     * @param  string $identityLoginId
     * @param  array  $profiles
     * @return bool
     * @author caoruidong
     */
    public function profileIncrementById(string $identityLoginId, array $profiles): bool
    {
        $identity = $this->identityLoginId($identityLoginId);

        return $this->sa->profile_increment_by_id($identity, $profiles);
    }

    /**
     * 列表类型的属性；用户喜爱的电影、用户点评过的餐厅等属性，可以记录列表型属性.
     * @note profileAppendById
     * @param  string $identityLoginId
     * @param  array  $profiles
     * @return bool
     * @author caoruidong
     */
    public function profileAppendById(string $identityLoginId, array $profiles): bool
    {
        $identity = $this->identityLoginId($identityLoginId);

        return $this->sa->profile_append_by_id($identity, $profiles);
    }

    /**
     * 用户身份标识.
     * @note identityLoginId
     * @param  string                    $identityLoginId
     * @return \SensorsAnalyticsIdentity
     * @author caoruidong
     */
    public function identityLoginId(string $identityLoginId): \SensorsAnalyticsIdentity
    {
        return new \SensorsAnalyticsIdentity(['$identity_login_id' => $identityLoginId]);
    }

    /**
     * 注册公共属性.
     * @param array $properties
     */
    public function registerSuperProperties(array $properties): void
    {
        $this->sa->register_super_properties($properties);
    }
}
