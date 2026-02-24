<?php

// use App\Models\Currency;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Session;
use App\Http\Helpers\Common;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use App\Models\LastViewdAddress;
use App\Models\Cartproduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Twilio\Http\CurlClient;



/**
 * [dateFormat description for database date]
 * @param  [type] $value    [any number]
 * @return [type] [formates date according to preferences setting in Admin Panel]
 */
if(!function_exists('setDateForDb')) {
    function setDateForDb($value = null)
    {
        if (empty($value)) {
            return null;
        }
        $separator   = Settings::getAll()->firstWhere('name', 'date_separator')->value;
        $date_format = Settings::getAll()->firstWhere('name', 'date_format_type')->value;;
        if (str_replace($separator, '', $date_format) == "mmddyyyy") {
            $value = str_replace($separator, '/', $value);
            $date  = date('Y-m-d', strtotime($value));
        } else {
            $date = date('Y-m-d', strtotime(strtr($value, $separator, '-')));
        }
        return $date;
    }
}

function dsAsset($path, $source = null)
{
     return asset('public/'.$path, $source);
    // return asset('public/'.$path . '?v=1.0.1', $source);
}
/**
 * [Default timezones]
 * @return [timezonesArray]
 */
function phpDefaultTimeZones()
{
    $zonesArray  = array();
    $timestamp   = time();
    foreach (timezone_identifiers_list() as $key => $zone) {
        date_default_timezone_set($zone);
        $zonesArray[$key]['zone']          = $zone;
        $zonesArray[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
    }
    return $zonesArray;
}





    function make_slug($string){
        return preg_replace('/\s+/u','-',trim($string));
        }
        
        function make_slugwithoutspace($string){
        return preg_replace('/[^\w\d]+/','',trim($string));
        }

        function getParamRecord($url, $data = array()) {

          // dd($data);
          ini_set('max_execution_time', 20); 
        
          $params = '';
          foreach($data as $key=>$value)
            $params .= $key.'='.$value.'&';
          $params = trim($params, '&');
        
          $requestURL = $url.'?'.$params;
         
          $ch = curl_init();   
          curl_setopt($ch,CURLOPT_URL, $requestURL);
        
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
          curl_setopt($ch, CURLOPT_TIMEOUT, 20);
          curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);   
          $redirect="";
          
        
         $result=curl_exec($ch);  
          //  dd($result);
         curl_close($ch);
         $result = mb_convert_encoding($result,'UTF-8','UTF-8');
         $obj = json_decode($result, true);
        // dd($obj);
         if(isset($obj['status']) && $obj['status']=="401")
         {
          redirect($redirect);
          exit();
        }
        
        return $obj;
        }

        function getkomojusession( $data = array()) {
          // dd( $data);
          ini_set('max_execution_time', 300);
          $curl = curl_init();
        $token=base64_encode(config('services.komoju.secretkey'));
        // dd(env('KOMOJU_KEY_JYP'));
        curl_setopt_array($curl, [
          CURLOPT_URL => "https://komoju.com/api/v1/sessions",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode([
            'default_locale' => 'en',
            'payment_data' => [
                'capture' => 'auto'
            ],
            'amount' => $data['amount'],
            'currency' => config('services.komoju.currency'),
            'return_url'=>$data['return_url'],
            'session_url'=>'',
          ]),
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Basic ".$token,
            "content-type: application/json"
          ],
        ]);
        
        $response = curl_exec($curl);
        // dd( $response);
        $err = curl_error($curl);
        
        curl_close($curl);
   
   if ($err) {
    $obj = json_decode($response, true);
   } else {
    $obj = json_decode($response, true);

   }
        
        return $obj;
        }

     

      
        

          function data_output_datatable($columns, $data) {
            $out = array();
            for ($i = 0, $ien = count($data); $i < $ien; $i++) {
                $row = array();
                for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                    $column = $columns[$j];
                    // Is there a formatter?
                    if (isset($column['formatter'])) {
                        $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                    } else {
                        $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                    }
                }
                $out[] = $row;
            }
            return $out;
        }
        
        
        function postParamRecord($url, $data) {
        // dd($url);
            $params = '';
            foreach($data as $key=>$value)
                $params .= $key.'='.$value.'&';
        
            $params = trim($params, '&');
            // dd($params);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); //Remote Location URL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser        
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, count($data)); //number of parameters sent
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params); //parameters data        
            $result = curl_exec($ch);
            curl_close($ch);
            $result = mb_convert_encoding($result, 'UTF-8', 'UTF-8');
            // print_r($result);die();
            // return $result;
              // dd(json_decode($result, true));
            return json_decode($result, true);
        }


 


        
        
            function sentmailsmtp($recipent,$subject,$message,$attach=''){
      $fromAddress = env('APP_ENV') === 'production'
    ? env('MAIL_FROM_ADDRESS_PROD')
    : env('MAIL_FROM_ADDRESS_LOCAL');

$fromName = env('APP_ENV') === 'production'
    ? env('MAIL_FROM_NAME_PROD')
    : env('MAIL_FROM_NAME_LOCAL');
    
              $phpMailer = new PHPMailer(true);
             //$phpMailer->SMTPDebug = SMTP::DEBUG_SERVER;
              $phpMailer->isSMTP();
              $phpMailer->Host = "realbucs.com";
              $phpMailer->SMTPAuth = true;
              $phpMailer->Username = $fromAddress;
              $phpMailer->Password = env('MAIL_PASSWORD'); //for realbusc

              $phpMailer->SMTPSecure = "ssl"; 
              $phpMailer->Port = 465;
              $phpMailer->isHTML(true);
              $phpMailer->CharSet = "UTF-8";
$phpMailer->setFrom($fromAddress, $fromName);
              $phpMailer->addAddress($recipent);
              $phpMailer->Subject = $subject;
              $phpMailer->Body = $message;
              $sendresponse=$phpMailer->send();
              if($sendresponse=='true'){
               return true;
                          } else {
                             return true;
                          }
            }
        
        
        
        // function slug(){
          function slug($str, $separator = '-', $lowercase = True)
            {
                if ($separator === 'dash')
                {
                    $separator = '-';
                }
                elseif ($separator === 'underscore')
                {
                    $separator = '_';
                }
        
                $q_separator = preg_quote($separator, '#');
        
                $trans = array(
                    '&.+?;'         => '',
                    '[^\w\d _-]'        => '',
                    '\s+'           => $separator,
                    '('.$q_separator.')+'   => $separator
                );
        
                $str = strip_tags($str);
                foreach ($trans as $key => $val)
                {
                    $str = preg_replace('#'.$key.'#i'.('a' ? 'u' : ''), $val, $str);
                }
        
                if ($lowercase === TRUE)
                {
                    $str = strtolower($str);
                }
        
                return trim(trim($str, $separator));
            }
        
        function day_of_week(){
            return date('w');
        }
        
        
        function arrWeek(){
           $arrWeek = ['0' => 'Sun', '1' => 'Mon', '2' => 'Tue', '3' => 'Wed', '4' => 'Thu', '5' => 'Fri', '6' => 'Sat'];
           return $arrWeek;
        }
         function formatTime($time)
            {
                if (!empty($time)) {
                    return date('h:i A', strtotime($time));
                }
            }
        
          function one_time_message($class, $message)
            {
        
                if ($class == 'error') $class = 'danger';
                Session::flash('alert-class', 'alert-'.$class);
                Session::flash('message', $message);
            }

       
            function strreplace($str)
{
$contentbodypart = preg_replace("/[\r\n]/","", trim($str));
$contentbodypart = str_replace("\\'","'",$contentbodypart);
// $contentbodypart = str_replace("'","&#39;",$contentbodypart);
$contentbodypart = stripslashes($contentbodypart);
$contentbodypart = str_replace("<p>&nbsp;</p>","",$contentbodypart);
$contentbodypart=str_replace("(","&#40;",$contentbodypart);
$contentbodypart=str_replace(")","&#41;",$contentbodypart);
return $contentbodypart;
}

 function base64url_encode($str) {
            return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
        }