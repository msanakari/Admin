<?php
namespace App\Exports;

use App\Models\{BuyUser, SellUser, BuySellUser, Agent ,Properties,PropertyEnquiries };
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Http\Helpers\Common;

class BuySellDataExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $type;

    public function __construct($type)
    {
        $this->type = strtoupper($type); // normalize
    }

    public function array(): array
    {
        $modelMap = [
            'SELL'    => SellUser::class,
            'BUY'     => BuyUser::class,
            'BUYSELL' => BuySellUser::class,
            'AGENT'   => Agent::class,
            'PROPERTYENQUIRY' => PropertyEnquiries::class,
        ];

        if (!isset($modelMap[$this->type])) {
            return [];
        }

        $model = $modelMap[$this->type];
       
       if($this->type=='PROPERTYENQUIRY'){
           $dataList = $model::where('is_email_verified', 1)
            ->orderBy('id', 'desc')
            ->get(); 
            // Properties
            
          $dataList = PropertyEnquiries::where('is_email_verified', 1)
    ->join('properties', 'properties.id', '=', 'propertyenquiries.propertyId') // corrected join
    ->orderBy('propertyenquiries.id', 'desc')
    ->select([
        'propertyenquiries.*',
        'properties.PropertyTitle'
    ])
    ->get();
            
       }else{
           $dataList = $model::where('is_email_verified', 1)
            ->orderBy('id', 'desc')
            ->get(); 
       }
           
       
        
        $data = [];

        if ($dataList->count()) {
            foreach ($dataList as $key => $value) {

                if (in_array($this->type, ['BUY', 'SELL'])) {
                    // Common structure for Buy & Sell
                  
                    $data[$key]['First Name']     = $value->first_name;
                    $data[$key]['Last Name']    = $value->last_name;
                    $data[$key]['Email'] = $value->email;
                    $data[$key]['Phone Number']      = $value->country_code.' '.$value->phone_number;
                    $data[$key]['States']       = $value->state;
                    $data[$key]['Property Type']  = $value->property_type;
                    $data[$key]['Property Sub Type']       = $value->property_sub_type;
                    $data[$key]['Price Range']       = $value->price_range;
                    $data[$key]['Date']          =date('d-m-Y H:i:s', strtotime($value->created_at));

                } elseif ($this->type == 'BUYSELL') {
                    // Different structure for BuySell
                   
                    $data[$key]['First Name']     = $value->first_name;
                    $data[$key]['Last Name']    = $value->last_name;
                    $data[$key]['Email'] = $value->email;
                    $data[$key]['Phone Number']      = $value->country_code.' '.$value->phone_number;
                    $data[$key]['Sell State']       = $value->sellState;
                    $data[$key]['Sell Property Type']  = $value->sellPropertyType;
                    $data[$key]['Sell Property Sub Type']       = $value->sellPropertySubType;
                    $data[$key]['Sell Price Range']       = $value->sellPriceRange;
                    $data[$key]['Buy State']       = $value->buyState;
                    $data[$key]['Buy Property Type']  = $value->buyPropertyType;
                    $data[$key]['Buy Property Sub Type']       = $value->buyPropertySubType;
                    $data[$key]['Buy Price Range']       = $value->buyPriceRange;
                    $data[$key]['Date']          =date('d-m-Y H:i:s', strtotime($value->created_at));

                } elseif ($this->type == 'AGENT') {
                    // Different structure for Agent
                
                    $data[$key]['First Name']     = $value->firstName;
                    $data[$key]['Last Name']    = $value->lastName;
                    $data[$key]['Email'] = $value->email;
                    $data[$key]['Phone Number']      = $value->countryCode.' '.$value->phoneNumber;
                    $data[$key]['States']       = $value->states;
                    $data[$key]['Prefrence Type']  = $value->prefrenceType;
                    $data[$key]['Property Type']       = $value->propertyType;
                    $data[$key]['Cashback Buyer']       = $value->cashbackBuyer;
                    $data[$key]['Agent Id']  = $value->agentId;
                    $data[$key]['Agent Commission']  = $value->agentCommission;
                    $data[$key]['Experience']  = $value->experience;
                    $data[$key]['Brokerage Company']       = $value->brokerageCompany;
                    $data[$key]['Date']           =date('d-m-Y H:i:s', strtotime($value->created_at));
                }elseif ($this->type == 'PROPERTYENQUIRY') {
                    // Different structure for Agent
                $data[$key]['Property Name']     = $value->PropertyTitle;
                    $data[$key]['First Name']     = $value->firstName;
                    $data[$key]['Last Name']    = $value->lastName;
                    $data[$key]['Email'] = $value->email;
                    $data[$key]['Phone Number']      = $value->countryCode.' '.$value->phoneNumber;
                    $data[$key]['Status']       = $value->enquiryStatus;
                    $data[$key]['Date']           =date('d-m-Y H:i:s', strtotime($value->created_at));
                }
            }
        }

        return $data;
    }


    public function headings(): array
    {
        if (in_array($this->type, ['BUY', 'SELL'])) {
            return [
                    'First Name',
                    'Last Name',
                    'Email',
                    'Phone Number',
                    'States',
                    'Property Type',
                    'Property Sub Type',
                    'Price Range',
                    'Date'
            ];
        } elseif ($this->type == 'BUYSELL') {
            return [
               'First Name',
               'Last Name',
               'Email',
               'Phone Number',
               'Sell State',
               'Sell Property Type',
               'Sell Property Sub Type',
               'Sell Price Range',
               'Buy State',
               'Buy Property Type',
               'Buy Property Sub Type',
               'Buy Price Range','Date'
            ];
        } elseif ($this->type == 'AGENT') {
            return [
                'First Name',
                'Last Name',
                'Email',
                'Phone Number',
                'States',
                'Prefrence Type',
                'Property Type',
                'Cashback Buyer',
                'Agent Id','Agent Commission','Experience','Brokerage Company','Date'
            ];
        }elseif ($this->type == 'PROPERTYENQUIRY') {
            return [
                'Property Name',
                'First Name',
                'Last Name',
                'Email',
                'Phone Number',
                'Status','Date'
            ];
        }

        return [];
    }
}