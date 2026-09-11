<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $submission app\models\Submission */
/* @var $committees app\models\SubmissionCommittee[] */
?>
<div class="submission-committee-confirm">
    <?php if (empty($committees)): ?>
        <p class="text-muted"><?= Yii::t('app', 'ไม่มีกรรมการที่รอการยืนยัน กรรมการทุกท่านได้รับการแจ้งเตือนแล้ว') ?></p>
    <?php else: ?>
        <p>
            <?= Yii::t('app', 'ท่านเลือกกรรมการทั้งหมด {count} ท่าน กรุณาตรวจสอบรายชื่อก่อนยืนยัน เมื่อกด "ยืนยันและส่งอีเมล" ระบบจะส่งอีเมลแจ้งกรรมการทันที', ['count' => count($committees)]) ?>
        </p>
        <?php $form = ActiveForm::begin(); ?>
        <ol>
            <?php foreach ($committees as $committee): ?>
                <li>
                    <?= Html::encode($committee->person->fullName) ?>
                    <?php if (isset($committee->committeePosition)): ?>
                        — <?= Html::encode($committee->committeePosition->fullName) ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>
