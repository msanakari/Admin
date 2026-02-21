import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PropertyEnquiryOffcanvasComponent } from './property-enquiry-offcanvas.component';

describe('PropertyEnquiryOffcanvasComponent', () => {
  let component: PropertyEnquiryOffcanvasComponent;
  let fixture: ComponentFixture<PropertyEnquiryOffcanvasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PropertyEnquiryOffcanvasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PropertyEnquiryOffcanvasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
