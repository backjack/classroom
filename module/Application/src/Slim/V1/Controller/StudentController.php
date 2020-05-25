<?php
namespace Application\Slim\V1\Controller;
use Application\Entity\RegistrationField;
use Application\Entity\Student;
use Application\Model\PasswordResetTable;
use Application\Model\RegistrationFieldTable;
use Application\Model\StudentFieldTable;
use Application\Model\StudentTable;
use Intermatics\HelperTrait;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
class StudentController extends Controller{

    use HelperTrait;
    protected $uploadDir;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $user= '';
        if(defined('USER_ID')){
            $user = '/'.USER_ID;
        }
        $filePath = 'usermedia'.$user;
        $this->uploadDir = $filePath.'/student_uploads/'.date('Y_m');
    }


    public function create($request, $response, $args) {
        if($this->getSetting('regis_enable_registration')!= 1){
            return jsonResponse(['status'=>false,'msg'=>'Registration Disabled']);
        }
        $data = $request->getParsedBody();

        $rules = [
            'first_name'=>'required',
            'last_name'=>'required',
            'mobile_number'=>'required',
            'email'=>'required|valid_email',
            'password'=>'required|max_len,100|min_len,6'
        ];

        foreach(RegistrationField::where('status',1)->orderBy('sort_order')->get() as $row){

            if($row->required==1 && $row->type != 'checkbox'){
                $rules['custom_'.$row->registration_field_id] = 'required';
            }

        }
        //validate request
       $isValid = $this->validate($data,$rules);

        if(!$isValid){
            return jsonResponse(['status'=>false,'msg'=>$this->getValidationErrors()]);
        }

        //check if email exists
        if(Student::where('email',trim($data['email']))->count()>0){
            return jsonResponse(['status'=>false,'msg'=>'This email address is already associated with another account']);
        }

        try{


                // your code
                // to access items in the container... $this->container->get('');

                $data['password'] = md5($data['password']);
                do{
//                    $token = md5(uniqid());
                    $token = bin2hex(random_bytes(16));
                }while(!Student::where('api_token',$token));

                $data['api_token'] = $token;
                $data['last_seen'] = time();
                $data['registration_complete'] = 1;
                $data['student_created'] = time();
                $data['status'] = 1;
                $data['token_expires'] = time() + (86400 * 365);
                $student = new Student();
                $student->fill($data);
                $student->save();
                $studentId = $student->student_id;
                $studentFieldTable = new StudentFieldTable();

                foreach(RegistrationField::where('status',1)->orderBy('sort_order')->get() as $row){
                    if(!isset($data['custom_'.$row->registration_field_id])){
                        continue;
                    }
                    $value = $data['custom_'.$row->registration_field_id];
                    if($row->type != 'file'){

                        $studentFieldTable->saveField($studentId,$row->registration_field_id,$value);
                    }
                    elseif(!empty($value['name']) && file_exists($value['tmp_name'])){

                        $file = $value['name'];
                        $newPath = $this->uploadDir.'/'.time().$studentId.'_'.sanitize($file);
                        $this->makeUploadDir();
                        rename($value['tmp_name'],'public/'.$newPath);
                        $studentFieldTable->saveField($studentId,$row->registration_field_id,$newPath);

                    }
                    else{
                        $studentFieldTable->saveField($studentId,$row->registration_field_id,'');
                    }
                }


                return jsonResponse(['status'=>true,'token'=>$token,'first_name'=>$student->first_name,'last_name'=>$student->last_name,'id'=>$student->student_id]);
        }
        catch(\Exception $ex){
            return jsonResponse(['status'=>false,'msg'=>$ex->getMessage()]);
        }
    }


    public function getToken($request, $response, $args){
        $token = $args['id'];
        $student = Student::where('api_token',$token)->where('token_expires','>',time())->first();
        if($student){
            $status = true;
        }
        else{
            $status = false;
        }
        return jsonResponse(['status'=>$status]);
    }


    public function getProfile(Request $request,Response $response,$args){

        $id = $args['id'];
        $student = $this->getApiStudent();
        if($student->student_id != $id){
            return jsonResponse([
               'status'=>false,
                'msg'=>'You do not have access to the profile'
            ]);
        }

         $student= $this->getApiStudent();


        $data = $student->toArray();

        $studentFieldTable = new StudentFieldTable();
        $records= $studentFieldTable->getStudentRecords($this->getApiStudentId());

        foreach($records as $record){
            $data['custom_'.$record->registration_field_id] = $record->value;
        }

       return jsonResponse([
           'status'=>true,
           'data'=>$data
       ]);


    }

    public function updateProfile(Request $request,Response $response,$args){

        $id = $args['id'];
        $params = $request->getParsedBody();

        $rules = [
            'first_name'=>'required',
            'last_name'=>'required',
            'mobile_number'=>'required',
            'email'=>'required|valid_email',
        ];

        foreach(RegistrationField::where('status',1)->orderBy('sort_order')->get() as $row){

            if($row->required==1 && $row->type != 'checkbox'){
                $rules['custom_'.$row->registration_field_id] = 'required';
            }

        }

        $this->validateParams($params,$rules);

        $row = $this->getApiStudent();
        $studentsTable = new StudentTable();
        $registrationFieldsTable = new RegistrationFieldTable();
        $studentFieldTable = new StudentFieldTable();

        $data = removeTags($params);

        $array = [
            'first_name'=>$data['first_name'],
            'last_name'=>$data['last_name'],
            'mobile_number'=>$data['mobile_number'],
            'email'=>$data['email'],
            'status'=>$data['status'],
        ];

        if(!empty($data['picture']['name'])){
            @unlink('public/'.$row->picture);

            $file = $data['picture']['name'];
            $newPath = $this->uploadDir.'/'.time().$id.'_'.sanitize($file);
            $this->makeUploadDir();
            rename($data['picture']['tmp_name'],'public/'.$newPath);
            $array['picture'] = $newPath;

        }


        $array[$studentsTable->getPrimary()]=$id;
        $studentsTable->saveRecord($array);



        $fields= $registrationFieldsTable->getActiveFields();
        foreach($fields as $row){


            $fieldRow = $studentFieldTable->getStudentFieldRecord($id,$row->registration_field_id);
            $value = $data['custom_'.$row->registration_field_id];
            if($row->type != 'file'){

                $studentFieldTable->saveField($id,$row->registration_field_id,$value);
            }
            elseif(!empty($value['name'])){

                @unlink('public/'.$fieldRow->value);

                $file = $value['name'];
                $newPath = $this->uploadDir.'/'.time().$id.'_'.sanitize($file);
                $this->makeUploadDir();
                rename($value['tmp_name'],'public/'.$newPath);
                $studentFieldTable->saveField($id,$row->registration_field_id,$newPath);

            }
        }

        $student= $this->getApiStudent();


        $data = $student->toArray();

        $studentFieldTable = new StudentFieldTable();
        $records= $studentFieldTable->getStudentRecords($this->getApiStudentId());

        foreach($records as $record){
            $data['custom_'.$record->registration_field_id] = $record->value;
        }

        return jsonResponse([
            'status'=>true,
            'data'=>$data
        ]);


    }


    public function saveProfilePhotoOld(Request $request,Response $response,$args){

       $params = $request->getParsedBody();



       $this->validateParams($params,[
           'picture'=>'required'
       ]);

        $data = $params['picture'];
        try{

                $data = base64_decode($data);

                $im = imagecreatefromstring($data);

                if ($im == false) {
                    return jsonResponse(['status'=>false,'msg'=>'Invalid image supplied']);
                }


                $row = $this->getApiStudent();

                //delete current pic
                @unlink('public/'.$row->picture);
                $id = $row->student_id;

                $newPath = $this->uploadDir.'/'.time().$id.'_'.sanitize($row->first_name).'.jpg';

                $this->makeUploadDir();
              //  touch($newPath);
                imagejpeg($im,'public/'.$newPath);
                crop_img('public/'.$newPath);
/*
            // Create new imagick object
            $file ='public/'.$newPath;
            $im = new \Imagick($file);

// Optimize the image layers
            $im->optimizeImageLayers();

// Compression and quality
            $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
            $im->setImageCompressionQuality(25);

// Write the image back
            $im->writeImages($file, true);*/

                chmod('public/'.$newPath,0777);


                $row->picture = $newPath;
                $row->save();
                return jsonResponse(['status'=>true]);

        }catch(\Exception $ex){
            return jsonResponse(['status'=>false,'msg'=>$ex->getMessage().'<br/>'.$ex->getTraceAsString()]);
        }


    }


    public function saveProfilePhoto(Request $request,Response $response,$args){

        $params['picture'] = $_FILES['picture']['tmp_name'];

        $this->validateParams($params,[
            'picture'=>'required'
        ]);

        $data = $params['picture'];
        try{

         //   $data = base64_decode($data);
            $im=false;
            //$im = imagecreatefromstring($data);
            $file= $_FILES['picture']['tmp_name'];
            $ext =getExtensionForMime($file);

            if($ext=='jpg'){
                $im = imagecreatefromjpeg($file);
            }
            elseif($ext=='png'){
                $im = imagecreatefrompng($file);
            }

            if ($im == false) {
                return jsonResponse(['status'=>false,'msg'=>'Invalid image supplied']);
            }


            $row = $this->getApiStudent();

            //delete current pic
            @unlink('public/'.$row->picture);
            $id = $row->student_id;

            $newPath = $this->uploadDir.'/'.time().$id.'_'.sanitize($row->first_name).'.jpg';

            $this->makeUploadDir();
            //  touch($newPath);
            imagejpeg($im,'public/'.$newPath);
            crop_img('public/'.$newPath);
            /*
                        // Create new imagick object
                        $file ='public/'.$newPath;
                        $im = new \Imagick($file);

            // Optimize the image layers
                        $im->optimizeImageLayers();

            // Compression and quality
                        $im->setImageCompression(\Imagick::COMPRESSION_JPEG);
                        $im->setImageCompressionQuality(25);

            // Write the image back
                        $im->writeImages($file, true);*/

            chmod('public/'.$newPath,0777);


            $row->picture = $newPath;
            $row->save();
            return jsonResponse(['status'=>true]);

        }catch(\Exception $ex){
            return jsonResponse(['status'=>false,'msg'=>$ex->getMessage().'<br/>'.$ex->getTraceAsString()]);
        }


    }




    public function removeProfilePhoto(){

        $row = $this->getApiStudent();

        //delete current pic
        @unlink('public/'.$row->picture);
        $row->picture = '';
        $row->save();
        return jsonResponse(['status'=>true]);

    }

    public function changePassword(Request $request,Response $response,$args){

        $data = $request->getParsedBody();

        $this->validateParams($data,[
            'password'=>'required|max_len,100|min_len,6'
        ]);

        $data['password'] = md5($data['password']);

        $student = $this->getApiStudent();
        $student->password = $data['password'];
        $student->save();

        return jsonResponse([
           'status'=>true,
            'msg'=>'Password changed!'
        ]);

    }

    public function resetPassword(Request $request,Response $response,$args){
        $data = $request->getParsedBody();

        $this->validateParams($data,[
            'email'=>'required'
        ]);

        $email = $data['email'];
        $student = Student::where('email',$email)->first();

        if(!$student){
            return jsonResponse([
                'status'=>false,
                'msg'=>'This email address is not associated with any account'
            ]);
        }

        $resetTable = new PasswordResetTable();
        $token = $resetTable->addEntry($email);
        $url = $this->getBaseApiUrl($request).'/student/change-password?token='.$token;

        $mode = getenv('APP_MODE');
        if($mode != 'demo'){

            $title = 'Password Reset';
            $senderName = $this->getSetting('general_site_name');
            $firstName = $student->first_name;
            $recipientEmail = $email;

            $message = "
                    Dear $firstName, <br/>
                    You have requested to change your password. Please click this url to change it now <br/>
                    <a href=\"$url\">$url</a> <br/>
                    Note: If you did not make this request, please ignore this email. <br/>
                    $senderName
                    ";

            $this->sendEmail($recipientEmail,$title,$message);
        }
        $msg="We have sent you a link for resetting your password. Please check your email.";

        return jsonResponse([
           'status'=> true,
            'msg'=>$msg
        ]);

    }

    private function makeUploadDir(){
        $path = 'public/'.$this->uploadDir;
        if(!file_exists($path)){
            mkdir($path,0777,true);
            chmod($path,0777);
        }
    }




}