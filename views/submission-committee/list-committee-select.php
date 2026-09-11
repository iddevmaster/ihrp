<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RegisterGroupPersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $submissionId int|null */
/* @var $submission app\models\Submission|null */

//$this->title = Yii::t('app', 'กำหนดผู้เข้าร่วมประชุมตามรอบ');

//CrudAsset::register($this);

$currentRole = Yii::$app->session->get('currentRole');
$canConfirm = isset($submissionId) && isset($submission)
        && $currentRole['role_id'] == \app\models\Role::PRESIDENT
        && $submission->status < \app\models\Submission::STATUS_AGENDA_ADDED;

?>
<div class="submission-committee-index">

    <div class="ajaxCrudDatatable panel panel-default">
        <?=GridView::widget([
            'id'=>'crud-datatable-submission-committee',
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'floatHeader' => true,
            'floatHeaderOptions' => ['top' => 66],
            'toolbar' => [
                ['content' =>
                    ($canConfirm ? Html::a('<i class="glyphicon glyphicon-ok"></i> ' . Yii::t('app', 'ยืนยันการเลือกกรรมการ'), ['submission-committee/confirm-committees', 'submissionId' => $submissionId], ['role' => 'modal-remote', 'class' => 'btn btn-primary', 'title' => 'ยืนยันการเลือกกรรมการ', 'data-toggle' => 'tooltip']) : '') .
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', Url::current(), ['data-pjax' => 1, 'class' => 'btn btn-default grey-600', 'title' => 'โหลดใหม่'])
//                    '{toggleData}' .
//                    '{export}'
                ],
            ],
            'toggleDataOptions' => [
                'all' => [
                    'icon' => 'resize-full',
                    'class' => 'btn btn-default grey-600',
                ],
                'page' => [
                    'icon' => 'resize-small',
                    'class' => 'btn btn-default grey-600',
                ],
            ],
//            'panelBeforeTemplate' => '
//                        <div class="pull-left">{summary}</div>
//                        <div class="pull-right">
//                            <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
//                                {toolbar}
//                            </div>    
//                        </div>
//                        {before}
//                        <div class="clearfix"></div>',
            'panel' => [
                'after' => FALSE,
                'heading' => '<i class="glyphicon glyphicon-list"></i> รายชื่อกรรมการที่ถูกเลือกอ่านงานวิจัยในโครงการนี้',
//                'beforeOptions' => [
//                    'class' => 'kv-panel-before bg-primary',
//                ],
            ],
            'pager' => array(
                'firstPageLabel' => '<i class="icon md-skip-previous"></i>',
                'lastPageLabel' => '<i class="icon md-skip-next"></i>',
                'prevPageLabel' => '<i class="icon md-fast-rewind"></i>',
                'nextPageLabel' => '<i class="icon md-fast-forward"></i>',
            ),
            'exportConfig' => [
                GridView::EXCEL => true,
            ],
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'clientOptions' => ['skipOuterContainers' => true]
                ],
            ],
            'columns' => require(__DIR__ . '/_columns-list-committee-select.php'),
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
        ])
        ?>
    </div>
</div>