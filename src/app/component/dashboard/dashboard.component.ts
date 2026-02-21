import { Component, CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { GlobalService } from '../../services/global.service';
import { HttpService } from '../../services/http.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { GET_DASHBOARD_COUNT } from '../../payload-model';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule,RouterLink],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss',
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class DashboardComponent {

  filterOption: Array<any> = [
    { name: 'Today', value: 'TODAY' },
    { name: 'Yesterday', value: 'YESTERDAY' },
    { name: 'This Week', value: 'WEEK' },
    { name: 'This Month', value: 'MONTH' }
  ]

   filterOptionNew: Array<any> = [
    'Open',
    'Close'
  ]

  selectedFilter = 'Open';
  pageData: any = '';
  isPageLoading: boolean = true;

  filterPrefixMap: Record<string, string> = {
    TODAY: 'today',
    YESTERDAY: 'yesterday',
    WEEK: 'week',
    MONTH: 'months'
  };

  filterType:string='Open'

  constructor(public gS: GlobalService, private http: HttpService) {

  }

  ngOnInit() {
    this.getCount();
  }


  getCount() {
    let payload:GET_DASHBOARD_COUNT={
      filtertype:this.filterType
    }
    this.http.getDashboardCountNew(payload).then((res: any) => {
      if (res?.status == 'success') {
        this.pageData = res?.data;
        console.log(this.pageData);
      }

      this.isPageLoading = false;
    })
  }

  get stats() {
    const prefix = this.filterPrefixMap[this.selectedFilter];

    if (!prefix || !this.pageData) {
      return { sell: 0, buy: 0, sellBuy: 0, agent: 0 };
    }

    return {
      sell: this.pageData[`${prefix.toLowerCase()}_sellcount`] || 0,
      buy: this.pageData[`${prefix.toLowerCase()}_buycount`] || 0,
      sellBuy: this.pageData[`${prefix.toLowerCase()}_sellbuycount`] || 0,
      agent: this.pageData[`${prefix.toLowerCase()}_agentcount`] || 0
    };
  }

  filterChange(){
    this.getCount();
  }
}
