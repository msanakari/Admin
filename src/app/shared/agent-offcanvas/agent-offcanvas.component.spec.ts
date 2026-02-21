import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AgentOffcanvasComponent } from './agent-offcanvas.component';

describe('AgentOffcanvasComponent', () => {
  let component: AgentOffcanvasComponent;
  let fixture: ComponentFixture<AgentOffcanvasComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AgentOffcanvasComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(AgentOffcanvasComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
