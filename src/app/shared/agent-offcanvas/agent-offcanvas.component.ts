import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-agent-offcanvas',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './agent-offcanvas.component.html',
  styleUrl: './agent-offcanvas.component.scss'
})
export class AgentOffcanvasComponent {

  @Input() title: string = 'Agent Details';
  @Input() agentData: any = '';
  @Output() close = new EventEmitter<boolean>();
  isOpen: boolean = false;

  ngOnInit() {
    console.log(this.agentData);

    setTimeout(() => {
      document.body.style.overflow = 'hidden';
      this.isOpen = true
    }, 100);
  }

  ngOnDestroy() {
    document.body.style.overflow = '';
  }

  closeOffcanvas() {
    this.isOpen = false;
    document.body.style.overflow = '';
    this.close.emit(true);
  }
}
