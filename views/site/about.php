<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        This is the About page.
    </p>
    <p>
        Yii2-API-example application
    </p>

    <code><?= __FILE__ ?></code>
</div>
