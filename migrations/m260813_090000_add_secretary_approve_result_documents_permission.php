<?php

use yii\db\Migration;

/**
 * Class m260813_090000_add_secretary_approve_result_documents_permission
 *
 * Grants role "เลขานุการ" access to the new
 * submission/secretary-approve-result-documents action (part of the
 * "เลขาฯตรวจสอบหนังสือแจ้งผล" step added between president approval and
 * the researcher receiving the result letter). Without this, RbacController
 * rejects the action with a 401 for the secretary role.
 *
 * Written as a migration (rather than an ad-hoc insert) because this
 * database gets restored/synced periodically, which was wiping out manual
 * RBAC inserts made during testing.
 */
class m260813_090000_add_secretary_approve_result_documents_permission extends Migration
{
    private $permissionName = 'ihrp.submission.secretary-approve-result-documents';
    private $roleName = 'เลขานุการ';

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
