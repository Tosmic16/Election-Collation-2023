<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\PollingResult;
use yii\filters\AccessControl;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
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
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $party = Yii::$app->db->createCommand('
        SELECT DISTINCT party_abbreviation as party FROM announced_pu_results') 
        ->queryAll();

        $lga = Yii::$app->db->createCommand('
        SELECT DISTINCT lga_name as lga, lga_id as id FROM lga') 
        ->queryAll();      
       
        return $this->render('login', [
            'party' => $party,
            'lga' => $lga,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout( $id)
    {
        $arr = [];
        $query = Yii::$app->db->createCommand('
        SELECT DISTINCT *
        FROM `announced_pu_results`
        LEFT JOIN `polling_unit` ON polling_unit.uniqueid = announced_pu_results.polling_unit_uniqueid
        LEFT JOIN `ward` ON ward.uniqueid = polling_unit.ward_id
        LEFT JOIN `lga` ON lga.lga_id = polling_unit.lga_id
        WHERE `announced_pu_results`.`polling_unit_uniqueid` = :polling_unit_uniqueid
        GROUP BY party_abbreviation
    ')
        ->bindValue(':polling_unit_uniqueid', $id)
        ->queryAll();

        if (!$query) {
            return $this->render('about', ['pollDetails' => $arr]);
        }
     $arr['p_name'] = $query[0]['polling_unit_name'];
    $arr['w_name'] = $query[0]['ward_name'];
    $arr['l_name'] = $query[0]['lga_name'];

    foreach ($query as $res){
        $arr[$res['party_abbreviation']] = $res['party_score'];
    }
 
        return $this->render('about', ['pollDetails' => $arr]);
    }
    
    public function actionFetchward($lga)
    {
        header('Access-Control-Allow-Origin: null');
        $query = Yii::$app->db->createCommand('
        SELECT DISTINCT ward_name as ward, uniqueid as id FROM ward WHERE lga_id = :lga') 
        ->bindValue(':lga', $lga)
        ->queryAll();

       $data =['options'=> $query, 'success'=>true];
       echo json_encode($data);
       die;
    }

    public function actionFetchpu($ward)
    {
        header('Access-Control-Allow-Origin: null');
        $query = Yii::$app->db->createCommand('
        SELECT DISTINCT polling_unit_name as pu, uniqueid as id FROM polling_unit WHERE ward_id = :ward') 
        ->bindValue(':ward', $ward)
        ->queryAll();

       $data =['options'=> $query, 'success'=>true];
       echo json_encode($data);
       die;
    }

    public function actionSave()
    {
     $formData = Yii::$app->request->post();
     $lga =  $formData['lga'];
     $ward =  $formData['ward'];
     $pu =  $formData['pu'];
     $eby =  'tosmic';
     $ip = Yii::$app->request->getUserIP();

     unset($formData['lga']);
     unset($formData['ward']);
     unset($formData['pu']);
     unset($formData['_csrf']);
     
foreach($formData as $data => $value){
    if($value !== ""){
    $query = Yii::$app->db->createCommand()->insert('announced_pu_results', [
        'polling_unit_uniqueid' => $pu,
        'party_abbreviation' => $data,
        'party_score' => $value,
        'entered_by_user' => $eby,
        'date_entered' => date('Y-m-d H:i:s', strtotime('now')),
        'user_ip_address' => $ip,
    ])->execute();
    }
}        

Yii::$app->session->setFlash('success', 'submitted Successfully');

$url = Url::to(['site/about', 'id' => $pu]);

// Redirect to another page
return $this->redirect($url);
    }
    
    public function actionProse()
    {
      
        $lga = Yii::$app->db->createCommand('
        SELECT DISTINCT lga_name as lga, lga_id as id FROM lga') 
        ->queryAll();      
       
        return $this->render('prose', [
           
            'lga' => $lga,
        ]);
    }
    public function actionFetch()
    {
        $formData = Yii::$app->request->post();
        $lga =  $formData['lga'];

        $arr = [];

        if (isset($formData['pu'])) {
            $pu =  $formData['pu'];

            $query = Yii::$app->db->createCommand('
            SELECT DISTINCT *
            FROM `announced_pu_results`
            LEFT JOIN `polling_unit` ON polling_unit.uniqueid = announced_pu_results.polling_unit_uniqueid
            LEFT JOIN `ward` ON ward.uniqueid = polling_unit.ward_id
            LEFT JOIN `lga` ON lga.lga_id = polling_unit.lga_id
            WHERE `announced_pu_results`.`polling_unit_uniqueid` = :polling_unit_uniqueid
            GROUP BY party_abbreviation
        ')
            ->bindValue(':polling_unit_uniqueid', $pu)
            ->queryAll();
    
            if (!$query) {
                return $this->render('about', ['pollDetails' => $arr]);
            }
         $arr['p_name'] = $query[0]['polling_unit_name'];
        $arr['w_name'] = $query[0]['ward_name'];
        $arr['l_name'] = $query[0]['lga_name'];
    
        foreach ($query as $res){
            $arr[$res['party_abbreviation']] = $res['party_score'];
        }
     $arr['for'] = 'polling unit result';
            return $this->render('about', ['pollDetails' => $arr]);
        }
     
    
        $pu = Yii::$app->db->createCommand('
       
        SELECT DISTINCT COUNT(polling_unit.uniqueid) as pu, lga.lga_name as lga
        FROM `lga`
        LEFT JOIN `polling_unit` ON lga.lga_id = polling_unit.lga_id
        WHERE `lga`.`lga_id` = :lga
    ') ->bindValue(':lga', $lga)
    ->queryAll();

    $ward = Yii::$app->db->createCommand('
       
    SELECT DISTINCT COUNT(uniqueid)  as ward
    FROM `ward`
    WHERE `lga_id` = :lga
') ->bindValue(':lga', $lga)
->queryAll();
$arr["w_name"] = $ward[0]['ward'];
$arr["p_name"] = $pu[0]['pu'];
$arr["l_name"] = $pu[0]['lga'];


        $query = Yii::$app->db->createCommand('
        SELECT SUM(announced_pu_results.party_score) as score, announced_pu_results.party_abbreviation as party
        FROM `announced_pu_results`
        
        WHERE polling_unit_uniqueid IN (SELECT uniqueid FROM polling_unit WHERE lga_id = :lga)

        GROUP BY party_abbreviation
    ')
        ->bindValue(':lga', $lga)
        ->queryAll();

 


        if (!$query) {
            return $this->render('about', ['pollDetails' => []]);
        }
   
    foreach ($query as $res){
        $arr[$res['party']] = $res['score'];
    }
    $arr['for'] = 'Local Government result';

        return $this->render('about', ['pollDetails' => $arr]);
    }
}
