import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class PreloadService {

  loading: boolean=true;
  haveError: boolean=false;
  error: any;

  constructor() { }

  fireLoading(){
    this.loading=true;
  }

  stopLoading(){
    this.loading=false;
  }
  
  haveErrors(){
    this.haveError=true;
  }
  clearErrors(){
    this.haveError=false;
  }


}
