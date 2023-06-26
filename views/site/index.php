<?php

use yii\helpers\Url;
use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">

<div class="flex items-center justify-center h-screen">
  <div class="text-center">
    <a href="<?= Url::to(['/site/login']) ?>" class="block my-4 py-2 px-4 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300 no-underline">Register and Save Polling Unit Result</a>
    <a href="<?= Url::to(['/site/prose']) ?>" class="block my-4 py-2 px-4 bg-green-500 text-white rounded-lg hover:bg-green-700 transition-colors duration-300 no-underline">Get Polling Unit or Local Government Result</a>
  </div>
</div>



</div>

