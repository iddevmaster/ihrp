<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Title;
use app\models\Organization;
use app\models\Department;
use app\models\Division;
use app\models\Position;
use app\models\JobCategory;
use app\models\Role;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\widgets\FileInput;
use kartik\datecontrol\DateControl;

$currentRole = \Yii::$app->session->get('currentRole');

/* @var $this yii\web\View */
/* @var $model app\models\Person */
/* @var $regForm app\models\RegistrationForm */
/* @var $searchModel app\models\PersonTrainingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="person-profile">

    <?php
    $form = ActiveForm::begin([
                'id' => 'profile-form',
                'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>
    <?= $this->renderFile('@app/views/widgets/_alert.php'); ?>

    <!-- ชื่อผู้ใช้และรหัสผ่าน -->
    <div class="profile-card" data-card="account">
        <button type="button" class="btn btn-sm btn-kkuec js-card-edit"><i class="icon md-edit"></i> <?= Yii::t('app', 'แก้ไข') ?></button>
        <h4><?= Yii::t('app', 'ชื่อผู้ใช้และรหัสผ่าน') ?></h4>
        <div class="js-lockable">
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($regForm, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($regForm, 'password')->passwordInput(['maxlength' => true])->label(Yii::t('app', 'รหัสผ่านที่ต้องการเปลี่ยน (password)')) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($regForm, 'password_repeat')->passwordInput(['maxlength' => true]) ?>
                </div>
            </div>
        </div>
        <div class="profile-card-actions">
            <button type="button" class="btn btn-kkuec-outline js-card-cancel"><?= Yii::t('app', 'ยกเลิกการแก้ไข') ?></button>
            <?= Html::submitButton(Yii::t('app', 'บันทึกการแก้ไขข้อมูล'), ['class' => 'btn btn-kkuec']) ?>
        </div>
    </div>

    <!-- 1. ข้อมูลผู้ใช้งานและสังกัด -->
    <div class="profile-card" data-card="info">
        <button type="button" class="btn btn-sm btn-kkuec js-card-edit"><i class="icon md-edit"></i> <?= Yii::t('app', 'แก้ไข') ?></button>
        <h4>1. <?= Yii::t('app', 'ข้อมูลผู้ใช้งานและสังกัด') ?></h4>
        <div class="js-lockable">
            <?php if ($currentRole['role_id'] == Role::ADMIN) { ?>
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'is_researcher_crec')->checkbox(); ?>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-lg-4">
                    <?php
                    $data = ArrayHelper::map(Title::find()->isDeleted(FALSE)->orderBy('CONVERT(title.name USING TIS620) ASC')->all(), 'id', 'i18nName');
                    echo $form->field($model, 'title_id')->widget(Select2::classname(), [
                        'data' => $data,
                        'options' => ['placeholder' => Yii::t('app', 'เลือกคำนำหน้าชื่อ')],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, 'first_name_eng')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'last_name_eng')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <h5>1.1 <?= Yii::t('app', 'ข้อมูลติดต่อ') ?></h5>
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'mobile_no')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'tel')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <h5>1.2 <?= Yii::t('app', 'สังกัดต้นสังกัด') ?></h5>
            <div class="row">
                <div class="col-lg-6">
                    <?php
                    $data = ArrayHelper::map(Organization::find()->isDeleted(FALSE)->orderBy('CONVERT(organization.name USING TIS620) ASC')->all(), 'id', 'i18nName');
                    echo $form->field($model, 'organization_id')->label(Yii::t('app', 'องค์กร/หน่วยงาน (หากไม่พบ กรุณาติดต่อศูนย์จริยธรรมฯ)'))->widget(Select2::classname(), [
                        'data' => $data,
                        'options' => ['placeholder' => Yii::t('app', 'เลือกหน่วยงาน')],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-lg-6">
                    <?php
                    $data = [];
                    if (!empty($model->organization_id)) {
                        $data = ArrayHelper::map(Department::find()->isDeleted(FALSE)->organization($model->organization_id)->orderBy('CONVERT(department.name USING TIS620)')->all(), 'id', 'i18nName');
                    }
                    echo $form->field($model, 'department_id')->widget(DepDrop::classname(), [
                        'type' => DepDrop::TYPE_SELECT2,
                        'data' => $data,
                        'select2Options' => [
                            'pluginOptions' => ['allowClear' => true]
                        ],
                        'pluginOptions' => [
                            'depends' => [Html::getInputId($model, 'organization_id')],
                            'url' => Url::to(['/department/list']),
                            'placeholder' => '',
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <?php
                    $data = [];
                    if (!empty($model->department_id)) {
                        $data = ArrayHelper::map(Division::find()->isDeleted(FALSE)->department($model->department_id)->orderBy('CONVERT(division.name USING TIS620)')->all(), 'id', 'i18nName');
                    }
                    echo $form->field($model, 'division_id')->widget(DepDrop::classname(), [
                        'type' => DepDrop::TYPE_SELECT2,
                        'data' => $data,
                        'options' => [
                            'placeholder' => yii::t('app', 'ภาควิชา'),
                        ],
                        'select2Options' => [
                            'pluginOptions' => ['allowClear' => true]
                        ],
                        'pluginOptions' => [
                            'depends' => [Html::getInputId($model, 'department_id')],
                            'url' => Url::to(['/division/list']),
                            'placeholder' => '',
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-lg-6">
                    <?php
                    $data = ArrayHelper::map(Position::find()->isDeleted(FALSE)->orderBy('CONVERT(position.name USING TIS620) ASC')->all(), 'id', 'i18nName');
                    echo $form->field($model, 'position_id')->widget(Select2::classname(), [
                        'data' => $data,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <?= $form->field($model, 'expertise')->textarea(['maxlength' => true]) ?>
                </div>
            </div>

            <h5>1.3 <?= Yii::t('app', 'สถานภาพและบทบาท') ?></h5>
            <div class="row">
                <div class="col-lg-6">
                    <?php
                    $data = ArrayHelper::map(JobCategory::find()->isDeleted(FALSE)->orderBy('CONVERT(job_category.name USING TIS620) ASC')->all(), 'id', 'name');
                    echo $form->field($model, 'job_category_id')->widget(Select2::classname(), [
                        'data' => $data,
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'is_paediatrician')->checkbox(); ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'is_external')->checkbox(); ?>
                </div>
            </div>

            <h5>1.4 <?= Yii::t('app', 'นโยบายและการยินยอม') ?></h5>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'accept_policy')->checkbox(['label' => Yii::t('app', 'ข้าพเจ้าเข้าใจและตกลงตามเงื่อนไขการให้บริการและนโยบายความเป็นส่วนตัว')]); ?>
                </div>
            </div>
        </div>
        <p><?= Html::a(Yii::t('app', '[นโยบาย]'), ['person/policy'], ['role' => 'modal-remote', 'data-toggle' => 'tooltip']); ?></p>
        <div class="profile-card-actions">
            <button type="button" class="btn btn-kkuec-outline js-card-cancel"><?= Yii::t('app', 'ยกเลิกการแก้ไข') ?></button>
            <?= Html::submitButton(Yii::t('app', 'บันทึกการแก้ไขข้อมูล'), ['class' => 'btn btn-kkuec']) ?>
        </div>
    </div>

    <!-- 2. การจัดการเอกสารและประวัติการอบรม -->
    <div class="profile-card" data-card="documents">
        <button type="button" class="btn btn-sm btn-kkuec js-card-edit"><i class="icon md-edit"></i> <?= Yii::t('app', 'แก้ไข') ?></button>
        <h4>2. <?= Yii::t('app', 'การจัดการเอกสารและประวัติการอบรม') ?></h4>
        <h5>2.1 <?= Yii::t('app', 'ประวัติผู้วิจัย (CV) ส่วนตัว') ?></h5>
        <div class="js-lockable">
            <div class="row">
                <div class="col-lg-6">
                    <?=
                    $form->field($model, 'cv_file')->label(Yii::t('app', '<font style="color: red">** ในกรณีแก้ไขข้อมูลส่วนตัว/แนบไฟล์ CV ใหม่ หลังจากแนบไฟล์แล้วให้กดปุ่มบันทึกเพื่อทำการบันทึกข้อมูล</font>'))->widget(FileInput::classname(), [
                        'options' => [
                            'accept' => '*',
                        ],
                        'pluginOptions' => [
                            'theme' => 'gly',
                            'showPreview' => FALSE,
                            'showRemove' => FALSE,
                            'showUpload' => false,
                            'browseLabel' => Yii::t('app', 'เลือกไฟล์'),
                            'removeLabel' => '',
                            'initialPreview' => [
                                Url::to(['person/download', 'id' => $model->id])
                            ],
                            'initialCaption' => $model->cv_file,
                            'initialPreviewConfig' => [],
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'cv_updated_at')->widget(DateControl::classname(), [
                        'type' => DateControl::FORMAT_DATE,
                        'widgetOptions' => [
                            'pluginOptions' => ['endDate' => '0d'],
                        ],
                    ])->label(Yii::t('app', 'วันที่ปรับปรุงประวัติ (CV) ล่าสุด')) ?>
                </div>
            </div>
        </div>
        <p>
            <?php
            if (isset($model->cv_file)) {
                echo Html::a('<i class="icon md-attachment"></i> ' . Yii::t('app', 'ไฟล์ประวัติ'),
                        Url::to(['person/download', 'id' => $model->id]),
                        ['target' => '_blank']);
                if (!$model->isNewRecord && !empty($model->cv_signed_at)) {
                    echo ' <span class="label label-info">' . Yii::t('app', 'ลงนามเมื่อ') . ' ' . Yii::$app->formatter->asDatetime($model->cv_signed_at) . '</span>';
                }
            }
            ?>
        </p>
        <div class="js-lockable profile-card-consent">
            <?= $form->field($model, 'cv_apply_stamp')->checkbox(['label' => Yii::t('app', 'ต้องการให้ระบบลงลายมือชื่ออิเล็กทรอนิกส์ให้ในไฟล์ (ประวัติส่วนตัวและเอกสารการอบรม) (ประทับชื่อและวันที่ลงบนเอกสาร)')]); ?>
            <?= $form->field($model, 'accept_cv_certify')->checkbox(['label' => Yii::t('app', 'ข้าพเจ้าขอรับรองว่าเอกสารที่อัปโหลด (CV/เอกสารการอบรม) เป็นความจริงและถูกต้องทุกประการ')]); ?>
        </div>
        <div class="profile-card-actions">
            <button type="button" class="btn btn-kkuec-outline js-card-cancel"><?= Yii::t('app', 'ยกเลิกการแก้ไข') ?></button>
            <?= Html::submitButton(Yii::t('app', 'บันทึกการแก้ไขข้อมูล'), ['class' => 'btn btn-kkuec']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <!-- 2.2 ประวัติการอบรม (grid มีฟอร์มค้นหาของตัวเอง จึงอยู่นอกฟอร์มหลัก) -->
    <div class="profile-card profile-card-grid" data-card="training">
        <h5>2.2 <?= Yii::t('app', 'ประวัติข้อมูลการอบรมด้านจริยธรรมการวิจัย') ?></h5>
        <?=
        $this->renderFile('@app/views/person-training/index.php', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        ?>
    </div>

</div>

<?php
$this->registerCss(<<<CSS
.profile-card {
    position: relative;
    background: #fff;
    border: 1px solid #e8e2da;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    padding: 20px 20px 15px;
    margin-bottom: 25px;
}
.profile-card h4 {
    color: #a83b24;
    font-weight: 600;
    margin: 0 90px 15px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #f0ebe4;
}
.profile-card h5 { font-weight: 600; margin-top: 20px; }
.profile-card > .js-card-edit { position: absolute; top: 15px; right: 15px; z-index: 5; }
.profile-card .profile-card-actions { margin-top: 15px; text-align: right; }
.profile-card.profile-card-locked .js-lockable { pointer-events: none; }
.profile-card-consent { margin-top: 10px; }
.btn-kkuec, .btn-kkuec:focus {
    background-color: #4663CA; border-color: #4663CA; color: #fff;
}
.btn-kkuec:hover, .btn-kkuec:active {
    background-color: #32408f; border-color: #32408f; color: #fff;
}
.btn-kkuec-outline, .btn-kkuec-outline:focus {
    background-color: #fff; border: 1px solid #4663CA; color: #4663CA;
}
.btn-kkuec-outline:hover { background-color: #eef0fa; color: #4663CA; }
CSS
);

$js = <<<JS
(function() {
    var snapshots = {};

    function setLocked(card, locked) {
        var scope = card.find('.js-lockable');
        scope.find('input, select, textarea')
             .not('[type=hidden]')
             .not('[type=file]')
             .prop('disabled', locked);
        scope.find('input[type=file]').each(function() {
            try { $(this).fileinput(locked ? 'disable' : 'enable'); } catch (e) {}
        });
        scope.find('select').trigger('change.select2');
        card.toggleClass('profile-card-locked', locked);
        card.find('.profile-card-actions').toggle(!locked);
        card.find('.js-card-edit').toggle(locked);
    }

    function snapshotCard(card) {
        var snap = {inputs: [], selects: []};
        card.find('.js-lockable').find('input, textarea').not('[type=file]').each(function() {
            snap.inputs.push({el: this, val: $(this).val(), checked: this.checked});
        });
        card.find('.js-lockable select').each(function() {
            snap.selects.push({el: this, html: $(this).html(), val: $(this).val()});
        });
        snapshots[card.data('card')] = snap;
    }

    function restoreCard(card) {
        var snap = snapshots[card.data('card')];
        if (!snap) { return; }
        $.each(snap.selects, function(_, s) { $(s.el).html(s.html).val(s.val); });
        $.each(snap.inputs, function(_, s) { $(s.el).val(s.val); s.el.checked = s.checked; });
        card.find('.js-lockable input[type=file]').each(function() {
            try { $(this).fileinput('clear'); } catch (e) {}
        });
        // change.select2 เท่านั้น เพื่อไม่ให้ DepDrop ยิง ajax ทับค่าที่คืนกลับ
        card.find('.js-lockable select').trigger('change.select2');
    }

    $('.js-card-edit').on('click', function() {
        var card = $(this).closest('.profile-card');
        snapshotCard(card);
        setLocked(card, false);
    });

    $('.js-card-cancel').on('click', function() {
        var card = $(this).closest('.profile-card');
        restoreCard(card);
        setLocked(card, true);
    });

    // เปิดใช้งานทุก input ก่อน submit เพื่อให้ค่าของการ์ดที่ล็อกอยู่ถูกส่งไปด้วย
    $('#profile-form').on('beforeSubmit', function() {
        $(this).find(':disabled').prop('disabled', false);
        $(this).find('input[type=file]').each(function() {
            try { $(this).fileinput('enable'); } catch (e) {}
        });
        return true;
    });

    // ถ้า validate ไม่ผ่านในการ์ดที่ล็อกอยู่ ให้ปลดล็อกการ์ดนั้นเพื่อให้เห็น error
    $('#profile-form').on('afterValidate', function(e, messages, errorAttrs) {
        if (errorAttrs && errorAttrs.length) {
            $('.profile-card').each(function() {
                if ($(this).find('.has-error').length) {
                    setLocked($(this), false);
                }
            });
        }
    });

    // เริ่มต้น: ล็อกทุกการ์ด ยกเว้นการ์ดที่มี validation error จากฝั่ง server
    $('.profile-card').not('.profile-card-grid').each(function() {
        setLocked($(this), $(this).find('.has-error').length === 0);
    });
})();
JS;
$this->registerJs($js);
?>
