<?php
namespace App\Contracts\User;

/**
 * 用户账号相关接口
 *
 * @author zhangzhengkun
 */
interface UserAccountInterface
{
    /**
     * 根据指定平台的openid获取用户id
     *
     * @param string $openId
     * @param string $platform
     *
     * @return int
     */
    public function getUserIdByOpenId($openId, $platform);

    /**
     * 获取指定用户的基本信息
     *
     * @param int $userId
     * @param array $fields
     *
     * @return array
     */
    public function getUserInfo($userId, $fields);

    /**
     * 获取用户列表信息
     *
     * @param array $filters
     * @param array $fields
     * @param array $orderBys
     * @param int $skip
     * @param int $limit
     *
     * @return array
     */
    public function getUserList($filters, $fields, $orderBys = [], $skip = 0, $limit = 20);

    /**
     * 注册用户账号
     *
     * @param string $platform
     * @param string $openId
     * @param string $nickname
     * @param string $avatar
     * @param string|null $mobile
     * @param array|null $arguments
     *
     * @return int
     */
    public function registerUserAccount($platform, $openId, $nickname, $avatar, $mobile = null, $arguments = null);

    /**
     * 修改用户基本信息
     *
     * @param int $userId
     * @param string $nickname
     * @param string $avatar
     * @param string|null $mobile
     * @param array|null
     *
     * @return int
     */
    public function updateUserInfo($userId, $nickname, $avatar, $mobile = null, $arguments = null);
}