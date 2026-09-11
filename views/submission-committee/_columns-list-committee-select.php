<?php

use yii\helpers\Url;
use app\models\Person;
use yii\helpers\Html;

$currentRole = Yii::$app->session->get('currentRole');

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'person.fullName',
        'format' => 'raw',
        'value' => function($model) use ($currentRole) {
            $mobile = isset($model->person->mobile_no) ? $model->person->mobile_no : "N/A";
            $email = isset($model->person->email) ? $model->person->email : "N/A";
            if (in_array($currentRole['role_id'], [\app\models\Role::STAFF, \app\models\Role::SECRETARY, \app\models\Role::ADMIN])) {
                $html = $model->person->i18nFullName . "<br>Mobile : " . $mobile . "<br>Email : " . $email;
            } else {
                $html = $model->person->i18nFullName;
            }
            if ($currentRole['role_id'] == \app\models\Role::PRESIDENT && $model->submission->status < \app\models\Submission::STATUS_AGENDA_ADDED) {
                $html .= '<br>' . Html::a(Yii::t('app', 'ลบ'), ['submission-committee/delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-xs',
                    'role' => 'modal-remote',
                    'title' => Yii::t('app', 'ลบ'),
                    'data-confirm' => false,
                    'data-method' => false,
                    'data-request-method' => 'post',
                    'data-toggle' => 'tooltip',
                ]);
            }
            return $html;
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'committeePosition.fullName',
//                'value' => function($model) {
//            if (isset($model->committee_position_id)) {
//                return $model->comitteePosition->fullName;
//            } else {
//                return 'no';
//            }
//        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'status',
        'value' => function($model) {
            if (isset($model->status)) {
                return app\models\SubmissionCommittee::getStatusLabels()[$model->status];
            } else {
                return 'ยังไม่มีการตอบรับ';
            }
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'can_meeting',
        'value' => function($model) {
            if (isset($model->can_meeting) && $model->status != \app\models\SubmissionCommittee::STATUS_PENDING) {
                return app\models\SubmissionCommittee::getStatusLabelsCanMeeting()[$model->can_meeting];
            } else {
                return 'ยังไม่มีการตอบรับ';
            }
        }
    ], [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'remark',
        'value' => function($model) {
            if (isset($model->remark)) {
                return $model->remark;
            } else {
                return '';
            }
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'remark_meeting',
        'value' => function($model) {
            if (isset($model->remark_meeting)) {
                return $model->remark_meeting;
            } else {
                return '';
            }
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'noWrap' => true,
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'template' => '  {committeeAccepted}{notifyStatus}',
        'controller' => 'submission-committee',
//        'urlCreator' => function($action, $model, $key, $index) {
//                return Url::to([$action,'id'=>$key]);
//        },
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip'],
        // 'template' => '{select} {delete}',
        'buttons' => [
            'select' => function($url, $model) {
                $options = ['role' => 'modal-remote', 'title' => 'เลือก', 'data-toggle' => 'tooltip'];
                return Html::a('<i class="icon md-edit font-size-18"></i>', ['submission-committee/select-committees', 'id' => $model->id, 'personId' => $model->person->id, 'submissionId' => $model->submission_id], $options);
            },
            'committeeAccepted' => function($url, $model) {
                return Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'ตอบรับการอ่านงานวิจัย'), ['submission/update' , 'id' => $model->submission_id , 'projectId' => $model->project_id,'committeeId' => $model->id, 'mode' => \app\models\Submission::MODE_ACCEPTCOMMITTEE], ['role' => 'modal-remote', 'data-toggle' => 'tooltip']) . '<br>';
            },
            'notifyStatus' => function($url, $model) {
                $notified = app\models\EmailQueue::find()->isDeleted(FALSE)
                        ->type(app\models\EmailQueue::TYPE_COMMITTEE_ACK)
                        ->modelId($model->id)->exists();
                if ($notified) {
                    return '';
                }
                return Html::tag('span', Yii::t('app', 'รอยืนยันการเลือกกรรมการ'), ['class' => 'label label-warning', 'style' => 'font-size:14px;']);
            },
        ],
        'visibleButtons' => [
            'select' => function($model) use ($currentRole) {
                return $currentRole['role_id'] == \app\models\Role::PRESIDENT;
            },
            'committeeAccepted' => function($model)use ($currentRole) {
                return $model->status == app\models\SubmissionCommittee::STATUS_PENDING && ($currentRole['role_id'] == \app\models\Role::STAFF || $currentRole['role_id'] == \app\models\Role::ADMIN );
            },
        ],
    ],
];
