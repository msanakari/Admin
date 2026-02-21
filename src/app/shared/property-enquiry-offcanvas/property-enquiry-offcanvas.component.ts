import { CommonModule } from '@angular/common';
import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-property-enquiry-offcanvas',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './property-enquiry-offcanvas.component.html',
  styleUrl: './property-enquiry-offcanvas.component.scss'
})
export class PropertyEnquiryOffcanvasComponent {

  @Input() title: string = 'View Details';
  @Input() inqueryData: any = '';
  @Output() close = new EventEmitter<boolean>();
  isOpen: boolean = false;

  ngOnInit() {
    console.log(this.inqueryData);

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
