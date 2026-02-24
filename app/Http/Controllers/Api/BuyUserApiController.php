<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{Admin,BuyUser,SellUser,BuySellUser,Agent,Settings,PropertyEnquiries,Properties,PropertyImages};
use Validator;
use DB;
use Carbon\Carbon;
use Notification;
use App\Exports\BuySellDataExport;
use Maatwebsite\Excel\Facades\Excel;
class BuyUserApiController extends Controller
{
 protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }


    public function exportBuySell(Request $request){
        $type = $request->get('type'); // SELL, BUY, BUYSELL, AGENT
    $fileName = $type . time() . '_data.xlsx';
    $filePath = public_path('upload/exports/' . $fileName);

    // Make sure folder exists
    if (!file_exists(public_path('upload/exports'))) {
        mkdir(public_path('upload/exports'), 0777, true);
    }

    // Store file temporarily in storage
    Excel::store(new BuySellDataExport($type), $fileName);

    // Copy it to public folder
    $tempPath = storage_path('app/' . $fileName);
    if (file_exists($tempPath)) {
        copy($tempPath, $filePath);
        unlink($tempPath); // optional: remove temp file
    }

    return response()->json([
        'success' => true,
        'file' => url('public/upload/exports/' . $fileName)
    ]);
   }

 public function getbuyuserlist(Request $request){
         try {
             $rules = array(
            'pageNo'=>'required',
            'pageSize'=>'required',
            'type'=>'required',
            'filtertype'=>'required'
        );

        $fieldNames = array(
            'pageNo'=>'pageNo',
            'pageSize'=>'pageSize',
            'type'=>'type',
            'filtertype'=>'filtertype'
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
        } else {
            if (!in_array($request->type, ['SELL', 'BUYSELL', 'BUY','AGENT'], true)) {
    return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => 'Please enter correct type'
    ], 200);
} 
       // Map request type to model class
// Allowed types
$modelMap = [
    'SELL'    => SellUser::class,
    'BUYSELL' => BuySellUser::class,
    'BUY'     => BuyUser::class,
    'AGENT'   => Agent::class,
];


    $model = $modelMap[$request->type];

    $data = $model::where('is_email_verified', 1)
        ->orderBy('id', 'desc')
        ->where('status',$request->filtertype)->paginate(
            max((int) $request->pageSize, 10),
            ['*'],
            'page',
            max((int) $request->pageNo, 1)
        );

return $this->apiResponse(['status' => 'success', 'data' => $data,'mesg'=>'data fetched  successfully'], 200);
    }
        }catch (Exception $qx) {
          return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => $e->getMessage()
    ], 500);
        }
    }


// getbuyuserlist


public function getDashboardCount(Request $request)
{
    try {
        // Example: Replace $model with your actual model class


        $today       = Carbon::today();
        $yesterday   = Carbon::yesterday();
        $weekStart   = Carbon::now()->startOfWeek();   // Monday by default
        $weekEnd     = Carbon::now()->endOfWeek();     // Sunday by default
        $monthStart  = Carbon::now()->startOfMonth();
        $monthEnd    = Carbon::now()->endOfMonth();


        $data['today_sellcount'] = SellUser::where('is_email_verified', 1)
            ->whereDate('created_at', $today)
            ->count();

        $data['yesterday_sellcount'] = SellUser::where('is_email_verified', 1)
            ->whereDate('created_at', $yesterday)
            ->count();

        $data['week_sellcount'] = SellUser::where('is_email_verified', 1)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $data['months_sellcount'] = SellUser::where('is_email_verified', 1)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

// buy count
            $data['today_buycount'] = BuyUser::where('is_email_verified', 1)
            ->whereDate('created_at', $today)
            ->count();

        $data['yesterday_buycount'] = BuyUser::where('is_email_verified', 1)
            ->whereDate('created_at', $yesterday)
            ->count();
        
        $data['week_buycount'] = BuyUser::where('is_email_verified', 1)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $data['months_buycount'] = BuyUser::where('is_email_verified', 1)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();


            // sellbuy count
            $data['today_sellbuycount'] = BuySellUser::where('is_email_verified', 1)
            ->whereDate('created_at', $today)
            ->count();

        $data['yesterday_sellbuycount'] = BuySellUser::where('is_email_verified', 1)
            ->whereDate('created_at', $yesterday)
            ->count();

        $data['week_sellbuycount'] = BuySellUser::where('is_email_verified', 1)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $data['months_sellbuycount'] = BuySellUser::where('is_email_verified', 1)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();
            
            // agent count
            $data['today_agentcount'] = Agent::where('is_email_verified', 1)
            ->whereDate('created_at', $today)
            ->count();

        $data['yesterday_agentcount'] = Agent::where('is_email_verified', 1)
            ->whereDate('created_at', $yesterday)
            ->count();

        $data['week_agentcount'] = Agent::where('is_email_verified', 1)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $data['months_agentcount'] = Agent::where('is_email_verified', 1)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        return $this->apiResponse([
            'status' => 'success',
            'data'   => $data,
            'mesg'   => 'Data fetched successfully'
        ], 200);

    } catch (\Throwable $e) {
        return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $e->getMessage()
        ], 500);
    }
}



public function getfilterCount(Request $request)
{
    try {
        // Example: Replace $model with your actual model class


        $data['today_sell'] = SellUser::where('is_email_verified', 1)
            ->where('status', $request->filtertype)
            ->count();

   
// buy count
            $data['today_buy'] = BuyUser::where('is_email_verified', 1)
            ->where('status', $request->filtertype)
            ->count();

    

            // sellbuy count
            $data['today_sellbuy'] = BuySellUser::where('is_email_verified', 1)
             ->where('status', $request->filtertype)
            ->count();

    
            
            // agent count
            $data['today_agent'] = Agent::where('is_email_verified', 1)
             ->where('status', $request->filtertype)
            ->count();

         // productenquires count
            $data['today_propertyenquiries'] = PropertyEnquiries::where('is_email_verified', 1)
             ->where('enquiryStatus', $request->filtertype)
            ->count();

        return $this->apiResponse([
            'status' => 'success',
            'data'   => $data,
            'mesg'   => 'Data fetched successfully'
        ], 200);

    } catch (\Throwable $e) {
        return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $e->getMessage()
        ], 500);
    }
}

     public function addupdatebuyuser(Request $request){
         try {
             $rules = array(
            'state'=>'required',
            'propertyType'=>'required',
            'propertySubType'=>'required',
               'priceRange'=>'required',
            'firstName'=>'required',
            'lastName'=>'required',
                'email'=>'required',
            'countryCode'=>'required',
            'phoneNumber'=>'required',
        );

        $fieldNames = array(
            'state'=>'state',
            'propertyType'=>'propertyType',
            'propertySubType'=>'propertySubType',
                  'priceRange'=>'priceRange',
            'firstName'=>'firstName',
            'lastName'=>'lastName',
                 'email'=>'email',
            'countryCode'=>'countryCode',
            'phoneNumber'=>'phoneNumber',
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
          return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
        $otp=$this->helper->generatePIN(4);
       
    if(isset($request->id) && $request->id>0){

$userdata = BuyUser::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->state   = $request->state;
$userdata->property_type   =$request->propertyType;
$userdata->property_sub_type   = $request->propertySubType;
$userdata->price_range   =$request->priceRange;
$userdata->first_name   =$request->firstName;
$userdata->last_name=$request->lastName;
$userdata->email=$request->email;
$userdata->country_code   = $request->countryCode;
$userdata->phone_number   =$request->phoneNumber;
$userdata->email_otp  =$otp;
$userdata->update();
$userid=$request->id;
    }else{
         $userdata = new BuyUser;
$userdata->state   = $request->state;
$userdata->property_type   =$request->propertyType;
$userdata->property_sub_type   = $request->propertySubType;
$userdata->price_range   =$request->priceRange;
$userdata->first_name   =$request->firstName;
$userdata->last_name=$request->lastName;
$userdata->email=$request->email;
$userdata->country_code   = $request->countryCode;
$userdata->phone_number   =$request->phoneNumber;
$userdata->email_otp  =$otp;
$userdata->save();  
$userid=$userdata->id;
    }


    
if($userid >0){
 $usr_subject='Your OTP for verification with RealBucs';
 $otpdata=array('name'=> $request->firstName,
 'email'=>$request->email,
 'otp'=>$otp);
 $html = view('mailer.otpmailer')->with(compact('otpdata'))->render();
 $usr_message= $html ;
 $attach='';
//  dd($html);
 sentmailsmtp($request->email,$usr_subject,$usr_message, $attach);
 $data=array(
'id'=>$userid
 );
 return $this->apiResponse(['status' => 'success', 'data' =>$data ,'mesg'=>'data added successfully'], 200);
}



return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }
   

    

         public function addupdateselluser(Request $request){
         try {
             $rules = array(
            'state'=>'required',
            'propertyType'=>'required',
            'propertySubType'=>'required',
               'priceRange'=>'required',
            'firstName'=>'required',
            'lastName'=>'required',
                'email'=>'required',
            'countryCode'=>'required',
            'phoneNumber'=>'required',
        );

        $fieldNames = array(
            'state'=>'state',
            'propertyType'=>'propertyType',
            'propertySubType'=>'propertySubType',
                  'priceRange'=>'priceRange',
            'firstName'=>'firstName',
            'lastName'=>'lastName',
                 'email'=>'email',
            'countryCode'=>'countryCode',
            'phoneNumber'=>'phoneNumber',
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
        $otp=$this->helper->generatePIN(4);
       
    if(isset($request->id) && $request->id>0){

$userdata = SellUser::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->state   = $request->state;
$userdata->property_type   =$request->propertyType;
$userdata->property_sub_type   = $request->propertySubType;
$userdata->price_range   =$request->priceRange;
$userdata->first_name   =$request->firstName;
$userdata->last_name=$request->lastName;
$userdata->email=$request->email;
$userdata->country_code   = $request->countryCode;
$userdata->phone_number   =$request->phoneNumber;
$userdata->email_otp  =$otp;
$userdata->update();
$userid=$request->id;
    }else{
         $userdata = new SellUser;
$userdata->state   = $request->state;
$userdata->property_type   =$request->propertyType;
$userdata->property_sub_type   = $request->propertySubType;
$userdata->price_range   =$request->priceRange;
$userdata->first_name   =$request->firstName;
$userdata->last_name=$request->lastName;
$userdata->email=$request->email;
$userdata->country_code   = $request->countryCode;
$userdata->phone_number   =$request->phoneNumber;
$userdata->email_otp  =$otp;
$userdata->save();  
$userid=$userdata->id;
    }


    
if($userid >0){
 $usr_subject='Your OTP for verification with RealBucs';
 $otpdata=array('name'=> $request->firstName,
 'email'=>$request->email,
 'otp'=>$otp);
 $html = view('mailer.otpmailer')->with(compact('otpdata'))->render();
 $usr_message= $html ;
 $attach='';
//  dd($html);
 sentmailsmtp($request->email,$usr_subject,$usr_message, $attach);
 $data=array(
'id'=>$userid
 );
 return $this->apiResponse(['status' => 'success', 'data' =>$data ,'mesg'=>'data added successfully'], 200);
}



return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }

   

           public function addupdatebuyselluser(Request $request){
         try {
             $rules = array(
            'sellState'=>'required',
            'sellPropertyType'=>'required',
            'sellPropertySubType'=>'required',
               'sellPriceRange'=>'required',

                  'buyState'=>'required',
            'buyPropertyType'=>'required',
            'buyPropertySubType'=>'required',
               'buyPriceRange'=>'required',

            'firstName'=>'required',
            'lastName'=>'required',
                'email'=>'required',
            'countryCode'=>'required',
            'phoneNumber'=>'required',
        );

        $fieldNames = array(
            'sellState'=>'sellState',
            'sellPropertyType'=>'sellPropertyType',
            'sellPropertySubType'=>'sellPropertySubType',
                  'sellPriceRange'=>'sellPriceRange',

                      'buyState'=>'buyState',
            'buyPropertyType'=>'buyPropertyType',
            'buyPropertySubType'=>'buyPropertySubType',
                  'buyPriceRange'=>'buyPriceRange',

            'firstName'=>'firstName',
            'lastName'=>'lastName',
                 'email'=>'email',
            'countryCode'=>'countryCode',
            'phoneNumber'=>'phoneNumber',
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
        return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
        $otp=$this->helper->generatePIN(4);
       
    if(isset($request->id) && $request->id>0){

$userdata = BuySellUser::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->sellState   = $request->sellState;
$userdata->sellPropertyType   =$request->sellPropertyType;
$userdata->sellPropertySubType   = $request->sellPropertySubType;
$userdata->sellPriceRange   =$request->sellPriceRange;

$userdata->buyState   = $request->buyState;
$userdata->buyPropertyType   =$request->buyPropertyType;
$userdata->buyPropertySubType   = $request->buyPropertySubType;
$userdata->buyPriceRange   =$request->buyPriceRange;

$userdata->first_name   =$request->firstName;
$userdata->last_name=$request->lastName;
$userdata->email=$request->email;
$userdata->country_code   = $request->countryCode;
$userdata->phone_number   =$request->phoneNumber;
$userdata->email_otp  =$otp;
$userdata->update();
$userid=$request->id;
    }else{
         $userdata = new BuySellUser;
$userdata->sellState   = $request->sellState;
$userdata->sellPropertyType   =$request->sellPropertyType;
$userdata->sellPropertySubType   = $request->sellPropertySubType;
$userdata->sellPriceRange   =$request->sellPriceRange;

$userdata->buyState   = $request->buyState;
$userdata->buyPropertyType   =$request->buyPropertyType;
$userdata->buyPropertySubType   = $request->buyPropertySubType;
$userdata->buyPriceRange   =$request->buyPriceRange;

$userdata->first_name   =$request->firstName;
$userdata->last_name=$request->lastName;
$userdata->email=$request->email;
$userdata->country_code   = $request->countryCode;
$userdata->phone_number   =$request->phoneNumber;
$userdata->email_otp  =$otp;
$userdata->save();  
$userid=$userdata->id;
    }


    
if($userid >0){
 $usr_subject='Your OTP for verification with RealBucs';
 $otpdata=array('name'=> $request->firstName,
 'email'=>$request->email,
 'otp'=>$otp);
 $html = view('mailer.otpmailer')->with(compact('otpdata'))->render();
 $usr_message= $html ;
 $attach='';
//  dd($html);
 sentmailsmtp($request->email,$usr_subject,$usr_message, $attach);
 $data=array(
'id'=>$userid
 );
 return $this->apiResponse(['status' => 'success', 'data' =>$data ,'mesg'=>'data added successfully'], 200);
}



return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }

         public function verifyemail(Request $request){
         try {
             $rules = array(
            'otp'=>'required',
            'formId'=>'required',
            'type'=>'required',
        );

        $fieldNames = array(
            'otp'=>'otp',
            'formId'=>'formId',
            'type'=>'type',
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
       $userdata=null;
    if(isset($request->formId) && $request->formId>0){

if (!in_array($request->type, ['SELL', 'BUYSELL', 'BUY','AGENT','PROPERTY'], true)) {
    return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => 'Please enter correct type'
    ], 200);
} 
              // Map request type to model class
    $modelMap = [
        'SELL'    => SellUser::class,
        'BUYSELL' => BuySellUser::class,
        'BUY'     => BuyUser::class,
        'AGENT'   => Agent::class,
        'PROPERTY'   => PropertyEnquiries::class,
    ];

    $model = $modelMap[$request->type];
$userdata = $model::find($request->formId);

if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}

if($userdata->email_otp==$request->otp){

$userdata->is_email_verified  =1;
$userdata->update();


 return $this->apiResponse(['status' => 'success', 'mesg'=>'otp verify  successfully'], 200);
}else{
 return $this->apiResponse(['status' => 'failed', 'mesg'=>'Please enter correct otp'], 200);

}


    }




return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }
   
   
         public function resendotp(Request $request){
         try {
             $rules = array(
            'formId'=>'required',
            'type'=>'required',
        );

        $fieldNames = array(
            'formId'=>'formId',
            'type'=>'type',
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
        return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
       $userdata=null;
         $otp=$this->helper->generatePIN(4);
    if(isset($request->formId) && $request->formId>0){


if (!in_array($request->type, ['SELL', 'BUYSELL', 'BUY','AGENT','PROPERTY','PROPERTYENQUIRY'], true)) {
    return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => 'Please enter correct type'
    ], 200);
}

 $modelMap = [
        'SELL'    => SellUser::class,
        'BUYSELL' => BuySellUser::class,
        'BUY'     => BuyUser::class,
        'AGENT'   => Agent::class,
        'PROPERTY' => Properties::class,
        'PROPERTYENQUIRY' => PropertyEnquiries::class,
    ];

    $model = $modelMap[$request->type];
$userdata = $model::find($request->formId);


if($userdata===null){
      return $this->apiResponse(['status' => 'failed', 'mesg'=>'Invalid form id '], 200);
}

if($otp!=''){

$userdata->email_otp  =$otp;
$userdata->update();


 $usr_subject='Your OTP for verification with RealBucs';
 $otpdata=array('name'=> $userdata->firstName,
 'email'=>$userdata->email,
 'otp'=>$otp);
 $html = view('mailer.otpmailer')->with(compact('otpdata'))->render();
 $usr_message= $html ;
 $attach='';

 sentmailsmtp($userdata->email,$usr_subject,$usr_message, $attach);

 return $this->apiResponse(['status' => 'success', 'data' => $userdata->id,'mesg'=>'Otp send successfully'], 200);
}else{
 return $this->apiResponse(['status' => 'failed', 'mesg'=>'Error in generating resend otp'], 200);

}


    }




return $this->apiResponse(['status' => 'failed', 'data' =>[],'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }
   

     public function adminlogin(Request $request){
         try {
             $rules = array(
            'emailid'=>'required',
            'password'=>'required',
        );

        $fieldNames = array(
            'emailid'=>'emailid',
            'password'=>'password',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
        } else {

           $password= md5($request->password);
            $admindata=Admin::where('email',$request->emailid)->where('password',$password)->first();
   
if($admindata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid email id or password '], 200);
}

return $this->apiResponse(['status' => 'success', 'data' => $admindata,'mesg'=>'data fetched  successfully'], 200);
    }
        }catch (Exception $qx) {
          return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => $e->getMessage()
    ], 500);
        }
    }




     public function addupdateagentuser(Request $request){
         try {
             $rules = array(
            'firstName'=>'required',
            'lastName'=>'required',
            'agentId'=>'required',
            'brokerageCompany'=>'required',
            'email'=>'required',
            'phoneNumber'=>'required',
            'experience'=>'required',
            'states'=>'required',
            'countryCode'=>'required',
            
        );

        $fieldNames = array(
            'firstName'=>'firstName',
            'lastName'=>'lastName',
            'agentId'=>'agentId',
            'brokerageCompany'=>'brokerageCompany',
            'email'=>'email',
            'phoneNumber'=>'phoneNumber',
            'experience'=>'experience',
            'states'=>'states',
            'countryCode'=>'countryCode'
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
          return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
        $otp=$this->helper->generatePIN(4);
       
    if(isset($request->id) && $request->id>0){

$userdata = Agent::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->firstName   = $request->firstName;
$userdata->lastName   =$request->lastName;
$userdata->agentId   = $request->agentId;
$userdata->brokerageCompany   =$request->brokerageCompany;
$userdata->email   =$request->email;
$userdata->phoneNumber=$request->phoneNumber;
$userdata->experience=$request->experience;
$userdata->states   = $request->states;
$userdata->countryCode   =$request->countryCode;
$userdata->email_otp  =$otp;
$userdata->update();
$userid=$request->id;
    }else{
         $userdata = new Agent;
$userdata->firstName   = $request->firstName;
$userdata->lastName   =$request->lastName;
$userdata->agentId   = $request->agentId;
$userdata->brokerageCompany   =$request->brokerageCompany;
$userdata->email   =$request->email;
$userdata->phoneNumber=$request->phoneNumber;
$userdata->experience=$request->experience;
$userdata->states   = $request->states;

$userdata->countryCode   =$request->countryCode;
$userdata->email_otp  =$otp;
$userdata->save();  
$userid=$userdata->id;
    }


    
if($userid >0){
 $usr_subject='Your OTP for verification with RealBucs';
 $otpdata=array('name'=> $request->firstName,
 'email'=>$request->email,
 'otp'=>$otp);
 $html = view('mailer.otpmailer')->with(compact('otpdata'))->render();
 $usr_message= $html ;
 $attach='';
//  dd($html);
 sentmailsmtp($request->email,$usr_subject,$usr_message, $attach);
 $data=array(
'id'=>$userid
 );
 return $this->apiResponse(['status' => 'success', 'data' =>$data ,'mesg'=>'data added successfully'], 200);
}



return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }
    
      public function updateagent(Request $request){
         try {
             $rules = array(
            'formId'=>'required',
            'prefrenceType'=>'required',
              'propertyType'=>'required'
        );

        $fieldNames = array(
            'formId'=>'formId',
            'prefrenceType'=>'prefrenceType',
              'propertyType'=>'propertyType'
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
       $userdata=null;
    if(isset($request->formId) && $request->formId>0){


$userdata = Agent::find($request->formId);

if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}

$userdata->prefrenceType   =$request->prefrenceType;
$userdata->propertyType   =$request->propertyType;
$userdata->cashbackBuyer   = $request->cashbackBuyer   !== '' ? $request->cashbackBuyer : '';
$userdata->agentCommission = $request->agentCommission !== '' ? $request->agentCommission : '';
$userdata->update();
 return $this->apiResponse(['status' => 'success', 'mesg'=>'data update  successfully'], 200);
    }
return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }

       public function addupdatesettings(Request $request){
         try {
             $rules = array(
            'id'=>'required',
            'value'=>'required'
        );

        $fieldNames = array(
            'id'=>'id',
            'value'=>'value'
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
          return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
       
    if(isset($request->id) && $request->id>0){

$userdata = Settings::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->value   =$request->value;
$userdata->update();
$userid=$request->id;
$message='data updated successfully';
    }else{
         $userdata = new Settings;
$userdata->name   = $request->name;
$userdata->value   =$request->value;
$userdata->type   =$request->type;
$userdata->save();  
$userid=$userdata->id;
$message='data added successfully';
    }

if($userid >0){
 return $this->apiResponse(['status' => 'success', 'data' =>$userid ,'mesg'=>$message], 200);
}
return $this->apiResponse(['status' => 'failed', 'mesg'=>'Some error occur'], 200);
    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }
   
   

 public function getsettings(Request $request){
         try {

    $data = Settings::where('id','>', 0)->get();
    

return $this->apiResponse(['status' => 'success', 'data' => $data,'mesg'=>'data fetched  successfully'], 200);
    
        }catch (Exception $qx) {
          return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => $e->getMessage()
    ], 500);
        }
    }

     public function deleteuserbyid(Request $request){
         try {
             $rules = array(
            'id'=>'required',
            'type'=>'required',
        );

        $fieldNames = array(
            'id'=>'id',
            'type'=>'type',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
        } else {
            if (!in_array($request->type, ['SELL', 'BUYSELL', 'BUY','AGENT','PROPERTYENQUIRY','PROPERTY'], true)) {
    return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => 'Please enter correct type'
    ], 200);
} 
       // Map request type to model class
// Allowed types
$modelMap = [
    'SELL'    => SellUser::class,
    'BUYSELL' => BuySellUser::class,
    'BUY'     => BuyUser::class,
    'AGENT'   => Agent::class,
    'PROPERTYENQUIRY' => PropertyEnquiries::class,
    'PROPERTY' => Properties::class,
];
// Properties,PropertyImages

    $model = $modelMap[$request->type];

   $data = $model::where('id', $request->id)->delete();

if ($data) {

if($request->type=='PROPERTY'){

      $imagedata = PropertyImages::where('property_id', $request->id)->delete();
}

    return $this->apiResponse([
        'status' => 'success',
        'data'   => $data,
        'mesg'   => 'Record deleted successfully'
    ], 200);
} else {
    return $this->apiResponse([
        'status' => 'error',
        'data'   => $data,
        'mesg'   => 'No record found to delete'
    ], 404);
}
    }
        }catch (Exception $qx) {
          return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => $e->getMessage()
    ], 500);
        }
    }


    
      public function updateStatus(Request $request){
         try {
             $rules = array(
            'formId'=>'required',
            'type'=>'required',
            'status'=>'required',
        );

        $fieldNames = array(
            'formId'=>'formId',
            'type'=>'type',
            'status'=>'status',
        );
     
         $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

    if ($validator->fails()) {
        return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
    } else {
       $userdata=null;
     
    if(isset($request->formId) && $request->formId>0){


if (!in_array($request->type, ['SELL', 'BUYSELL', 'BUY','AGENT','PROPERTY','PROPERTYENQUIRY'], true)) {
    return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => 'Please enter correct type'
    ], 200);
}

 $modelMap = [
    'SELL'     => SellUser::class,
    'BUYSELL'  => BuySellUser::class,
    'BUY'      => BuyUser::class,
    'AGENT'    => Agent::class,
    'PROPERTY' => Properties::class,
    'PROPERTYENQUIRY' => PropertyEnquiries::class,
];

    $model = $modelMap[$request->type];
    $userdata = $model::find($request->formId);


if($userdata===null){
      return $this->apiResponse(['status' => 'failed', 'mesg'=>'Invalid form id '], 200);
}

if($request->status!=''){

$property =  $model::find($request->formId);
$status = $request->status;

if ($request->type == 'PROPERTY') {
    if ($status == 'Open') {
        $status = 'Active';
    } else {
        $status = 'Inactive';
    }
}

if ($request->type == 'PROPERTY') {
    $property->PropertyStatus =  $status;
}elseif($request->type == 'PROPERTYENQUIRY'){
     $property->enquiryStatus =  $status;
}else{
     $property->status =  $status;
}

   
    $property->save();

 return $this->apiResponse(['status' => 'success', 'data' => $userdata->id,'mesg'=>'Status Update successfully'], 200);
}else{
 return $this->apiResponse(['status' => 'failed', 'mesg'=>'Error in update status'], 200);

}


    }




return $this->apiResponse(['status' => 'failed', 'data' =>[],'mesg'=>'Some error occur'], 200);


    }
        }catch (Exception $qx) {
             return $this->apiResponse([
        'status' => 'error',
        'message' =>  $qx
    ], 500);
        }
    }
   
   
   public function uploadimage(Request $request)
{
    try {
        // Validate file upload and allowed types
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120' // max 5MB
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = 'image_' . date('Ymd_His') . '.' . $extension;

        // Store in public/upload
        $file->move(public_path('upload'), $filename);

        // Save to database (optional)
        // $imageupload = new Imageupload;
        // $imageupload->image_path = $filename;
        // $imageupload->save();

        return $this->apiResponse([
            'status' => 'success',
            'data' => $filename,
            'mesg' => 'Data inserted successfully',
            'imagepath' =>env('APP_URL').'/public/upload/'
        ], 200);

    } catch (\Exception $e) {
        return $this->apiResponse([
            'status' => 'failed',
            'data' => '',
            'mesg' => $e->getMessage()
        ], 500);
    }
}


}





