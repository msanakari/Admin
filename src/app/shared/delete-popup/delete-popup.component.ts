import { Component, EventEmitter, Input, Output } from '@angular/core';
import { DELETE_DATA, EnquiryType } from '../../payload-model';
import { GlobalService } from '../../services/global.service';
import { HttpService } from '../../services/http.service';

@Component({
  selector: 'app-delete-popup',
  standalone: true,
  imports: [],
  templateUrl: './delete-popup.component.html',
  styleUrl: './delete-popup.component.scss'
})
export class DeletePopupComponent {
  @Input() type: EnquiryType = '';
  @Input() item: any = '';

  @Output() onSuccess = new EventEmitter<any>();
  @Output() onClose = new EventEmitter<void>();

  constructor(public gS: GlobalService, private http: HttpService) {

  }

  ngOnInit() {
    console.log(this.type, 'type');
    console.log(this.item, 'item');
  }

  close() {
    this.onClose.emit();
  }

  deleteConfirm() {
    if (this.type == '' || this.type == undefined || this.type == null) {
      this.gS.openToast('Error', '');
      return;
    }

    if (!this.item) {
      this.gS.openToast('Error', '');
      return;
    }

    let payload: DELETE_DATA = {
      id: this.item?.id,
      type: this.type
    }

    this.http.deleteData(payload).then((res: any) => {
      if (res?.status == 'success') {
        this.onSuccess.emit(true);
      } else {
        this.gS.openToast('Error', res?.mesg);
      }
    });

  }
}
