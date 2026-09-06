<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SubmissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = isset($pageTitle) ? $pageTitle : Yii::t('app', 'เลือกประเภทการพิจารณาโครงการ');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="submission-index">
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable-president-select-committee',
            'dataProvider' => $dataProvider,
            'floatHeader' => true,
            'floatHeaderOptions' => ['top' => 66],
            'pjax' => true,
            'columns' => [
                [
                    'class' => 'kartik\\grid\\SerialColumn',
                    'width' => '30px',
                ],
                [
                    'label' => Yii::t('app', 'เลขที่โครงการ'),
                    'value' => function ($model) {
                        return $model->project->project_code ?: $model->submission_number;
                    },
                ],
                [
                    'label' => Yii::t('app', 'ชื่อโครงการวิจัย'),
                    'value' => function ($model) {
                        return $model->project->name_thai;
                    },
                ],
                [
                    'label' => Yii::t('app', 'ประเภทโครงการ'),
                    'value' => function ($model) {
                        return $model->submissionType->name;
                    },
                ],
                [
                    'class' => 'kartik\\grid\\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a(
                                '<i class="glyphicon glyphicon-edit"></i> ' . Yii::t('app', 'เลือกโครงการ'),
                                ['submission/project-submission', 'submissionId' => $model->id],
                                ['data-pjax' => 0, 'data-toggle' => 'tooltip', 'class' => 'btn btn-primary btn-raised']
                            );
                        },
                    ],
                ],
            ],
            'toolbar' => [
                [
                    'content' => Html::a(
                        '<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('app', 'โหลดใหม่'),
                        Url::current(),
                        ['data-pjax' => 1, 'class' => 'btn btn-default btn-raised']
                    ),
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => false,
                'before' => '<div class="pull-left">{summary}</div>',
            ],
            'pager' => [
                'firstPageLabel' => '<i class="icon md-skip-previous"></i>',
                'lastPageLabel' => '<i class="icon md-skip-next"></i>',
                'prevPageLabel' => '<i class="icon md-fast-rewind"></i>',
                'nextPageLabel' => '<i class="icon md-fast-forward"></i>',
            ],
        ]); ?>
    </div>
</div>
