<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[AgendaSubmissionType]].
 *
 * @see AgendaResolutionQuery
 */
class AgendaResolutionQuery extends \yii\db\ActiveQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * @inheritdoc
     * @return AgendaResolutionQuery[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AgendaResolutionQuery|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

    public function isDeleted($deleted = TRUE) {
        return $this->andWhere(['agenda_resolution.deleted' => $deleted]);
    }

    public function agenda($agendaId) {
        return $this->andWhere(['agenda_resolution.agenda_id' => $agendaId]);
    }
    
        public function resolution($resolutionId) {
        return $this->andWhere(['agenda_resolution.resolution_id' => $resolutionId]);
    }
}
