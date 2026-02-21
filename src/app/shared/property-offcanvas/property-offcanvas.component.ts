import { CommonModule } from "@angular/common";
import { Component, EventEmitter, Input, Output } from "@angular/core";
import {
  FormBuilder,
  FormGroup,
  FormsModule,
  ReactiveFormsModule,
  Validators,
} from "@angular/forms";
import { HttpService } from "../../services/http.service";
import { GlobalService } from "../../services/global.service";
import { ADD_UPDATE_PROPERTY } from "../../payload-model";

@Component({
  selector: "app-property-offcanvas",
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule],
  templateUrl: "./property-offcanvas.component.html",
  styleUrl: "./property-offcanvas.component.scss",
})
export class PropertyOffcanvasComponent {
  @Input() title: string = "Add Property";
  @Input() inqueryData: any = "";
  @Output() close = new EventEmitter<boolean>();
  isOpen: boolean = false;

  contactForm: FormGroup;

  selectedPropertyType = "Residential";

  selectedImages: Array<any> = [];
  selectedDocs: Array<any> = [];
  isUploading: boolean = false;
  isUploadingDocs: boolean = false;

  isEdit: boolean = false;

  constructor(
    private fb: FormBuilder,
    private http: HttpService,
    public gS: GlobalService
  ) {
    this.contactForm = this.fb.group({
      id: [0],
      PropertyType: ["Residential", Validators.required],
      PropertyTitle: ["", Validators.required],
      PropertyPrice: ["", Validators.required],
      PropertyBedrooms: [""],
      PropertyBathrooms: [""],
      PropertyArea: [""],
      PropertyYearBuilt: [""],
      PropertyLocation: [""],
      PropertyDescription: ["", Validators.required],
      PropertyStatus: [true],
      image_url: [""],
      PropertyDocument: [""]
    });
  }

  ngOnInit() {
    console.log(this.inqueryData);

    if (this.inqueryData) {
      this.selectedPropertyType = this.inqueryData?.PropertyType;
      this.contactForm.patchValue({
        id: this.inqueryData?.id,
        PropertyType:
          this.inqueryData?.PropertyType || this.selectedPropertyType,
        PropertyTitle: this.inqueryData?.PropertyTitle,
        PropertyPrice: this.inqueryData?.PropertyPrice,
        PropertyBedrooms: this.inqueryData?.PropertyBedrooms,
        PropertyBathrooms: this.inqueryData?.PropertyBathrooms,
        PropertyArea: this.inqueryData?.PropertyArea,
        PropertyYearBuilt: this.inqueryData?.PropertyYearBuilt,
        PropertyLocation: this.inqueryData?.PropertyLocation,
        PropertyDescription: this.inqueryData?.PropertyDescription,
        PropertyStatus: this.inqueryData?.PropertyStatus,
      });

      if (this.inqueryData?.images) {
        this.inqueryData?.images.forEach((item: any) => {
          if (item?.image_url) {
            this.selectedImages.push(item?.image_url);
          } else if (item?.PropertyDocuments) {
            this.selectedDocs.push(item?.PropertyDocuments);
          }
        });
      }

      this.isEdit = true;
      this.title = "Update Property";
    }

    setTimeout(() => {
      document.body.style.overflow = "hidden";
      this.isOpen = true;
    }, 100);
  }

  ngOnDestroy() {
    document.body.style.overflow = "";
  }

  closeOffcanvas() {
    this.isOpen = false;
    document.body.style.overflow = "";
    this.close.emit(true);
  }

  setPropertyType(type: string) {
    this.selectedPropertyType = type;
    this.contactForm.patchValue({
      PropertyType: type,
    });

    if (type === "Commercial") {
      this.contactForm.get("PropertyBedrooms")?.reset(null);
      this.contactForm.get("PropertyBathrooms")?.reset(null);
      this.contactForm.get("PropertyArea")?.reset(null);
      this.contactForm.get("PropertyYearBuilt")?.reset(null);
      this.contactForm.get("PropertyLocation")?.reset(null);
    }
  }

  saveDetails() {
    let payload: ADD_UPDATE_PROPERTY = {
      id: this.contactForm.value.id,
      PropertyType: this.contactForm.value.PropertyType,
      PropertyTitle: this.contactForm.value.PropertyTitle,
      PropertyPrice: this.contactForm.value.PropertyPrice,
      PropertyBedrooms: this.contactForm.value.PropertyBedrooms,
      PropertyBathrooms: this.contactForm.value.PropertyBathrooms,
      PropertyArea: this.contactForm.value.PropertyArea,
      PropertyYearBuilt: this.contactForm.value.PropertyYearBuilt,
      PropertyLocation: this.contactForm.value.PropertyLocation,
      PropertyDescription: this.contactForm.value.PropertyDescription,
      PropertyStatus: this.contactForm.value.PropertyStatus,
      image_url: this.selectedImages.join(","),
      PropertyDocument: this.selectedDocs.join(",")
    };

    console.log(payload);

    this.http.addUpdateProperty(payload).then((res: any) => {
      console.log(res);
      if (res?.status == "success") {
        this.gS.openToast(
          "Success",
          this.isEdit
            ? "Property updated successfully"
            : "Property added successfully"
        );
        this.closeOffcanvas();
      }
    });
  }

  async onFileSelected(event: any) {
    const files: FileList = event.target.files;

    for (let i = 0; i < files.length; i++) {
      this.isUploading = true;
      const file = files[i];
      const formData = new FormData();
      formData.append("image", file);

      try {
        await this.http.uploadImage(formData).then((res: any) => {
          console.log(res, "image upload");
          if (res?.status == "success") {
            let data = res?.data;
            this.selectedImages.push(data);

            this.contactForm.patchValue({
              image_url: data,
            });
          }
        });
      } catch (error) {
        console.log(error);
      } finally {
        this.isUploading = false;
      }
    }
  }

  removeImageByName(name: string) {
    this.selectedImages = this.selectedImages.filter(
      (img) => !img.includes(name)
    );
  }


  removeDocByName(name: string) {
    this.selectedDocs = this.selectedDocs.filter(
      (img) => !img.includes(name)
    );
  }

  async onFileSelectedDocuments(event: any) {
    const files: FileList = event.target.files;

    for (let i = 0; i < files.length; i++) {
      this.isUploadingDocs = true;
      const file = files[i];
      const formData = new FormData();
      formData.append("image", file);

      try {
        await this.http.uploadImage(formData).then((res: any) => {
          console.log(res, "image upload");
          if (res?.status == "success") {
            let data = res?.data;
            this.selectedDocs.push(data);

            this.contactForm.patchValue({
              PropertyDocument: data,
            });
          }
        });
      } catch (error) {
        console.log(error);
      } finally {
        this.isUploadingDocs = false;
      }
    }
  }

  viewDocument(url: string) {
    window.open(url, "_blank");
  }
}
