<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 1/19/2017
 * Time: 4:16 PM
 */

namespace Admin\Controller;


use Application\Controller\AbstractController;
use Application\Entity\Coupon;
use Application\Entity\CouponCategory;
use Application\Entity\CouponSession;
use Application\Entity\Currency;
use Application\Entity\PaymentMethodCurrency;
use Application\Form\PaymentMethodForm;
use Application\Model\CountryTable;
use Application\Model\PaymentMethodFieldTable;
use Application\Model\PaymentMethodTable;
use Application\Model\SessionCategoryTable;
use Application\Model\SessionTable;
use Application\Model\SessionToSessionCategoryTable;
use Intermatics\BaseForm;
use Intermatics\HelperTrait;
use Intermatics\UtilityFunctions;
use Zend\Form\Element\Select;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class PaymentController extends AbstractController {

    use HelperTrait;
    public function indexAction()
    {
        $table = new PaymentMethodTable($this->getServiceLocator());
        $countryTable = new CountryTable($this->getServiceLocator());

        $paginator = $table->getPaginatedRecords(true);

        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(30);

        $select = new Select('currency');
        $select->setAttribute('id','currencyselect')
                ->setAttribute('class','form-control')
            ->setAttribute('data-sort','currency');
        $select->setEmptyOption(__('Select Currency'));

        $options = [];
        $options['ANY'] = __('Any Currency');
        $rowset = $countryTable->getRecords();
        foreach($rowset as $row)
        {
            $options[$row->currency_code] = $row->currency_name;
        }
        $select->setValueOptions($options);
        return new ViewModel (array(
            'paginator'=>$paginator,
            'pageTitle'=>__('Payment Methods'),
            'select'=>$select
        ));

    }

    public function editAction(){
        $paymentMethodTable = new PaymentMethodTable($this->getServiceLocator());
        $fieldsTable = new PaymentMethodFieldTable($this->getServiceLocator());
        $id = $this->params('id');
        $pmRow = $paymentMethodTable->getRecord($id);
        $form = new PaymentMethodForm(null,$this->getServiceLocator(),$id);
        $output = [];
        $fields = $fieldsTable->getRecordsForMethod($id);
        $fields->buffer();

        if($this->request->isPost()){
            $data = $this->request->getPost();
            $paymentMethodTable->update(['status'=>$data['status'],'sort_order'=>$data['sort_order'],'method_label'=>$data['method_label'],'is_global'=>$data['is_global']],$id);

            foreach($fields as $row){

                $fieldsTable->updateValue($data[$row->key],$row->key,$id);

            }

            $this->flashMessenger()->addMessage(__('pm-settings-saved',['paymentMethod'=>$pmRow->payment_method]));
            $this->redirect()->toRoute('admin/default',['controller'=>'payment','action'=>'index']);

        }
        else{
            $formData = [];
            foreach($fields as $row){
                $formData[$row->key] = $row->value;

            }
            $formData['status'] = $pmRow->status;
            $formData['sort_order']= $pmRow->sort_order;
            $formData['method_label'] = $pmRow->method_label;
            $formData['is_global'] = $pmRow->is_global;
            $form->setData($formData);
        }
        $output['fields'] = $fields;
        $output['form'] = $form;
        $output['pageTitle'] = __('Edit Payment Method').': '.$pmRow->payment_method;
        $output['id'] = $id;

        return $output;

    }

    public function currenciesAction(){
        $currencies = Currency::get();
        $id = $this->params('id');
        if($this->request->isPost()){

            $currency = $this->request->getPost('currency');
            if(!PaymentMethodCurrency::where('currency_id',$currency)->where('payment_method_id',$id)->first())
            {
                $model = new PaymentMethodCurrency();
                $model->currency_id = $currency;
                $model->payment_method_id = $id;
                $model->save();
            }


        }

        //get list of payment methods
        $rowset = PaymentMethodCurrency::where('payment_method_id',$id)->get();
        return $this->blade('admin.payment.currencies',compact('currencies','rowset'));
    }

    public function deletecurrencyAction(){
        $id = $this->params('id');
        $paymentMethodCurrency = PaymentMethodCurrency::find($id);
        $method = $paymentMethodCurrency->payment_method_id;
        $paymentMethodCurrency->delete();

        $response = $this->forward()->dispatch('Admin\Controller\Payment',['action'=>'currencies','id'=>$method]);
        return $response;

    }

    public function couponsAction(){

        $this->data['coupons'] = Coupon::orderBy('coupon_id','desc')->paginate(20);
        $this->data['pageTitle'] = __('Coupons');
        return $this->data;
    }

    public function addcouponAction(){

        $form = $this->couponForm();

        if($this->request->isPost()){

            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                $data = $this->setNull($data);
                $data['expires'] = strtotime($data['expires']);
                $data['date_start'] = strtotime($data['date_start']);
                $data['code'] = trim(strtolower(safeUrl($data['code'])));
                $data['discount'] = $this->checkDiscount($data['discount'],$data['type']);
                $coupon = Coupon::create($data);
                $this->saveCouponData($coupon,$formData);
                $this->flashMessenger()->addMessage('Coupon created');
                return $this->redirect()->toRoute('admin/default',['controller'=>'payment','action'=>'coupons']);
            }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }

        }

        $this->data['pageTitle'] = __('Add Coupon');
        $this->data['form'] = $form;
        return $this->data;

    }

    public function editcouponAction(){
        $form = $this->couponForm();
        $id = $this->params('id');
        $coupon = Coupon::find($id);
        if($this->request->isPost()){

            $formData = $this->request->getPost();
            $form->setData($formData);
            if($form->isValid()){
                $data = $form->getData();
                $data = $this->setNull($data);
                $data['expires'] = strtotime($data['expires']);
                $data['date_start'] = strtotime($data['date_start']);
                $data['code'] = trim(strtolower(safeUrl($data['code'])));
                $data['discount'] = $this->checkDiscount($data['discount'],$data['type']);
                $coupon->fill($data);
                $coupon->save();
                $this->saveCouponData($coupon,$formData);
                $this->flashMessenger()->addMessage('Coupon saved');
                return $this->redirect()->toRoute('admin/default',['controller'=>'payment','action'=>'coupons']);
            }
            else{
                $this->data['message'] = $this->getFormErrors($form);
            }

        }
        else{

            $data = $coupon->toArray();
            $data['expires'] = date('Y-m-d',$coupon->expires);
            $data['date_start'] = date('Y-m-d',$coupon->date_start);

            foreach($coupon->couponCategories as $groupRow){
                $data['categories[]'][] = $groupRow->session_category_id;
            }

            foreach($coupon->couponSessions as $groupRow){
                $data['sessions[]'][] = $groupRow->session_id;
            }


            $form->setData($data);
        }

        $this->data['pageTitle'] = __('Edit Coupon');
        $this->data['form'] = $form;
        $viewModel = new ViewModel($this->data);
        $viewModel->setTemplate('admin/payment/addcoupon');
        return $viewModel;
    }

    public function setNull($data){
        foreach($data as $key=>$value){
            $data[$key] = empty($value)? null:$value;
        }
        return $data;
    }

    private function saveCouponData(Coupon $coupon,$data){

        $coupon->couponCategories()->delete();
        $coupon->couponSessions()->delete();

        foreach($data['sessions'] as $value){

            CouponSession::create([
               'coupon_id'=>$coupon->coupon_id,
                'session_id'=>intval($value[0])
            ]);
        }

        foreach($data['categories'] as $value){

            CouponCategory::create([
                'coupon_id'=>$coupon->coupon_id,
                'session_category_id'=>intval($value[0])
            ]);
        }

    }

    private function checkDiscount($discount,$type){
        if($discount> 100 && $type=='P'){
            $discount = 100;
        }
        elseif($discount < 1){
            $discount =1;
        }

        return $discount;
    }

    public function deletecouponAction(){
        $id = $this->params('id');
        $coupon = Coupon::find($id);
        $coupon->delete();
        $this->flashMessenger()->addMessage(__('Coupon deleted'));
        return $this->goBack();
    }

    private function couponForm(){
        $form= new BaseForm();
        $form->createText('code','Coupon Code',true,null,null,__('code-not-case'));
        $form->createText('discount','Discount',true,'form-control digit',null,__('Numbers only'));
        $form->createText('expires','End Date',true,'form-control date');

        $form->createSelect('enabled','Status',[1=>__('Enabled'),0=>__('Disabled')],true,false);
        $form->createText('name','Coupon Name',true);
        $form->createSelect('type','Type',[
           'P'=>__('Percentage'),
            'F'=>__('Fixed Amount')
        ],true,false);
        $form->createText('total','Total Amount',false,'form-control digit');
        $form->createText('date_start','Start Date',true,'form-control date');
        $form->createText('uses_total','Uses Per Coupon',false,'form-control number');
        $form->createText('uses_customer','Uses Per Customer',false,'form-control number');

        $options = [];
        $sessionCategoryTable = new SessionCategoryTable();
        $rowset = $sessionCategoryTable->getLimitedRecords(5000);
        foreach($rowset as $row){
            $options[$row->session_category_id]=$row->category_name;
        }

        $form->createSelect('categories[]','Course Categories',$options,false);
        $form->get('categories[]')->setAttribute('multiple','multiple');
        $form->get('categories[]')->setAttribute('class','form-control select2');


        $options = [];
        $sessionTable = new SessionTable();
        $rowset = $sessionTable->getLimitedRecords(5000);
        foreach($rowset as $row){
            $options[$row->session_id]=$row->session_name;
        }

        $form->createSelect('sessions[]','Courses',$options,false);
        $form->get('sessions[]')->setAttribute('multiple','multiple');
        $form->get('sessions[]')->setAttribute('class','form-control select2');




        $form->setInputFilter($this->couponFilter());
        return $form;
    }

    private function couponFilter(){
        $filter = new InputFilter();
        $filter->add([
            'name'=>'code',
            'required'=>true,
            'validators'=>[
        [
            'name'=>'NotEmpty'
        ]

            ]
        ]);
        $filter->add([
            'name'=>'discount',
            'required'=>true,
            'validators'=>[
                ['name'=>'NotEmpty']
            ]
        ]);

        $filter->add([
            'name'=>'expires',
            'required'=>true,
            'validators'=>[
                ['name'=>'NotEmpty']
            ]
        ]);

        $filter->add([
            'name'=>'enabled',
            'required'=>false,
        ]);



        $filter->add([
            'name'=>'name',
            'required'=>true,
            'validators'=>[
                ['name'=>'NotEmpty']
            ]
        ]);

        $filter->add([
            'name'=>'type',
            'required'=>true,
            'validators'=>[
                ['name'=>'NotEmpty']
            ]
        ]);

        $filter->add([
            'name'=>'total',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'date_start',
            'required'=>true,
            'validators'=>[
                ['name'=>'NotEmpty']
            ]
        ]);


        $filter->add([
            'name'=>'uses_total',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'uses_customer',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'categories[]',
            'required'=>false
        ]);

        $filter->add([
            'name'=>'sessions[]',
            'required'=>false
        ]);




        return $filter;
    }
}