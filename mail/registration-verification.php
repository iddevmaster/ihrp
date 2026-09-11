<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */
$url = Url::to(['person/verify-email', 'token' => $user->verify_token], TRUE);
?>
<div style="text-align: center"><img src="<?= Url::to(Yii::$app->urlManager->baseUrl . '/images/logo.png', true) ?>" width="90"></div>
<div style="text-align: center; font-size: 18px"><?= Yii::$app->name ?></div>
<p>เรียน <?= isset($user->person->fullName) ? $user->person->fullName : "" ?></p>
<p>ขอบคุณสำหรับการลงทะเบียนระบบ e-Submission กรุณาคลิกลิงก์ด้านล่างเพื่อยืนยันอีเมลที่ท่านได้ลงทะเบียนไว้ เพื่อดำเนินการเข้าใช้งานระบบ</p>
<div style="padding: 20px; border-radius: 3px; background-color: #1d499c; color: white"><?= Html::a($url, $url, ['style' => 'color: white']); ?></div>
<p>หากไม่สามารถคลิกลิงก์ด้านบนได้ ท่านสามารถคัดลอกลิงก์ไปวางในเว็บเบราว์เซอร์(Browser) เพื่อดำเนินการต่อได้</p>
<p>
<font style="color: red"><?= Yii::$app->util->emailFooter(); ?></font>
</p>
<br>
<hr color="red" align="center" width="70%" size="5">
<br>
<p>To <?= isset($user->person->fullNameEng) ? $user->person->fullNameEng : "" ?></p>
<p>Thank you for registering with our e-Submission. Please click the link below to confirm your email address.</p>
<div style="padding: 20px; border-radius: 3px; background-color: #1d499c; color: white"><?= Html::a($url, $url, ['style' => 'color: white']); ?></div>
<p>If you are unable to click the link above, you can copy and paste the link into your web browser to continue.</p>
<p>
<font style="color: red"><?= Yii::$app->util->emailFooterEng(); ?></font>
</p>