<?php

use yii\db\Migration;

/**
 * Class m260909_140000_rename_researcher_role_to_investigator
 *
 * Part of the system-wide wording change นักวิจัย -> ผู้วิจัย (Researcher ->
 * Investigator). The PHP source (Role::roleLabels(), views, mail templates,
 * messages/en/app.php) was already updated in a prior commit, but the RBAC
 * role name and the `role` table row were seeded once by
 * m180218_044122_insert_role.php and never re-synced, so the navbar
 * (Person::getI18nCurrentRoleName(), which reads `role`.`name` from the DB)
 * kept showing the old Thai text.
 *
 * `role`.`name` and `auth_item`.`name` (type=role) store the exact same
 * string by convention (controllers call
 * $auth->getRole($model->role->name)), so both must change together.
 * `auth_item_child`.`parent`/`child` and `auth_assignment`.`item_name` have
 * FK constraints with ON UPDATE CASCADE onto `auth_item`.`name`, so updating
 * `auth_item` alone propagates to them automatically - no separate
 * statements needed for those tables.
 *
 * Also fixes the same "นักวิจัย" wording in two seeded lookup tables that
 * are system labels (not user-entered data or historical records):
 * `resolution`.`name` (6 rows, meeting resolution text shown across
 * submission/meeting screens) and `ethics`.`name` (1 row, GCP checklist
 * item). Deliberately NOT touching `person`.`first_name`/`last_name` (real
 * people's names), `meeting_agenda`.`summary`, or
 * `meeting_person`.`role_name` (historical point-in-time snapshots) found in
 * the same DB scan - those are records, not system wording.
 */
class m260909_140000_rename_researcher_role_to_investigator extends Migration
{
    private $oldRoleName = 'นักวิจัย';
    private $newRoleName = 'ผู้วิจัย';

    private $resolutionIds = [12, 13, 14, 15, 19, 21];
    private $ethicsIds = [4];

    public function safeUp()
    {
        $this->update('auth_item', ['name' => $this->newRoleName], ['name' => $this->oldRoleName, 'type' => 1]);
        $this->update('role', ['name' => $this->newRoleName], ['name' => $this->oldRoleName]);

        foreach ($this->resolutionIds as $id) {
            $this->execute("UPDATE `resolution` SET `name` = REPLACE(`name`, :old, :new) WHERE `id` = :id", [
                ':old' => $this->oldRoleName,
                ':new' => $this->newRoleName,
                ':id' => $id,
            ]);
        }
        foreach ($this->ethicsIds as $id) {
            $this->execute("UPDATE `ethics` SET `name` = REPLACE(`name`, :old, :new) WHERE `id` = :id", [
                ':old' => $this->oldRoleName,
                ':new' => $this->newRoleName,
                ':id' => $id,
            ]);
        }
    }

    public function safeDown()
    {
        foreach ($this->ethicsIds as $id) {
            $this->execute("UPDATE `ethics` SET `name` = REPLACE(`name`, :new, :old) WHERE `id` = :id", [
                ':old' => $this->oldRoleName,
                ':new' => $this->newRoleName,
                ':id' => $id,
            ]);
        }
        foreach ($this->resolutionIds as $id) {
            $this->execute("UPDATE `resolution` SET `name` = REPLACE(`name`, :new, :old) WHERE `id` = :id", [
                ':old' => $this->oldRoleName,
                ':new' => $this->newRoleName,
                ':id' => $id,
            ]);
        }

        $this->update('role', ['name' => $this->oldRoleName], ['name' => $this->newRoleName]);
        $this->update('auth_item', ['name' => $this->oldRoleName], ['name' => $this->newRoleName, 'type' => 1]);
    }
}
