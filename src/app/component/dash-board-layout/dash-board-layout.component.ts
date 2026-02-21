import { Component, HostListener } from '@angular/core';
import { HeaderComponent } from '../header/header.component';
import { AsideComponent } from '../aside/aside.component';
import { DashboardComponent } from '../dashboard/dashboard.component';
import { FooterComponent } from '../footer/footer.component';
import { RouterOutlet } from '@angular/router';
import { CommonModule } from '@angular/common';
import { GlobalService } from '../../services/global.service';

@Component({
  selector: 'app-dash-board-layout',
  standalone: true,
  imports: [CommonModule,RouterOutlet, HeaderComponent, AsideComponent, DashboardComponent, FooterComponent],
  templateUrl: './dash-board-layout.component.html',
  styleUrls: ['./dash-board-layout.component.scss']
})
export class DashBoardLayoutComponent {

  layoutClass = 'grid-layout'; // default

  constructor(public service: GlobalService) {
  }

  

  ngOnInit() {
    this.updateLayoutClass();
  }

  @HostListener('window:resize')
  onResize() {
    this.updateLayoutClass();
  }

  updateLayoutClass() {
    const isMobile = window.innerWidth < 769;
    this.layoutClass = isMobile ? 'flex-layout' : 'grid-layout';
  }

}
