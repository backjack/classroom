<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 2/4/2017
 * Time: 1:49 PM
 */

namespace Intermatics;


trait PaymentTrait {



    private function approve(){

        $session = new Container('enroll');
        $id = $session->id;
        if(empty($id)){
            return $this->redirect()->toRoute('application/enroll');
        }
        $sessionTable = new SessionTable($this->getServiceLocator());
        $row = $sessionTable->getRecord($id);

        $studentSessionTable = new StudentSessionTable($this->getServiceLocator());
        $code = generateRandomString(5);
        $studentSessionTable->addRecord(array(
            'student_id'=>$this->getId(),
            'session_id'=>$id,
            'reg_code'=>$code
        ));

        $student = $this->getStudent();
        $message = "<h4>Your enrollment code is $code</h4>";
        $emailMessage = $message.$this->getSetting('regis_email_message',$this->getServiceLocator());
        $this->sendEmail($student->email,'Enrollment Complete',$emailMessage,$this->getServiceLocator());
        $this->sendEnrollMessage($student,$row->session_name);
        $this->flashMessenger()->addMessage("You have successfully enrolled for a session! ".$emailMessage);
        unset($_SESSION['enroll']);
        return $this->redirect()->toRoute('application/enroll');

    }


}