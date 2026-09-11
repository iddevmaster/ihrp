<?php

use yii\db\Migration;

/**
 * Class m260910_090000_add_confirm_committees_permission
 *
 * Grants role "ประธานคณะกรรมการ" access to the new
 * submission-committee/confirm-committees action, added so the president
 * confirms the full committee selection (via a pop-up listing the selected
 * names) before the committee-acknowledge email is sent, instead of an
 * email going out immediately for each person selected. Without this,
 * RbacController rejects the action with a 401 for the president role.
 *
 * Written as a migration (rather than an ad-hoc insert) because this
 * database gets restored/synced periodically, which was wiping out manual
 * RBAC inserts made during testing.
 */
class m260910_090000_add_confirm_committees_permission extends Migration
{
    private $permissionName = 'ihrp.submission-committee.confirm-committees';
    private $roleName = 'ประธานคณะกรรมการ';

    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $permission = $auth->getPermission($this->permissionName);
        if ($permission === null) {
            $permission = $auth->createPermission($this->permissionName);
            $auth->add($permission);
        }

        $role = $auth->getRole($this->roleName);
        if ($role !== null && !$auth->hasChild($role, $permission)) {
            $auth->addChild($role, $permission);
        }
    }

    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $role = $auth->getRole($this->roleName);
        $permission = $auth->getPermission($this->permissionName);

        if ($role !== null && $permission !== null && $auth->hasChild($role, $permission)) {
            $auth->removeChild($role, $permission);
        }
        if ($permission !== null) {
            $auth->remove($permission);
        }
    }
}
