<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;
use app\models\SaeAssessForm;
use yii\helpers\ArrayHelper;
use app\models\Submission;
use app\models\Resolution;
use yii\helpers\Url;
use app\models\ReviewChoice;

/* @var $this yii\web\View */
/* @var $model app\models\SaeAssessForm */
/* @var $form yii\widgets\ActiveForm */
$reviewChoicesByType = ArrayHelper::index($reviewChoices, null, 'type');

$currentRole = \Yii::$app->session->get('currentRole');
?>

<div class="sae-assess-form-form">

    <?php
    $form = ActiveForm::begin([
//                'layout' => 'inline',
                'id' => 'submission-type-assess-form',
                'enableClientValidation' => false,
                'action' => Url::to(['sae-assess-form/create', 'submissionId' => $model->submission_id, 'submissionCommitteeId' => $model->submission_committee_id]),
    ]);
    ?>
    <?= $this->renderFile('@app/views/widgets/_alert.php'); ?>

    <table class="table table-condensed table-bordered">
        <tbody>
            <tr>
                <td class="text-center font-weight-900" style="background-color: #DCDCDC;"><?= Yii::t('app', 'ข้อสรุปของกรรมการ') ?></td>
            </tr>
            <tr>
                <td  class="text-center font-weight-900"><?= $form->field($model, 'suggestion')->label(false)->textarea(['rows' => 4]) ?></td>
            </tr>
            <tr>
                <td class="text-center font-weight-900" style="background-color: #DCDCDC;"><?= Yii::t('app', 'ข้อคิดเห็นของกรรมการ') ?></td>
            </tr>
            <?php if ($model->submission->submission_type_id == ReviewChoice::TYPE_RENEW) { ?>
                <?php if ($agendaCheck->agenda->name == "3.4") { ?>
                    <tr>
                        <td >
                            <?php
                            if ($model->submission->submission_type_id == ReviewChoice::TYPE_RENEW) {
                                $typeId = $model->submission->submission_type_id ?? null;
                                $list = (is_scalar($typeId) && isset($reviewChoicesByType[$typeId])) ? $reviewChoicesByType[$typeId] : [];
                                $choices = array_filter($list, function ($c) {
                                    return $c->type_ref == 1;
                                });
                            } else {
                                $choices = $reviewChoicesByType[$model->submission->submission_type_id];
                            }
                            echo $form->field($model, 'review_choice_id')->label(false)->radioList(ArrayHelper::map($choices, 'id', 'name'), [
                                'unselect' => NULL,
                                'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                                    $id = "review_choice_id-{$value}";
                                    $res = '';
                                    $style = '';
                                    $rc = ReviewChoice::findOne($value);
//                            if ($value == BioForm::TOS_OTHERS || $value == BioForm::TOS_CLINICAL_TRIAL) {
//                                $res .= '<br>';
//                                $label .= "<i class='margin-left-20'>Please specify</i>";
//                                $style = "display: table-cell !important;white-space: nowrap;";
//                            }
                                    $res .= Html::tag('div', Html::radio($name, $checked, [
                                                        'id' => $id,
                                                        'value' => $value
                                                    ]) . Html::label($label, $id, ['class' => 'padding-right-20']), [
                                                'class' => "radio-custom radio-primary",
                                                'style' => $style,
                                    ]);
                                    if ($rc->need_text) {
                                        $res .= $form->field($model, 'review_choice_text', [
                                                    'options' => [
//                                                'style' => $style . "width: 100%;"
                                                    ]
                                                ])->label(false)->textInput();
                                    }
                                    if (count($rc->children) > 0) {
                                        $res .= $form->field($model, 'reviewIds', [
                                                    'options' => [
                                                        'class' => 'margin-left-20'
//                                                'style' => $style . "width: 100%;"
                                                    ]
                                                ])->label(false)->checkboxList(ArrayHelper::map($rc->children, 'id', 'name'), [
                                            'unselect' => NULL,
                                            'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                                                $id = "review-choice-{$value}";
                                                return Html::tag('div', Html::checkbox($name, $checked, [
                                                                    'id' => $id,
                                                                    'value' => $value,
                                                                ]) . Html::label($label, $id, ['class' => 'padding-right-20']), [
                                                            'class' => "checkbox-custom checkbox-primary"
                                                ]);
                                            }
                                        ]);
                                    }
                                    return $res;
                                }
                            ]);
                            ?>
                        </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td>
                            <?php
                            if ($model->submission->submission_type_id == ReviewChoice::TYPE_RENEW) {
                                $typeId = $model->submission->submission_type_id ?? null;
                                $list = (is_scalar($typeId) && isset($reviewChoicesByType[$typeId])) ? $reviewChoicesByType[$typeId] : [];
                                $choices = array_filter($list, function ($c) {
                                    return $c->type_ref == 2;
                                });
                            } else {
                                $choices = $reviewChoicesByType[$model->submission->submission_type_id];
                            }
                            echo $form->field($model, 'review_choice_id')->label(false)->radioList(ArrayHelper::map($choices, 'id', 'name'), [
                                'unselect' => NULL,
                                'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                                    $id = "review_choice_id-{$value}";
                                    $res = '';
                                    $style = '';
                                    $rc = ReviewChoice::findOne($value);
//                            if ($value == BioForm::TOS_OTHERS || $value == BioForm::TOS_CLINICAL_TRIAL) {
//                                $res .= '<br>';
//                                $label .= "<i class='margin-left-20'>Please specify</i>";
//                                $style = "display: table-cell !important;white-space: nowrap;";
//                            }
                                    $res .= Html::tag('div', Html::radio($name, $checked, [
                                                        'id' => $id,
                                                        'value' => $value
                                                    ]) . Html::label($label, $id, ['class' => 'padding-right-20']), [
                                                'class' => "radio-custom radio-primary",
                                                'style' => $style,
                                    ]);
                                    if ($rc->need_text) {
                                        $res .= $form->field($model, 'review_choice_text', [
                                                    'options' => [
//                                                'style' => $style . "width: 100%;"
                                                    ]
                                                ])->label(false)->textInput();
                                    }
                                    if (count($rc->children) > 0) {
                                        $res .= $form->field($model, 'reviewIds', [
                                                    'options' => [
                                                        'class' => 'margin-left-20'
//                                                'style' => $style . "width: 100%;"
                                                    ]
                                                ])->label(false)->checkboxList(ArrayHelper::map($rc->children, 'id', 'name'), [
                                            'unselect' => NULL,
                                            'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                                                $id = "review-choice-{$value}";
                                                return Html::tag('div', Html::checkbox($name, $checked, [
                                                                    'id' => $id,
                                                                    'value' => $value,
                                                                ]) . Html::label($label, $id, ['class' => 'padding-right-20']), [
                                                            'class' => "checkbox-custom checkbox-primary"
                                                ]);
                                            }
                                        ]);
                                    }
                                    return $res;
                                }
                            ]);
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            <?Php } else { ?>
                <tr>
                    <td>
                        <?php
                        echo $form->field($model, 'review_choice_id')->label(false)->radioList(ArrayHelper::map($reviewChoicesByType[$model->submission->submission_type_id], 'id', 'name'), [
                            'unselect' => NULL,
                            'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                                $id = "review_choice_id-{$value}";
                                $res = '';
                                $style = '';
                                $rc = ReviewChoice::findOne($value);
//                            if ($value == BioForm::TOS_OTHERS || $value == BioForm::TOS_CLINICAL_TRIAL) {
//                                $res .= '<br>';
//                                $label .= "<i class='margin-left-20'>Please specify</i>";
//                                $style = "display: table-cell !important;white-space: nowrap;";
//                            }
                                $res .= Html::tag('div', Html::radio($name, $checked, [
                                                    'id' => $id,
                                                    'value' => $value
                                                ]) . Html::label($label, $id, ['class' => 'padding-right-20']), [
                                            'class' => "radio-custom radio-primary",
                                            'style' => $style,
                                ]);
                                if ($rc->need_text) {
                                    $res .= $form->field($model, 'review_choice_text', [
                                                'options' => [
//                                                'style' => $style . "width: 100%;"
                                                ]
                                            ])->label(false)->textInput();
                                }
                                if (count($rc->children) > 0) {
                                    $res .= $form->field($model, 'reviewIds', [
                                                'options' => [
                                                    'class' => 'margin-left-20'
//                                                'style' => $style . "width: 100%;"
                                                ]
                                            ])->label(false)->checkboxList(ArrayHelper::map($rc->children, 'id', 'name'), [
                                        'unselect' => NULL,
                                        'item' => function ($index, $label, $name, $checked, $value) use ($model, $form) {
                                            $id = "review-choice-{$value}";
                                            return Html::tag('div', Html::checkbox($name, $checked, [
                                                                'id' => $id,
                                                                'value' => $value,
                                                            ]) . Html::label($label, $id, ['class' => 'padding-right-20']), [
                                                        'class' => "checkbox-custom checkbox-primary"
                                            ]);
                                        }
                                    ]);
                                }
                                return $res;
                            }
                        ]);
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php if (($currentRole['role_id'] == \app\models\Role::STAFF || $currentRole['role_id'] == \app\models\Role::ADMIN) || ($currentRole['role_id'] == \app\models\Role::COMMITTEE && $model->submissionCommittee->status == app\models\SubmissionCommittee::STATUS_ACCEPTED)) { ?>
        <div class="form-group margin-15">
            <?= Html::submitButton(Yii::t('app', 'บันทึก'), ['class' => 'btn btn-primary btn-assess-form-save']) ?>

            <?php if (isset($model->id) && isset($model->resolution_id)) { ?>
                <a href="<?= Url::to(['sae-assess-form/print-pdf', 'id' => $model->id]) ?>" data-pjax="0" style="text-decoration: none" target="_blank">
                    <button type="button" class="btn btn-default"><i class="icon wb-print" aria-hidden="true"></i> <?= Yii::t('app', 'พิมพ์ฟอร์ม') ?></button>
                </a>
            <?php } ?>
        </div>
    <?php } ?>


    <?php ActiveForm::end(); ?>

</div>
