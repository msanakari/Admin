import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { GlobalService } from './services/global.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrl: './app.component.scss'
})
export class AppComponent {

  constructor(public gS:GlobalService){

  }
  
  ngOnInit(){
    if (window.innerWidth <= 768) {
      this.gS.isCollapse=true;
    }
  }
}
