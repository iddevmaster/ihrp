<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $submissions app\models\Submission[] */

?>

<div class="secretary-approve-result-documents">

    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-8">
            <h3 style="margin-top:0;">
                <?= Yii::t('app', 'หนังสือแจ้งผลรอเลขาฯตรวจสอบ') ?> <?= Yii::t('app', 'รายการทั้งหมด') ?> <?= count($submissions) ?> <?= Yii::t('app', 'รายการ') ?>
            </h3>
        </div>
        <div class="col-md-4 text-right">
            <button type="button" id="btn-approve-all" class="btn btn-success btn-raised">
                <i class="icon md-check-all"></i> <?= Yii::t('app', 'อนุมัติทั้งหมด (Approve All)') ?>
            </button>
        </div>
    </div>

    <?php
    $form = ActiveForm::begin([
                'id' => 'secretary-bulk-approve-form',
                'action' => Url::to(['submission/secretary-approve-result-documents']),
                'method' => 'post',
    ]);
    ?>

    <div class="panel panel-info" style="border-left: 4px solid #5bc0de;">
        <div class="panel-heading">
            <h4 class="panel-title" style="margin:0;">
                <i class="icon md-file-text"></i>
                <?= Yii::t('app', 'หนังสือแจ้งผลรอเลขาฯตรวจสอบ') ?>
                <span class="badge badge-info"><?= count($submissions) ?> รายการ</span>
            </h4>
        </div>
        <div class="panel-body" style="padding:0;">
            <?php if (count($submissions) > 0): ?>
                <table class="table table-striped table-condensed table-hover" style="margin-bottom:0;">
                    <thead>
                        <tr style="background-color: #f5f5f5;">
                            <th style="width:15%"><?= Yii::t('app', 'CODE') ?></th>
                            <th style="width:30%"><?= Yii::t('app', 'ประเภทการยื่นรายงาน') ?></th>
                            <th style="width:10%"><?= Yii::t('app', 'ผลการพิจารณา') ?></th>
                            <th style="width:10%; text-align:center;"><?= Yii::t('app', 'รายละเอียดโครงการ') ?><br><?= Yii::t('app', 'คลิก ที่ Icon ') ?><i class="icon wb-eye"></i></th>
                            <th style="width:10%; text-align:center;"><?= Yii::t('app', 'รายละเอียด') ?><br><?= Yii::t('app', 'คลิก ที่ Icon ') ?><i class="icon md-chevron-down"></i></th>
                            <th style="width:25%; text-align:center;"><?= Yii::t('app', 'เลือกเพื่ออนุมัติ') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $index => $submission): ?>
                            <?=
                            $this->render('_secretary-approve-row', [
                                'submission' => $submission,
                                'index' => $index,
                            ])
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-muted" style="padding: 15px;"><?= Yii::t('app', 'ไม่มีเอกสารรออนุมัติ') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div id="sticky-footer" style="position:fixed; bottom:0; left:0; right:0; background:#fff; border-top:2px solid #ccc; padding:15px 30px; z-index:1000; box-shadow: 0 -2px 5px rgba(0,0,0,0.1);">
        <div class="row">
            <div class="col-md-6" style="padding-top: 8px;">
                <span id="decision-summary" style="font-size: 14px;">
                    เลือกแล้ว: <strong id="count-approve" style="color:#5cb85c;">0</strong> อนุมัติ
                </span>
            </div>
            <div class="col-md-6 text-right">
                <a href="<?= Url::to(['site/index']) ?>" class="btn btn-default btn-raised btn-lg" style="margin-right: 10px;">
                    <?= Yii::t('app', 'ยกเลิก') ?>
                </a>
                <?=
                Html::submitButton(
                        '<i class="icon md-check"></i> ' . Yii::t('app', 'ยืนยันการอนุมัติ'),
                        [
                            'class' => 'btn btn-success btn-raised btn-lg',
                            'id' => 'btn-submit-decisions',
                        ]
                )
                ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div style="height: 80px;"></div>

<?php
$js = <<<'JS'

$(document).on('click', '.btn-toggle-detail', function(e) {
    e.preventDefault();
    var target = $(this).data('target');
    var row = $('#' + target);
    var icon = $(this).find('i');
    if (row.css('display') === 'none') {
        row.css('display', 'table-row');
        icon.removeClass('md-chevron-down').addClass('md-chevron-up');
    } else {
        row.css('display', 'none');
        icon.removeClass('md-chevron-up').addClass('md-chevron-down');
    }
});

$(document).on('change', '.decision-checkbox', function() {
    updateSummary();
});

$('#btn-approve-all').on('click', function() {
    $('input.decision-checkbox').prop('checked', true);
    updateSummary();
});

function updateSummary() {
    var approveCount = $('input.decision-checkbox:checked').length;
    $('#count-approve').text(approveCount);
}

$('#secretary-bulk-approve-form').on('beforeSubmit', function(e) {
    var approveCount = $('input.decision-checkbox:checked').length;
    if (approveCount === 0) {
        alert('กรุณาเลือกอย่างน้อย 1 รายการ');
        return false;
    }
    return confirm('ยืนยันการอนุมัติ ' + approveCount + ' รายการ?');
});

updateSummary();

JS;

$this->registerJs($js);
?>
