import { CommonModule } from '@angular/common';
import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { SettingOffcanvasComponent } from '../../shared/setting-offcanvas/setting-offcanvas.component';
import { HttpService } from '../../services/http.service';

@Component({
  selector: 'app-settings',
  standalone: true,
  imports: [CommonModule, SettingOffcanvasComponent],
  templateUrl: './settings.component.html',
  styleUrl: './settings.component.scss',
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class SettingsComponent {

  pageData: Array<any> = [];;
  selectedItem: any = '';
  viewSettingPopup: boolean = false;
  isPageLoading: boolean = true;

  constructor(private http: HttpService) {

  }

  ngOnInit() {
    this.getSettings();
  }


  async getSettings() {
    await this.http.getSettings().then((res: any) => {
      if (res?.status == "success") {
        this.pageData = res?.data;
      }
    })
    this.isPageLoading = false;
  }

  viewSetting(item: any) {
    this.selectedItem = item;
    setTimeout(() => {
      this.viewSettingPopup = true;
    }, 100);

  }

  closeOffcanvas(ev: any) {
    this.viewSettingPopup = false;
  }

  handleUpdate(ev: any) {
    this.isPageLoading = true;
    this.viewSettingPopup = false;
    this.getSettings();
  }
}
