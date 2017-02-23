<?php
namespace app\commands;

use yii\console\Controller;

class DefaultsController extends Controller
{
    public function actionInitUsers()
    {
         $admin = new \app\models\User(['username' => 'admin', 'email' => 'admin@localhost']);
         $admin->setPassword('test123');
         $admin->generateAuthKey();
         $admin->save();
    }
}
