<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = yii::t('app', 'เข้าสู่ระบบ');
$this->params['breadcrumbs'][] = $this->title;
//\yii\helpers\VarDumper::dump(Yii::$app->session->get('currentRole'));
?>
<style>
    .page-login-v3 .panel {
        width: 500px;
        margin-bottom: 45px;
        background: #fff;
        border-radius: 4px;
    }
</style>
<div class="panel whiteframe-12dp">
    <div class="panel-body text-left">
        <div class="brand">
            <div class="text-center" style="display: flex; align-items: center; justify-content: center; gap: 20px;">
                <?= Html::img('@web/images/logo02.png', ['style' => 'height: 80px; width: auto;']); ?>
                <?= Html::img('@web/images/logo03.png', ['style' => 'height: 80px; width: auto;']); ?>
            </div>
            <div class="brand-text font-size-18 text-center text-primary"><?= yii::t('app', 'e-Submission System'); ?></div>
            <!--<div class="font-size-20 font-weight-900">CORE</div>-->
        </div>
        <h4 class="text-center"><?= Yii::t('app', 'กรุณาเลือกหน้าที่') ?></h4>
        <?php
        foreach ($roles as $role):
            if ($role->role_id == app\models\Role::COORDINATOR) {
                $p = '<br>' . yii::t('app', '(กรณียื่นโครงการแทนผู้วิจัยหลัก)');
            } else {
                $p = NULL;
            }
            ?>
            <?= Html::a($role->role->i18nName . $p, ['site/select-role', 'personRoleId' => $role->id], ['class' => 'btn btn-block btn-primary btn-lg']); ?>
        <?php endforeach; ?>
    </div>
</div>