import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SettingOffcanvasComponent } from './setting-offcanvas.component';

describe('SettingOffcanvasComponent', () => {
  let component: SettingOffcanvasComponent;
  let fixture: ComponentFixture<SettingOffcanvasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SettingOffcanvasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(SettingOffcanvasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
