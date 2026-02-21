import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BuySellOffcanvasComponent } from './buy-sell-offcanvas.component';

describe('BuySellOffcanvasComponent', () => {
  let component: BuySellOffcanvasComponent;
  let fixture: ComponentFixture<BuySellOffcanvasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BuySellOffcanvasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(BuySellOffcanvasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
