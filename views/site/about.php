<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'polling unit / LGA result';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="container mx-auto py-8">
    <div class="bg-indigo-500 rounded-lg shadow p-6">
    <?php if(!empty($pollDetails)): ?>

      <h1 class="text-2xl font-bold mb-4 text-white"><?= is_numeric($pollDetails['p_name'])? $pollDetails['l_name']. ' LGA':$pollDetails['p_name'] . ' POLLING UNIT'  ?></h1>


      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="font-bold text-white">Polling Unit: <span class="font-normal text-gray-200"><?= $pollDetails['p_name'] ?? 'Nil' ?></span></p>
          <p class="font-bold text-white">Ward: <span class="font-normal text-gray-200"><?= $pollDetails['w_name'] ?? 'Nil' ?></span></p>
          <p class="font-bold text-white">Local Government: <span class="font-normal text-gray-200"><?= $pollDetails['l_name'] ?? 'Nil' ?></span></p>
        </div>
      <?php 
      

      unset($pollDetails['p_name']);
      unset($pollDetails['w_name']);
      unset($pollDetails['l_name']);
      unset($pollDetails['for']);
      if(!empty($pollDetails)):
      ?>
        <div>

        <?php foreach($pollDetails as $party => $score): ?>
          <p class="font-bold text-white"><?= $party ?><span class="font-normal text-gray-200"> <?= $score ?> votes</span></p>
          <?php  endforeach; ?>

        </div>
    <?php  endif; ?>

      </div>
      <?php else: ?>
      <h1 class="font-bold text-white"> No Data For this polling unit </h1>
      <?php endif; ?>
    </div>
  </div>
</div>
