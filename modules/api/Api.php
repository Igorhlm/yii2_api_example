<?php

namespace app\modules\api;

use app\modules\api\modules\v1\V1;

/**
 * Module Api definition class
 */
class Api extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->modules = [
            'v1' => [
                'class' => V1::class,
            ]
        ];
    }
}
