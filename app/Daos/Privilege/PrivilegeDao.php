<?php
namespace App\Daos\Privilege;

use App\Exceptions\OperationFailedException;
use App\Models\Privilege\RoleModel;
use App\Models\Privilege\RolePrivilegeModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 权限数据访问对象
 *
 * @author googol24
 */
class PrivilegeDao
{
    /**
     * 创建角色
     *
     * @param string $roleName
     * @param array $privilegeList
     *
     * @return int
     */
    public function createRole($roleName, $privilegeList)
    {
        DB::beginTransaction();

        try {
            // 添加角色数据
            $roleId = RoleModel::query()->insertGetId([
                'is_enable' => 1,
                'name'      => $roleName,
            ]);

            // 添加角色权限绑定数据
            $rolePrivilegeData = [
                "role_id"   => $roleId,
                "privilege" => implode(",", $privilegeList),
            ];

            RolePrivilegeModel::query()->insert($rolePrivilegeData);

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('创建角色失败', [
                'exception' => $e,
                'params'    => [
                    'role_name' => $roleName,
                    'privilege' => $privilegeList
                ]
            ]);

            throw new OperationFailedException('创建角色失败！');
        }

        return $roleId;
    }

    /**
     * 更新角色
     *
     * @param int $roleId
     * @param string $roleName
     * @param array $privilegeList
     *
     * @return bool
     *
     */
    public function updateRole($roleId, $roleName, $privilegeList)
    {
        DB::beginTransaction();

        try {
            // 更新角色数据
            RoleModel::query()->whereKey($roleId)->update([
                'name' => $roleName
            ]);

            $rolePrivilegeData = [
                "privilege" => implode(",", $privilegeList),
            ];

            // 更新角色对应的权限
            RolePrivilegeModel::query()->where('role_id', $roleId)->update($rolePrivilegeData);

            DB::commit();

        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('更新角色信息失败 role_id:' . $roleId, [
                'exception' => $e,
                'params'    => [
                    'role_id'   => $roleId,
                    'role_name' => $roleName,
                    'privilege' => $privilegeList
                ]
            ]);

            throw new OperationFailedException('更新角色失败！');
        }

        return true;
    }

    /**
     * 设置角色的禁启用状态
     *
     * @param int $roleId
     * @param int $status
     *
     * @return int
     */
    public function setRoleStatus($roleId, $status)
    {
        $result = RoleModel::query()->whereKey($roleId)->update([
            'status' => $status
        ]);

        return $result;
    }

    /**
     * 查看角色详情
     *
     * @param int $roleId
     *
     * @return array
     *
     */
    public function getRoleInfo($roleId)
    {
        $rolePrivilegeData = RoleModel::query()
            ->select(['id', 'name', 'is_enable', 'created_at', 'updated_at'])
            ->leftJoin('role_privilege', 'role.id', '=', 'role_privilege.role_id')
            ->where('role.is_enable', 1)
            ->where('role.id', $roleId)->first();

        if (!empty($rolePrivilegeData['privilege'])) {
            $rolePrivilegeData['privilege_list'] = array_filter(explode(",", $rolePrivilegeData['privilege']));
            unset($rolePrivilegeData['privilege']);
        }

        return !empty($rolePrivilegeData) ? $rolePrivilegeData->toArray() : null;
    }
}