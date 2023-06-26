<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\helpers\Url;
use yii\bootstrap5\Html;
use yii\helpers\Html as H;
use yii\bootstrap5\ActiveForm;
use PhpParser\Node\Stmt\Foreach_;


$this->title = 'Enter Party Result';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to save result:</p>
<form method="POST" action="<?= Url::to(['/site/save']) ?>">
<input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />

    <div class="container mx-auto p-4">
        <div class="mb-4">
            <label for="lga" class="block text-gray-700 text-sm font-bold mb-2">LGA:</label>
            <select name="lga" id="lga" class="bg-white border border-gray-300 rounded-md px-4 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select a Local Government.</option>

            <?php foreach ($lga as $key ): ?>   
            <option value="<?= $key['id'] ?>"><?= $key['lga'] ?></option>
            <?php endforeach; ?>   

            </select>
        </div>

        <div class="mb-4" id="wardContainer" style="display:none">
            <label for="ward" class="block text-gray-700 text-sm font-bold mb-2">Ward:</label>
            <select  name="ward" id="ward" class="bg-white border border-gray-300 rounded-md w-64 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <!-- Ward options here -->
            </select>
        </div>

        <div class="mb-4" id="puContainer" style="display:none">
            <label for="polling-unit" class="block text-gray-700 text-sm font-bold mb-2">Polling Unit:</label>
            <select  name="pu" id="pu" class="bg-white border border-gray-300 rounded-md px-4 w-64 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <!-- Polling unit options here -->
            </select>
        </div>
    
        <div class="flex flex-wrap">
    <?php foreach ($party as $key ): ?>   
        <div class="w-64 mb-4">
            <label for="result" class="block text-gray-700 text-sm font-bold"><?= $key['party'] ?></label>
            <input value="" id="result" type="number" name="<?= $key['party'] ?>" placeholder="<?= $key['party'] ?>" class="result bg-white border border-gray-300 rounded-md w-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
        </div>
    <?php endforeach; ?>   
</div>

<button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
  Submit
</button>



    </div>

</form>
</div>

<script>
        $(document).ready(function() {
            $('#lga').on('change', function(){
                var selected = $(this).val();
                console.log(selected);
                if(selected !==  ''){
                    $('#wardContainer').show();

                    $.ajax({
            url: 'http://localhost:8080/index.php/site/fetchward', // Replace with the actual URL to fetch the options
            type: 'GET',
            data: { lga: selected },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var options = response.options;
                    $('#ward').empty();
                    $('#ward').append($('<option>').text('Select a Ward').val(''));
                    
                    $.each(options, function(index, option) {
                  $('#ward').append($('<option>').text(option.ward).val(option.id));
                });
                if(options.length === 0){
                    $('#ward').empty();
                    $('#ward').append($('<option>').text('No Ward available for this LGA').val(''));
                  }
                }else{

                }
            },
            error: function(xhr, status, error) {
              // Handle AJAX error
            }
        })
                }else{
                    $('#wardContainer').hide();
                     $('#ward').empty();
                     $('#puContainer').hide();
                     $('#pu').empty();
                }
            });

            $('#ward').on('change', function(){
                var selected = $(this).val();
                console.log(selected);
                if(selected !==  ''){
                    $('#puContainer').show();

                    $.ajax({
            url: 'http://localhost:8080/index.php/site/fetchpu', // Replace with the actual URL to fetch the options
            type: 'GET',
            data: { ward: selected },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var options = response.options;
console.log(options);
                    $('#pu').empty();
                    $('#pu').append($('<option>').text('Select a Polling Unit').val(''));
                    
                    $.each(options, function(index, option) {
                  $('#pu').append($('<option>').text(option.pu).val(option.id));
                 
                });
                if(options.length === 0){
                    $('#pu').empty();
                    $('#pu').append($('<option>').text('No Polling Unit available for this ward').val(''));
                  }
                }else{

                }
            },
            error: function(xhr, status, error) {
              // Handle AJAX error
            }
        })
                }else{
                    $('#puContainer').hide();
                     $('#pu').empty();
                     
                }
            });

            $('#pu').on('change', function () {
                $('.result').prop("disabled", false)
            });
        });
</script>