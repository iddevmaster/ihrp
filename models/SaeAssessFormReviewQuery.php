<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[SaeAssessFormReview]].
 *
 * @see SaeAssessFormReview
 */
class SaeAssessFormReviewQuery extends \yii\db\ActiveQuery {
    /* public function active()
      {
      return $this->andWhere('[[status]]=1');
      } */

    /**
     * {@inheritdoc}
     * @return SaeAssessFormReview[]|array
     */
    public function all($db = null) {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SaeAssessFormReview|array|null
     */
    public function one($db = null) {
        return parent::one($db);
    }

    public function isDeleted($deleted = TRUE) {
        return $this->andWhere(['sae_assess_form_review.deleted' => $deleted]);
    }
    
    public function saeAssessForm($formId) {
        return $this->andWhere(['sae_assess_form_review.sae_assess_form_id' => $formId]);
    }
    
    public function reviewChoice($reviewId) {
        return $this->andWhere(['sae_assess_form_review.review_choice_id' => $reviewId]);
    }
}
