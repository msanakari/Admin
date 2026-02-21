import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { HttpService } from '../../services/http.service';
import { DOWNLOAD_EXCEL, GET_ENUIRY_DATA, UPDATE_QUERY_STATUS } from '../../payload-model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatSelectModule } from '@angular/material/select';
import { MatFormFieldModule } from '@angular/material/form-field';
import { BuyOffcanvasComponent } from '../../shared/buy-offcanvas/buy-offcanvas.component';
import { GlobalService } from '../../services/global.service';
import { DeletePopupComponent } from '../../shared/delete-popup/delete-popup.component';

@Component({
  selector: 'app-sell',
  standalone: true,
  imports: [CommonModule, FormsModule, MatPaginatorModule, MatSelectModule, MatFormFieldModule, BuyOffcanvasComponent, DeletePopupComponent],
  templateUrl: './sell.component.html',
  styleUrl: './sell.component.scss',
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class SellComponent {

  pageSizes: number[] = [5, 10, 20, 50, 100, 200];
  pageData: Array<any> = [];
  totalPages: number = 0;
  isPageLoading: boolean = true;

  viewInquiry: boolean = false;
  selectedItem: any = '';

  isDeletPopup: boolean = false;
  deleteData: any = '';

   filterOptionNew: Array<any> = [
    'Open',
    'Close'
  ]

   filterType:string='Open'

  constructor(private http: HttpService, public gS: GlobalService) {
    this.getData(0, 10);
  }

  getData(pageNo: number = 0, pageSize: number = 10) {
    let payload: GET_ENUIRY_DATA = {
      pageNo: pageNo,
      pageSize: pageSize,
      type: "SELL",
      filtertype:this.filterType
    }

    this.http.getEnquiryData(payload).then((res: any) => {
      if (res?.status == 'success' && res?.data) {
        let data = res.data;
        this.totalPages = data?.total || 0;
        this.pageData = data?.data || [];
        console.log('Response from getEnquiryData:', this.pageData);
      }

      this.isPageLoading = false;
    })
  }

  filterChange(){
    this.getData();
  }

  onPageChange(event: any) {
    console.log('Page changed:', event);

    if (event.pageIndex !== undefined && event.pageSize !== undefined) {
      this.getData(event.pageIndex, event.pageSize);
    }
  }

  openInquery(item: any) {
    this.selectedItem = item;
    setTimeout(() => {
      this.viewInquiry = true;
    }, 200);

  }

  closeOffcanvas(ev: any) {
    this.viewInquiry = false;
  }

  deleteEntry(item: any) {
    this.deleteData = item;
    setTimeout(() => {
      this.isDeletPopup = true;
    }, 100);
  }

  closeModal(ev: any) {
    this.isDeletPopup = false;
    this.deleteData = '';
  }

  deleteSuccessClose(ev: any) {
    this.gS.openToast('Success', 'Data deleted successfully!');
    this.getData();
    this.isDeletPopup = false;
    this.deleteData = '';
  }

  downloadExcel() {
    let payload: DOWNLOAD_EXCEL = {
      type: 'SELL'
    }

    this.http.downloadExcel(payload).then((res: any) => {

      if (res?.success) {
        console.log(res);
        if (res?.file) {
          let anchorTag = document.createElement('a') as HTMLAnchorElement;
          anchorTag.href = res?.file;
          anchorTag.target = '_blank';
          anchorTag.click();
        }
      }
    })
  }

  onToggleChange(event: Event, item: any) {
    const input = event.target as HTMLInputElement;
    const isChecked = input.checked;

    let payload: UPDATE_QUERY_STATUS = {
      formId: item?.id,
      type: 'SELL',
      status: isChecked ? 'Open' : 'Close'
    }

    this.http.updateQueryStatus(payload).then((res: any) => {
      console.log(res);
      if (res?.status=="success") {
        this.gS.openToast('Success', res?.mesg);
        this.getData();
      } else {
        this.gS.openToast('Error', res?.mesg);
        input.checked=false;
      }

    })
  }
}
