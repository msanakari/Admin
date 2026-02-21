import { Component } from '@angular/core';
import { GlobalService } from '../../services/global.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-header',
  standalone: true,
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss'] 
})


export class HeaderComponent {

  constructor(public service:GlobalService,private router:Router){
  }


  logout(){
    localStorage.clear();
    this.router.navigate(['']);
  }
}
