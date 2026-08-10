<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers\api;

use yii\rest\Controller;
use app\models\LoginForm;
use yii\filters\VerbFilter;
use yii\web\HttpException;

class AuthController extends Controller {

    public function behaviors() {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @api {post} /index.php?r=api/auth/login Login เพื่อสร้าง token ให้ client
     * @apiVersion 1.0.0
     * @apiName API Login
     * @apiGroup Auth
     *
     * @apiParam {String} username ชื่อผู้ใช้งาน
     * @apiParam {String} password รหัสผ่านผู้ใช้งาน
     *
     * @apiSuccess (Login สำเร็จ) {String} status สถานะของผลการ Login (OK=Login สำเร็จ, ERROR=มีข้อผิดพลาดเกิดขึ้น)
     * @apiSuccess (Login สำเร็จ) {String} token token สำหรับใช้ในการยืนยันตัวตนในการเรียกใช้งาน API
     * @apiSuccessExample ตัวอย่างข้อมูล
     *     HTTP/1.1 200 OK
     *     {
     *       "status": "OK",
     *       "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IjRmMWcyM2ExMmFhIn0.
     *                 eyJpc3MiOiJodHRwOlwvXC90cmFuc3BvcnQtcG9ydGFsIiwiYXVkIjoiaHR0cDp
     *                 cL1wvdHJhbnNwb3J0LXBvcnRhbCIsImp0aSI6IjRmMWcyM2ExMmFhIiwiaWF0Ij
     *                 oxNTc3Nzg4MzQ1LCJleHAiOjE1Nzc3OTE5NDUsInVpZCI6MX0.5i4NH2YILgdW4
     *                 tPQQqeRYcSEDbMQKiE_boPNOiBN5j8"
     *     }
     *
     * @apiError (Error) {String} status สถานะของผลการ Login (OK=Login สำเร็จ, ERROR=มีข้อผิดพลาดเกิดขึ้น)
     * @apiError (Error) {Object[]} errors ข้อผิดพลาด
     * @apiError (Error) {String} errors.field ชื่อฟิลด์
     * @apiError (Error) {String} errors.message ข้อความ
     * 
     * @apiErrorExample ตัวอย่างข้อมูล
     *     HTTP/1.1 200 OK
     *     {
     *       "status": "ERROR",
     *       "errors": [{
     *          "field": "username",
     *          "message": "กรุณาระบุชื่อผู้ใช้และรหัสผ่าน"
     *       }]
     *     }
     */
    public function actionLogin() {
        $response = \Yii::$app->getResponse();
        $model = new LoginForm();
        $model->username = \Yii::$app->request->post('username');
        $model->password = \Yii::$app->request->post('password');
        if (!isset($model->username) || !isset($model->password)) {
            $response->setStatusCode(200);
            return $this->asJson([
                        'status' => 'ERROR',
                        'errors' => [
                            [
                                'field' => 'username',
                                'message' => 'กรุณาระบุชื่อผู้ใช้และรหัสผ่าน',
                            ]
                        ],
            ]);
            //throw new HttpException(500, 'กรุณาระบุชื่อผู้ใช้และรหัสผ่าน');
        }
        $user = $model->getUser();

        if (!$user || !$user->validatePassword($model->password)) {
            $response->setStatusCode(200);
            return $this->asJson([
                        'status' => 'ERROR',
                        'errors' => [
                            [
                                'field' => 'password',
                                'message' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
                            ]
                        ],
            ]);
            //throw new HttpException(500, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        }
        $jwt = \Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();
        $token = $jwt->getBuilder()
                ->issuedBy('https://echr.kku.ac.th')// Configures the issuer (iss claim)
                ->permittedFor('https://echr.kku.ac.th')// Configures the audience (aud claim)
                ->identifiedBy('93f5f52b2b6a5d9fce4e2278b1341da1c1e06bbd', true)// Configures the id (jti claim), replicating as a header item
                ->issuedAt($time)// Configures the time that the token was issue (iat claim)
                ->expiresAt($time + 3600)// Configures the expiration time of the token (exp claim)
                ->withClaim('uid', $user->id)// Configures a new claim, called "uid"
                ->getToken($signer, $key); // Retrieves the generated token

        return $this->asJson([
                    'status' => 'OK',
                    'token' => (string) $token,
        ]);
    }

}
