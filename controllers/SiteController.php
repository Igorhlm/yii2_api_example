<?php

declare(strict_types=1);

namespace app\controllers;

use \yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

/**
 * Класс SiteController
 */

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();

        $formats = [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'formats' => [
                    'application/html' => Response::FORMAT_HTML,
                ],
            ],
        ];

        return ArrayHelper::merge($parentBehaviors, $formats);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
