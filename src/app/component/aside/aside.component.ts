import { CommonModule, NgClass } from '@angular/common';
import { Component } from '@angular/core';
import { NavigationEnd, Router, RouterLink } from '@angular/router';
import { GlobalService } from '../../services/global.service';

@Component({
  selector: 'app-aside',
  standalone: true,
  imports: [NgClass, CommonModule],
  templateUrl: './aside.component.html',
  styleUrl: './aside.component.scss'
})
export class AsideComponent {

  currentRoute = '';
  currentMainLi = ''
  constructor(private router: Router, public service: GlobalService) {
    console.log('Current URL:', this.router.url);
    this.currentRoute = this.router.url;

    this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd) {
        this.currentRoute = this.router.url;
        console.log(this.currentRoute, "currentPage");
      }
    });

  }

  ngOnInit(): void {
    this.currentMItem(this.currentRoute);
  }

  onClick(path: string) {
    this.router.navigate([path]);
    this.currentRoute = path;
    if (window.innerWidth <= 768) {
      this.service.collapseSidebar();
    }
  }

  currentMenuItem(path: string) {
    this.currentMainLi = path;
  }

  currentMItem(path: string) {
    if (path === '/mainDashboard') {
      this.currentMainLi = 'firstItem';
    }
  }

  logout() {
    localStorage.clear();
    this.router.navigate(['']);
  }



}
