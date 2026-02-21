import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PropertyOffcanvasComponent } from './property-offcanvas.component';

describe('PropertyOffcanvasComponent', () => {
  let component: PropertyOffcanvasComponent;
  let fixture: ComponentFixture<PropertyOffcanvasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PropertyOffcanvasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PropertyOffcanvasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
