<?php

use yii\helpers\Html;

/* @var $submission app\models\Submission */
/* @var $index int */

$id = $submission->id;
$project = $submission->project;
?>

<?php // Main row ?>
<tr class="submission-row" data-submission-id="<?= $id ?>">
    <td>
        <strong><?= Html::encode($submission->project->project_code) ?> <?php if (isset($submission->submission_number)) { ?><br>(<?= Html::encode($submission->submission_number) ?>)<?php } ?></strong>
    </td>
    <td>
        <?= Html::encode($submission->submissionTypeName) ?>
    </td>
    <td>
        <?= Html::encode($submission->resolution) ?>
    </td>
    <td style="text-align:center;">
        <?=
        Html::a(
                '<i class="icon wb-eye"></i>',
                ['submission/project-submission', 'submissionId' => $id],
                ['target' => '_blank', 'data-pjax' => 0, 'title' => Yii::t('app', 'ดูรายละเอียดโครงการ')]
        )
        ?>
    </td>
    <td style="text-align:center;">
        <a href="javascript:void(0)" class="btn-toggle-detail" data-target="detail-<?= $id ?>">
            <i class="icon md-chevron-down"></i>
        </a>
    </td>
    <td style="text-align:center;">
        <label class="checkbox-inline" style="color:#5cb85c;">
            <input type="checkbox" name="decisions[<?= $id ?>][action]" value="approve" class="decision-checkbox" data-sid="<?= $id ?>">
            <?= Yii::t('app', 'อนุมัติ') ?>
        </label>
    </td>
</tr>

<?php // Detail row (hidden by default)  ?>
<tr id="detail-<?= $id ?>" class="detail-row" style="display:none; background-color:#f9f9f9;">
    <td colspan="6">
        <div style="padding: 10px 15px;">
            <div class="row">
                <div class="col-md-12">
                    <span class="label label-primary">TH</span> <strong><?= Yii::t('app', 'ชื่อไทย') ?>:</strong>
                    <?= Html::encode($project->name_thai ?? '-') ?>
                </div>
            </div>
            <div class="row" style="margin-top: 5px;">
                <div class="col-md-12">
                    <span class="label label-info">GB</span> <strong>Eng:</strong>
                    <?= Html::encode($project->name_eng ?? '-') ?>
                </div>
            </div>
            <div class="row" style="margin-top: 8px;">
                <div class="col-md-12">
                    <?=
                    Html::a(
                            '<i class="icon md-link"></i> ' . Yii::t('app', 'ไปยังหน้าโครงการ (Go to Project)'),
                            ['submission/project-submission', 'submissionId' => $id],
                            ['target' => '_blank', 'data-pjax' => 0, 'class' => 'text-primary']
                    )
                    ?>
                </div>
            </div>
        </div>
    </td>
</tr>
