import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
import { ADD_UPDATE_SETTING } from '../../payload-model';
import { HttpService } from '../../services/http.service';
import { GlobalService } from '../../services/global.service';

@Component({
  selector: 'app-setting-offcanvas',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: './setting-offcanvas.component.html',
  styleUrl: './setting-offcanvas.component.scss'
})
export class SettingOffcanvasComponent {

  @Input() title: string = 'Setting Details';
  @Input() settingData: any = '';
  @Output() close = new EventEmitter<boolean>();
  @Output() handleUpdate = new EventEmitter<boolean>();
  isOpen: boolean = false;

  settingForm: FormGroup;

  constructor(private fb: FormBuilder, private http: HttpService, private gS: GlobalService) {
    this.settingForm = this.fb.group({
      id: [''],
      value: ['', Validators.required],
    })
  }

  ngOnInit() {
    if (this.settingData) {
      this.settingForm.patchValue({
        id: this.settingData.id,
        value: this.settingData.value
      })
    }

    setTimeout(() => {
      document.body.style.overflow = 'hidden';
      this.isOpen = true
    }, 100);
  }

  ngOnDestroy(){
    document.body.style.overflow = '';
  }

  closeOffcanvas() {
    this.isOpen = false;
    document.body.style.overflow = '';
    this.close.emit(true);
  }

  updateSettingValue() {
    if (this.settingForm.invalid)
      return

    let payload: ADD_UPDATE_SETTING = {
      id: this.settingForm.value.id,
      value: this.settingForm.value.value
    }

    this.http.addUpdateSettings(payload).then((res: any) => {
      if (res?.status == 'success') {
        this.gS.openToast('Success', res?.mesg);
        this.handleUpdate.emit(true);
      } else {
        this.gS.openToast('Error', res?.mesg);
      }
    })
  }
}
