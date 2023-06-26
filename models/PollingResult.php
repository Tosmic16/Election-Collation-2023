<?php

namespace app\models;

use yii\db\ActiveRecord;

class PollingResult extends ActiveRecord
{
    public static function tableName()
{
    return 'announced_pu_results';
}

   }
