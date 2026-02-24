<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Models\{ContactUsEnquiry,Properties,PropertyEnquiries,PropertyImages,Settings,SellUser,BuySellUser,BuyUser,Agent};
use Validator;
use DB;
use Carbon\Carbon;
use Notification;

class PropertyApiController extends Controller
{
 protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }
    
//      public function getPropertyenquirylist(Request $request){
//          try {
//              $rules = array(
//             'pageNo'=>'required',
//             'pageSize'=>'required',
//         );

//         $fieldNames = array(
//             'pageNo'=>'pageNo',
//             'pageSize'=>'pageSize',
//         );
//         $validator = Validator::make($request->all(), $rules);
//         $validator->setAttributeNames($fieldNames);

//         if ($validator->fails()) {
//          return $this->apiResponse([
//             'status' => 'failed',
//             'mesg'   => $validator->errors()->first()
//         ], 200);
//         } else {
          

//     $data = PropertyEnquiries::where('is_email_verified', 1)->orderBy('id', 'desc')
//         ->paginate(
//             max((int) $request->pageSize, 10),
//             ['*'],
//             'page',
//             max((int) $request->pageNo, 1)
//         );


// return $this->apiResponse(['status' => 'success', 'data' => $data,'mesg'=>'data fetched  successfully'], 200);

//     }
//         }catch (Exception $qx) {
//           return $this->apiResponse([
//         'status' => 'failed',
//         'mesg'   => $e->getMessage()
//     ], 500);
//         }
//     }
    

 public function getPropertylist(Request $request){
         try {
             $rules = array(
            'pageNo'=>'required',
            'pageSize'=>'required',
            'type'=>'required',
        );

        $fieldNames = array(
            'pageNo'=>'pageNo',
            'pageSize'=>'pageSize',
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
            
              if (!in_array($request->type, ['PROPERTY', 'PROPERTYENQUIRY'], true)) {
    return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => 'Please enter correct type'
    ], 200);
} 
       // Map request type to model class
// Allowed types
$modelMap = [
    'PROPERTY'    => Properties::class,
    'PROPERTYENQUIRY' => PropertyEnquiries::class,
];


    $model = $modelMap[$request->type];
    
    if($request->type=='PROPERTYENQUIRY'){
        
        
    $data = $model::where('is_email_verified', 1);

if (isset($request->filtertype) && $request->filtertype != '') {
    $data = $data->where('enquiryStatus', $request->filtertype);
} else {
    // Optional: you can skip this else if you don't want a default filter
    $data = $data->where('enquiryStatus', $request->filtertype);
}

$data = $data->orderBy('id', 'desc')
    ->paginate(
        max((int) $request->pageSize, 10),
        ['*'],
        'page',
        max((int) $request->pageNo, 1)
    );


    }else{
         $data = Properties::with('images')->orderBy('id', 'desc')
        ->paginate(
            max((int) $request->pageSize, 10),
            ['*'],
            'page',
            max((int) $request->pageNo, 1)
        );
    }

 

return $this->apiResponse(['status' => 'success', 'data' => $data,'mesg'=>'data fetched  successfully'], 200);

    }
        }catch (Exception $qx) {
          return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => $e->getMessage()
    ], 500);
        }
    }
    
    
    


 public function getactivePropertylist(Request $request){
         try {
             $rules = array(
            'pageNo'=>'required',
            'pageSize'=>'required',
        );

        $fieldNames = array(
            'pageNo'=>'pageNo',
            'pageSize'=>'pageSize',
        );
        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
         return $this->apiResponse([
            'status' => 'failed',
            'mesg'   => $validator->errors()->first()
        ], 200);
        } else {
          

    $data = Properties::with('images')->where('PropertyStatus','Active')->orderBy('id', 'desc')
        ->paginate(
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


 public function getactivePropertybyid(Request $request){
         try {
             $rules = array(
            'id'=>'required',
        );

        $fieldNames = array(
            'id'=>'id',
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

$userdata = Properties::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
    $data = Properties::with('images')->where('id',$request->id)->first();

}
return $this->apiResponse(['status' => 'success', 'data' => $data,'mesg'=>'data fetched  successfully'], 200);

    }
        }catch (Exception $qx) {
          return $this->apiResponse([
        'status' => 'failed',
        'mesg'   => $e->getMessage()
    ], 500);
        }
    }




    

        
         public function addupdateproperty(Request $request){
         try {
             $rules = array(
            'PropertyTitle'=>'required',
            'PropertyPrice'=>'required',
            // 'PropertyBedrooms'=>'required',
            // 'PropertyBathrooms'=>'required',
            // 'PropertyArea'=>'required',
            // 'PropertyYearBuilt'=>'required',
            // 'PropertyLocation'=>'required',
            'PropertyDescription'=>'required',
            'PropertyStatus'=>'required',
            // 'image_url'=>'required',
        );

        $fieldNames = array(
            'PropertyTitle'=>'PropertyTitle',
            'PropertyPrice'=>'PropertyPrice',
            // 'PropertyBedrooms'=>'PropertyBedrooms',
            // 'PropertyBathrooms'=>'PropertyBathrooms',
            // 'PropertyArea'=>'PropertyArea',
            // 'PropertyYearBuilt'=>'PropertyYearBuilt',
            // 'PropertyLocation'=>'PropertyLocation',
            'PropertyDescription'=>'PropertyDescription',
            'PropertyStatus'=>'PropertyStatus',
            // 'image_url'=>'image_url',
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
             $mesg='data updated successfully';

$userdata = Properties::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->PropertyType   = $request->PropertyType ?? null;
$userdata->PropertyTitle   = $request->PropertyTitle ?? null;
$userdata->PropertyPrice   =$request->PropertyPrice ?? '0';
$userdata->PropertyBedrooms   = $request->PropertyBedrooms ?? '0';
$userdata->PropertyBathrooms   =$request->PropertyBathrooms ?? '0';
$userdata->PropertyArea   =$request->PropertyArea ?? null;
$userdata->PropertyYearBuilt=$request->PropertyYearBuilt ?? null;
$userdata->PropertyLocation=$request->PropertyLocation ?? null;
$userdata->PropertyDescription   = $request->PropertyDescription ?? null;
$userdata->PropertyDocument   = $request->PropertyDocument ?? null;
$userdata->PropertyStatus   =$request->PropertyStatus;
$userdata->update();
$userid=$request->id;
// $userdata->image_url =$request->image_url;


//  PropertyImages::where('property_id', $request->id)->delete();

// Always run this block, even if image_url is empty
$images = explode(',', $request->image_url ?? '');
$images = array_filter(array_map('trim', $images), function ($img) {
    return $img !== '';
});
$images = array_unique($images);

// ðŸ§¹ Step 1: Delete images that are NOT in the current list (including all if list is empty)
PropertyImages::where('property_id', $userid)
    ->whereNotIn('image_url', $images)
    ->delete();

// ðŸ”„ Step 2: Ensure all provided images exist
foreach ($images as $image_url) {
    $existing = PropertyImages::where('property_id', $userid)
        ->where('image_url', $image_url)
        ->first();

    if (!$existing) {
        $newImage = new PropertyImages();
        $newImage->property_id = $userid;
        $newImage->image_url   = $image_url;
        $newImage->save();
    }
}


    
// Always run this block, even if PropertyDocument is empty
$documents = explode(',', $request->PropertyDocument ?? '');

// Clean and normalize the document URLs
$documents = array_unique(array_filter(array_map('trim', $documents), function ($doc) {
    return $doc !== '';
}));

// ðŸ§¹ Step 1: Delete old documents that are NOT in the new list
PropertyImages::where('property_id', $userid)
    ->whereNotIn('PropertyDocuments', $documents)
    ->delete();

// ðŸ”„ Step 2: Add any new documents not already in DB
foreach ($documents as $docUrl) {
    $exists = PropertyImages::where('property_id', $userid)
        ->where('PropertyDocuments', $docUrl)
        ->exists();

    if (!$exists) {
        $newDoc = new PropertyImages(); // Replace with PropertyDocuments if using a separate model
        $newDoc->property_id = $userid;
        $newDoc->PropertyDocuments = $docUrl;
        $newDoc->save();
    }
}




    }else{
        $mesg='data added successfully';
  
         $userdata = new Properties;
$userdata->PropertyType   = $request->PropertyType ?? null;
$userdata->PropertyTitle   = $request->PropertyTitle ?? null;
$userdata->PropertyPrice   =$request->PropertyPrice ?? '0';
$userdata->PropertyBedrooms   = $request->PropertyBedrooms ?? '0';
$userdata->PropertyBathrooms   =$request->PropertyBathrooms ?? '0';
$userdata->PropertyArea   =$request->PropertyArea ?? null;
$userdata->PropertyYearBuilt=$request->PropertyYearBuilt ?? null;
$userdata->PropertyLocation=$request->PropertyLocation ?? null;
$userdata->PropertyDescription   = $request->PropertyDescription ?? null;
$userdata->PropertyDocument   = $request->PropertyDocument ?? null;
$userdata->save();  
$userid=$userdata->id;


if (!empty($request->image_url)) {
    $images = explode(',', $request->image_url);

    // Clean and filter the image URLs
    $images = array_unique(array_filter(array_map('trim', $images), function ($img) {
        return $img !== '';
    }));

    // Loop and insert each image
    foreach ($images as $imageUrl) {
        $newImage = new PropertyImages(); // Or your correct image model
        $newImage->property_id = $userid; // Make sure $userid is the new property's ID
        $newImage->image_url = $imageUrl;
        $newImage->save();
    }
}


 if (!empty($request->PropertyDocument)) {
    $documents = explode(',', $request->PropertyDocument);
    
    // Clean and filter the documents
    $documents = array_unique(array_filter(array_map('trim', $documents), function ($doc) {
        return $doc !== '';
    }));

    // Loop and insert each document
    foreach ($documents as $docUrl) {
        $newDoc = new PropertyImages(); // Change to PropertyDocuments if using a separate model
        $newDoc->property_id = $userid; // Make sure $userid is set to the new property ID
        $newDoc->PropertyDocuments = $docUrl;
        $newDoc->save();
    }
}




    }

    
if($userid >0){
 return $this->apiResponse(['status' => 'success', 'data' =>$userid ,'mesg'=>$mesg], 200);
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

     public function propertyenquiry(Request $request){
         try {
             $rules = array(
            'firstName'=>'required',
            'lastName'=>'required',
            'email'=>'required',
            'phoneNumber'=>'required',
            'countryCode'=>'required',
            'propertyId'=>'required',

        );

        $fieldNames = array(
            'firstName'=>'firstName',
            'lastName'=>'lastName',
            'email'=>'email',
            'phoneNumber'=>'phoneNumber',
            'countryCode'=>'countryCode',
            'propertyId'=>'propertyId',
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

$userdata = PropertyEnquiries::find($request->id);
// dd($userdata->id );
if($userdata===null){
      return $this->apiResponse(['status' => 'failed','mesg'=>'Invalid form id '], 200);
}
$userdata->firstName   = $request->firstName;
$userdata->lastName   =$request->lastName;
$userdata->email   = $request->email;
$userdata->phoneNumber   =$request->phoneNumber;
$userdata->countryCode   =$request->countryCode;
$userdata->propertyId   =$request->propertyId;
$userdata->email_otp  =$otp;
$userdata->update();
$userid=$request->id;
    }else{
         $userdata = new PropertyEnquiries;
$userdata->firstName   = $request->firstName;
$userdata->lastName   =$request->lastName;
$userdata->email   = $request->email;
$userdata->phoneNumber   =$request->phoneNumber;
$userdata->countryCode   =$request->countryCode;
$userdata->propertyId   =$request->propertyId;
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
 return $this->apiResponse(['status' => 'success', 'data' =>$data ,'mesg'=>'Property Enquiry submitted successfully'], 200);
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
    
      

      
   

}



