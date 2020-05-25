<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 11/13/2018
 * Time: 6:27 PM
 */

namespace Application\Slim\V1\Controller;
use Application\Entity\Currency;
use Application\Entity\RegistrationField;
use Application\Entity\Session;
use Application\Entity\SessionCategory;
use Application\Entity\Setting;
use Application\Entity\Student;
use Application\Entity\WidgetValue;
use Application\Model\NewsflashTable;
use Application\Model\WidgetValueTable;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AppController extends Controller  {



    public function login($request, $response, $args) {
        // your code
        // to access items in the container... $this->container->get('');

        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = trim($data['password']);

        $student = Student::where('email',$email)->where('password',md5($password))->first();
        if($student){
            //check if student has token
            $token = $student->api_token;

            if(empty($token)){
                do{
                    $token = bin2hex(random_bytes(16));
                }while(!Student::where('api_token',$token));

                $student->api_token = $token;

            }

            $student->token_expires = time() + (86400 * 365);
            $student->save();



            return jsonResponse(['id'=>$student->student_id,'first_name'=>$student->first_name,'last_name'=>$student->last_name,'token'=>$token,'status'=>true]);
        }
        else{
            return jsonResponse(['status'=>false]);
        }

    }

    public function setup($request,$response,$args){
//        $response->withStatus(200);

       
        $output = [];

        $settings = [];
        foreach(Setting::where('key','NOT LIKE','footer_%')->where('key','NOT LIKE','social_%')->where('key','NOT LIKE','mail_%')->where('key','NOT LIKE','color_%')->get() as $row){
            $settings[$row->key] = $row->value;
        }
        $exclude = ['general_chat_code','general_header_scripts','general_foot_scripts','footer_newsletter_code','footer_credits','sms_enabled','sms_sender_name','social_enable_facebook','social_facebook_secret','social_facebook_app_id','social_enable_google','social_google_secret','social_google_id'];

        foreach($exclude as $value){
            unset($settings[$value]);
        }
        $output['settings'] = $settings;
        //set default currency

        $output['currencies'] = [];
        foreach(Currency::get() as $currency){
            $output['currencies'][] = [
              'currency_id'=>$currency->currency_id,
                'currency_code'=>$currency->country->currency_code,
                'currency_name'=>$currency->country->currency_name,
                'currency_symbol'=>$currency->country->symbol_left,
                'exchange_rate'=>$currency->exchange_rate
            ];
        }
        $session= new \Zend\Session\Container('currency');
        $currencyId = $session->currency_id;
        $output['student_currency'] = $currencyId;
        $uri = $request->getUri();
        $baseUrl = $uri->getBaseUrl();
        $output['base_path'] = $baseUrl;

        $widgets = [];
        $widgetValueTable = new WidgetValueTable();
        foreach($widgetValueTable->getWidgets(1,'m') as $row){
            if($row->widget_code=='sessions'){

                $sessionList = [];
                $vals = unserialize($row->value);
                for($i=1; $i<=10; $i++){
                    if(!empty($vals['session'.$i]) && Session::find($vals['session'.$i])){
                        $record = Session::find($vals['session'.$i]);
                        $sessionList[] = [
                            'session_id'=>$record->session_id,
                            'session_name'=>$record->session_name,
                            'amount'=>$record->amount,
                            'payment_required'=>$record->payment_required,
                            'short_description'=>$record->short_description,
                            'session_type'=>$record->session_type,
                            'picture'=>$record->picture
                        ];
                    }
                }
                $widgets[] = [
                    'widget_code'=>$row->widget_code,
                    'value'=> $sessionList
                ];

            }
            elseif($row->widget_code=='blog'){
                $newsTable = new NewsflashTable();
                $rowSet = $newsTable->getNews(5);
                $data = [];
                foreach($rowSet as $blog){
                    $data[] = [
                        'id'=>$blog->newsflash_id,
                      'title'=>$blog->title,
                        'content'=>limitLength(strip_tags($blog->content)),
                        'date'=>date('d M Y',$blog->date),
                        'picture'=>$blog->picture
                    ];
                }
                $widgets[] = [
                    'widget_code'=>$row->widget_code,
                    'value'=> $data
                ];
            }
            else{
                $widgets[] = [
                    'widget_code'=>$row->widget_code,
                    'value'=> unserialize($row->value)
                ];
            }

        }

        $output['widgets'] = $widgets;

        $registration = [];

        foreach(RegistrationField::where('status',1)->orderBy('sort_order')->get() as $row){
            $fieldData = $row->toArray();
            $fieldData['options'] = explode(PHP_EOL, $fieldData['options']);

            $registration[] = $fieldData;
        }

        $output['registration'] = $registration;

        $categories = [];

        foreach(SessionCategory::where('status',1)->orderBy('sort_order')->get() as $row){
            $categories[] = [
                'session_category_id'=>$row->session_category_id,
                'category_name'=>$row->category_name,
            ];
        }
        $output['categories'] = $categories;
        return jsonResponse($output);
    }

    public function update($request, $response, $args) {
        $data = $request->getParsedBody();
        $student = getApiStudent($request);

        $student->fill($student);
        $student->save();

        return jsonResponse($student->toArray());

    }

}