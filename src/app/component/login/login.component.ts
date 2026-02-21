import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { LOGIN } from '../../payload-model';
import { HttpService } from '../../services/http.service';
import { GlobalService } from '../../services/global.service';

declare const Toast: any;
@Component({
  selector: 'app-login',
  standalone: true,
  imports: [ReactiveFormsModule, CommonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.scss'
})
export class LoginComponent {

  loginForm: FormGroup;
  submitted = false;

  constructor(private fb: FormBuilder, private router: Router, private http: HttpService, public gS: GlobalService) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  onSubmit() {
    this.submitted = true;

    if (this.loginForm.invalid) {
      return;
    }

    let payload: LOGIN = {
      emailid: this.loginForm.value.email,
      password: this.loginForm.value.password
    }

    this.http.adminLogin(payload).then((res: any) => {
      if (res?.status == 'success') {
        this.gS.userName=res?.data?.username;
        this.gS.openToast('Success', 'Login Successfully !');
        localStorage.setItem('adminId', res?.data?.id);
        localStorage.setItem('userName', res?.data?.username);
        this.router.navigate(['/dashboard']);
      } else {
        this.gS.openToast('Error', res?.mesg);
      }

    })
  }


}
