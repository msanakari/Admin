import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BuyOffcanvasComponent } from './buy-offcanvas.component';

describe('BuyOffcanvasComponent', () => {
  let component: BuyOffcanvasComponent;
  let fixture: ComponentFixture<BuyOffcanvasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BuyOffcanvasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(BuyOffcanvasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
