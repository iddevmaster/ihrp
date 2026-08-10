<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\components;

class JwtValidationData extends \sizeg\jwt\JwtValidationData {

    /**
     * @inheritdoc
     */
    public function init() {
        $this->validationData->setIssuer('https://echr.kku.ac.th');
        $this->validationData->setAudience('https://echr.kku.ac.th');
        $this->validationData->setId('93f5f52b2b6a5d9fce4e2278b1341da1c1e06bbd');

        parent::init();
    }

}
