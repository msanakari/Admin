import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { environment } from '../../environments/environment';
import { ADD_UPDATE_PROPERTY, ADD_UPDATE_SETTING, DELETE_DATA, DOWNLOAD_EXCEL, GET_DASHBOARD_COUNT, GET_ENUIRY_DATA, GET_PROPERTY_LISTING, LOGIN, UPDATE_QUERY_STATUS } from '../payload-model';

@Injectable({
  providedIn: 'root'
})
export class HttpService {

  apiUrl = environment.apiEndPoint;
  constructor(private http: HttpClient) { }

  getDashboardCount() {
    return new Promise((resolve, reject) => {
      this.http.get(this.apiUrl + 'get_dashboard_count').subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  getDashboardCountNew(data: GET_DASHBOARD_COUNT) {
    return new Promise((resolve, reject) => {
      let apiParam = {
        filtertype: data.filtertype
      };
      this.http.get(this.apiUrl + 'getfilterCount',{params:apiParam}).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  getEnquiryData(data: GET_ENUIRY_DATA) {
    return new Promise((resolve, reject) => {
      let apiParam = {
        pageNo: data.pageNo,
        pageSize: data.pageSize,
        type: data.type,
        filtertype: data.filtertype,
      }
      this.http.get(this.apiUrl + 'getbuyuserlist', { params: apiParam }).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  getPropertyListing(data: GET_PROPERTY_LISTING) {
    return new Promise((resolve, reject) => {
      let apiParam = {
        pageNo: data.pageNo,
        pageSize: data.pageSize,
        type: data.type,
        ...(data.filtertype ? { filtertype: data.filtertype } : {})
      }
      
      this.http.get(this.apiUrl + 'getPropertylist', { params: apiParam }).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  getSettings() {
    return new Promise((resolve, reject) => {
      this.http.get(this.apiUrl + 'get_settings').subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  addUpdateSettings(data: ADD_UPDATE_SETTING) {
    return new Promise((resolve, reject) => {
      let apiParam = {
        id: data.id,
        value: data.value
      }
      this.http.post(this.apiUrl + 'addupdate_settings', data).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  adminLogin(data: LOGIN) {
    return new Promise((resolve, reject) => {
      let body = new HttpParams();
      body = body.set('emailid', data.emailid);
      body = body.set('password', data.password);

      this.http.post(this.apiUrl + 'admin_login', data).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  deleteData(data: DELETE_DATA) {
    return new Promise((resolve, reject) => {
      let body = new HttpParams();
      body = body.set('id', data.id);
      body = body.set('type', data.type);
      let apiParam = {
        id: data.id,
        type: data.type
      };

      this.http.delete(this.apiUrl + 'delete_userbyid', { params: apiParam }).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          resolve(err)
        }
      })
    })
  }

  downloadExcel(data: DOWNLOAD_EXCEL) {
    return new Promise((resolve, reject) => {
      let body = new HttpParams();
      body = body.set('type', data.type);

      this.http.post(this.apiUrl + 'exportBuySell', data).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  updateQueryStatus(data: UPDATE_QUERY_STATUS) {
    return new Promise((resolve, reject) => {
      this.http.post(this.apiUrl + 'updateStatus', data).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  uploadImage(data: any) {
    return new Promise((resolve, reject) => {
      this.http.post(this.apiUrl + 'uploadimage', data).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

  addUpdateProperty(data: ADD_UPDATE_PROPERTY) {
    return new Promise((resolve, reject) => {
      this.http.post(this.apiUrl + 'addupdateproperty', data).subscribe({
        next: (res: any) => {
          resolve(res);
        }, error(err) {
          reject(err)
        }
      })
    })
  }

}
