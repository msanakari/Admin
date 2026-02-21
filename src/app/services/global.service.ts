import { Injectable } from '@angular/core';
import * as bootstrap from 'bootstrap';

export type ToastType = 'Info' | 'Success' | 'Error' | 'Warning';

@Injectable({
  providedIn: 'root'
})
export class GlobalService {

  isCollapse = false;
  errorType: ToastType = 'Error';
  errorMessage: String = 'Something went wrong, Please try again later.';

  userName: string = '';

  imagePath:string='https://realbucs.com/apiadmin/public/upload/';

  constructor() {
    this.userName = localStorage.getItem('userName') ?? '';
  }

  ngOnInit(){
  }

  collapseSidebar() {
    this.isCollapse = !this.isCollapse;
  }

  isLoggedIn(): boolean {
    return !!localStorage.getItem('adminId');
  }


  openToast(type: ToastType, message: string) {
    this.errorType = type;
    if (message) {
      this.errorMessage = message;
    }
    setTimeout(() => {
      const toastEl = document.getElementById('myToast');
      if (toastEl) {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
      }
    }, 100);
  }
}
