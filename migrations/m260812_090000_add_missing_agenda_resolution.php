<?php

use yii\db\Migration;

/**
 * Class m260812_090000_add_missing_agenda_resolution
 *
 * Completes the agenda_resolution rows that m260708_141602_add_agenda_resolution
 * was supposed to insert but never did (that migration stopped after agenda_id=21,
 * before the agenda_id 12-18 "full board" batch — so those agenda types had zero
 * selectable resolutions in the "มติที่ประชุม" dropdown).
 *
 * The source migration also had rows for agenda_id 22-25, but this database's
 * `agenda` table only goes up to id=21 (those agenda templates don't exist here),
 * so those rows are omitted — inserting them would violate the
 * fk_agenda_resolution_agenda_id foreign key.
 *
 * Only inserts agenda_resolution rows; does not touch table/column structure or
 * the resolution table itself.
 */
class m260812_090000_add_missing_agenda_resolution extends Migration
{
    private $rows = [
        ['agenda_id' => 12, 'resolution_id' => 18],
        ['agenda_id' => 12, 'resolution_id' => 13],
        ['agenda_id' => 12, 'resolution_id' => 19],
        ['agenda_id' => 12, 'resolution_id' => 20],

        ['agenda_id' => 13, 'resolution_id' => 18],
        ['agenda_id' => 13, 'resolution_id' => 13],
        ['agenda_id' => 13, 'resolution_id' => 19],
        ['agenda_id' => 13, 'resolution_id' => 20],

        ['agenda_id' => 14, 'resolution_id' => 18],
        ['agenda_id' => 14, 'resolution_id' => 13],
        ['agenda_id' => 14, 'resolution_id' => 19],
        ['agenda_id' => 14, 'resolution_id' => 20],

        ['agenda_id' => 15, 'resolution_id' => 18],
        ['agenda_id' => 15, 'resolution_id' => 13],
        ['agenda_id' => 15, 'resolution_id' => 19],
        ['agenda_id' => 15, 'resolution_id' => 20],

        ['agenda_id' => 16, 'resolution_id' => 18],
        ['agenda_id' => 16, 'resolution_id' => 8],
        ['agenda_id' => 16, 'resolution_id' => 22],

        ['agenda_id' => 17, 'resolution_id' => 18],
        ['agenda_id' => 17, 'resolution_id' => 13],
        ['agenda_id' => 17, 'resolution_id' => 19],
        ['agenda_id' => 17, 'resolution_id' => 21],

        ['agenda_id' => 18, 'resolution_id' => 14],
        ['agenda_id' => 18, 'resolution_id' => 25],
        ['agenda_id' => 18, 'resolution_id' => 22],
    ];

    public function safeUp()
    {
        foreach ($this->rows as $row) {
            $exists = (new \yii\db\Query())
                ->from('agenda_resolution')
                ->where($row)
                ->andWhere(['deleted' => 0])
                ->exists();
            if (!$exists) {
                $this->insert('agenda_resolution', $row);
            }
        }
    }

    public function safeDown()
    {
        foreach ($this->rows as $row) {
            $this->delete('agenda_resolution', $row);
        }
    }
}
