<?php

/** @var yii\web\View $this */

$this->title = Yii::$app->name;
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Yii2-API-example application</h1>
    </div>

    <div class="body-content">
        <div class="jumbotron text-center bg-transparent mt-5 mb-5">
            <p><?= yii\helpers\Html::a('Yii2-API-example swagger', ['docs/index.html'], ['class' => 'btn btn-lg btn-success']) ?></p>
        </div>
    </div>
</div>
