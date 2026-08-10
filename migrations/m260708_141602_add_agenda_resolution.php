<?php

use yii\db\Migration;

/**
 * Class m260708_141602_add_agenda_resolution
 *
 * Keeps the original (un-stamped) CV / training file so the e-signature flow can
 * serve the stamped file when "ประทับชื่อ" is chosen, or revert to the original
 * when it is not — without losing the raw upload.
 */
class m260708_141602_add_agenda_resolution extends Migration {

    /**
     * {@inheritdoc}
     */
    public function safeUp() {

        $this->addColumn('submission', 'resolution_id', $this->integer());
        $this->addColumn('meeting_agenda', 'resolution_id', $this->integer());

        $this->addCommentOnColumn('submission', 'resolution_id', 'รหัสผลการพิจารณา');
        $this->addCommentOnColumn('meeting_agenda', 'resolution_id', 'รหัสผลการพิจารณา');

        $this->addForeignKey('fk_submission_resolution_id', 'submission', 'resolution_id', 'resolution', 'id');
        $this->addForeignKey('fk_meeting_agenda_resolution_id', 'meeting_agenda', 'resolution_id', 'resolution', 'id');

        $this->createTable('agenda_resolution', [
            'id' => $this->primaryKey(),
            'agenda_id' => $this->integer(),
            'resolution_id' => $this->integer(),
            'deleted' => $this->boolean()->notNull()->defaultValue(FALSE),
            'created_by' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_by' => $this->integer(),
            'updated_at' => $this->dateTime(),
        ]);

        $this->addCommentOnColumn('agenda_resolution', 'agenda_id', 'วาระ');
        $this->addCommentOnColumn('agenda_resolution', 'resolution_id', 'ผลการพิจารณา');
        $this->addCommentOnColumn('agenda_resolution', 'deleted', '0=ใช้งาน,1=ไม่ใช้งาน');
        $this->addCommentOnColumn('agenda_resolution', 'created_by', 'สร้างโดย');
        $this->addCommentOnColumn('agenda_resolution', 'created_at', 'สร้างเมื่อ');
        $this->addCommentOnColumn('agenda_resolution', 'updated_by', 'ปรับปรุงโดย');
        $this->addCommentOnColumn('agenda_resolution', 'updated_at', 'ปรับปรุงเมื่อ');

        $this->addForeignKey('fk_agenda_resolution_agenda_id', 'agenda_resolution', 'agenda_id', 'agenda', 'id');
        $this->addForeignKey('fk_agenda_resolution_resolution_id', 'agenda_resolution', 'resolution_id', 'resolution', 'id');
        $this->addForeignKey('fk_agenda_resolution_created_by', 'agenda_resolution', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_agenda_resolution_updated_by', 'agenda_resolution', 'updated_by', 'user', 'id');

        // วาระ 3
        $this->insert('agenda_resolution', ['agenda_id' => 6, 'resolution_id' => 7]);
        $this->insert('agenda_resolution', ['agenda_id' => 6, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 6, 'resolution_id' => 9]);

        $this->insert('agenda_resolution', ['agenda_id' => 7, 'resolution_id' => 7]);
        $this->insert('agenda_resolution', ['agenda_id' => 7, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 7, 'resolution_id' => 9]);

        $this->insert('agenda_resolution', ['agenda_id' => 8, 'resolution_id' => 11]);
        $this->insert('agenda_resolution', ['agenda_id' => 8, 'resolution_id' => 12]);

        $this->insert('agenda_resolution', ['agenda_id' => 9, 'resolution_id' => 7]);
        $this->insert('agenda_resolution', ['agenda_id' => 9, 'resolution_id' => 13]);

        $this->insert('agenda_resolution', ['agenda_id' => 10, 'resolution_id' => 14]);
        $this->insert('agenda_resolution', ['agenda_id' => 10, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 10, 'resolution_id' => 24]);

        $this->insert('agenda_resolution', ['agenda_id' => 11, 'resolution_id' => 7]);
        $this->insert('agenda_resolution', ['agenda_id' => 11, 'resolution_id' => 15]);

        $this->insert('agenda_resolution', ['agenda_id' => 20, 'resolution_id' => 7]);
        $this->insert('agenda_resolution', ['agenda_id' => 20, 'resolution_id' => 13]);

        $this->insert('agenda_resolution', ['agenda_id' => 21, 'resolution_id' => 7]);
        $this->insert('agenda_resolution', ['agenda_id' => 21, 'resolution_id' => 13]);

        $this->insert('agenda_resolution', ['agenda_id' => 22, 'resolution_id' => 14]);
        $this->insert('agenda_resolution', ['agenda_id' => 22, 'resolution_id' => 8]);

        //วาระ 4
        $this->insert('agenda_resolution', ['agenda_id' => 12, 'resolution_id' => 18]);
        $this->insert('agenda_resolution', ['agenda_id' => 12, 'resolution_id' => 13]);
        $this->insert('agenda_resolution', ['agenda_id' => 12, 'resolution_id' => 19]);
        $this->insert('agenda_resolution', ['agenda_id' => 12, 'resolution_id' => 20]);

        $this->insert('agenda_resolution', ['agenda_id' => 13, 'resolution_id' => 18]);
        $this->insert('agenda_resolution', ['agenda_id' => 13, 'resolution_id' => 13]);
        $this->insert('agenda_resolution', ['agenda_id' => 13, 'resolution_id' => 19]);
        $this->insert('agenda_resolution', ['agenda_id' => 13, 'resolution_id' => 20]);

        $this->insert('agenda_resolution', ['agenda_id' => 14, 'resolution_id' => 18]);
        $this->insert('agenda_resolution', ['agenda_id' => 14, 'resolution_id' => 13]);
        $this->insert('agenda_resolution', ['agenda_id' => 14, 'resolution_id' => 19]);
        $this->insert('agenda_resolution', ['agenda_id' => 14, 'resolution_id' => 20]);

        $this->insert('agenda_resolution', ['agenda_id' => 15, 'resolution_id' => 18]);
        $this->insert('agenda_resolution', ['agenda_id' => 15, 'resolution_id' => 13]);
        $this->insert('agenda_resolution', ['agenda_id' => 15, 'resolution_id' => 19]);
        $this->insert('agenda_resolution', ['agenda_id' => 15, 'resolution_id' => 20]);

        $this->insert('agenda_resolution', ['agenda_id' => 16, 'resolution_id' => 18]);
        $this->insert('agenda_resolution', ['agenda_id' => 16, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 16, 'resolution_id' => 22]);

        $this->insert('agenda_resolution', ['agenda_id' => 17, 'resolution_id' => 18]);
        $this->insert('agenda_resolution', ['agenda_id' => 17, 'resolution_id' => 13]);
        $this->insert('agenda_resolution', ['agenda_id' => 17, 'resolution_id' => 19]);
        $this->insert('agenda_resolution', ['agenda_id' => 17, 'resolution_id' => 21]);

        $this->insert('agenda_resolution', ['agenda_id' => 18, 'resolution_id' => 14]);
        $this->insert('agenda_resolution', ['agenda_id' => 18, 'resolution_id' => 25]);
        $this->insert('agenda_resolution', ['agenda_id' => 18, 'resolution_id' => 22]);


        $this->insert('agenda_resolution', ['agenda_id' => 23, 'resolution_id' => 14]);
        $this->insert('agenda_resolution', ['agenda_id' => 23, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 23, 'resolution_id' => 22]);


        $this->insert('agenda_resolution', ['agenda_id' => 24, 'resolution_id' => 14]);
        $this->insert('agenda_resolution', ['agenda_id' => 24, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 24, 'resolution_id' => 22]);

        $this->insert('agenda_resolution', ['agenda_id' => 25, 'resolution_id' => 14]);
        $this->insert('agenda_resolution', ['agenda_id' => 25, 'resolution_id' => 8]);
        $this->insert('agenda_resolution', ['agenda_id' => 25, 'resolution_id' => 22]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown() {
        $this->dropColumn('submission', 'issue1_eng');
    }

}
