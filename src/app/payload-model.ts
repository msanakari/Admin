export type EnquiryType = 'SELL' | 'BUYSELL' | 'BUY' | 'AGENT' | 'PROPERTY' | 'PROPERTYENQUIRY' | '';
export type EnquiryStatus = 'Open' | 'Close';

export interface GET_ENUIRY_DATA {
    pageNo: number
    pageSize: number
    type: EnquiryType
    filtertype:string
}

export interface GET_PROPERTY_LISTING {
    pageNo: number
    pageSize: number,
    type: EnquiryType
    filtertype?:string
}

export interface LOGIN {
    emailid: string
    password: string
}


export interface ADD_UPDATE_SETTING {
    id: number
    value: any
}

export interface DELETE_DATA {
    id: number
    type: string
}

export interface DOWNLOAD_EXCEL {
    type: EnquiryType
}

export interface UPDATE_QUERY_STATUS {
    formId: number
    type: EnquiryType
    status: EnquiryStatus
}

export interface ADD_UPDATE_PROPERTY {
    id?: number
    PropertyType?:string
    PropertyTitle: string
    PropertyPrice: any
    PropertyBedrooms: any
    PropertyBathrooms: any
    PropertyArea: any
    PropertyYearBuilt: any
    PropertyLocation: any
    PropertyDescription: any
    PropertyStatus: any
    image_url: any
    PropertyDocument?:any
}

export interface GET_DASHBOARD_COUNT {
    filtertype:string
}