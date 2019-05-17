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
    console.log("fire loading");
  }

  stopLoading(){
    this.loading=false;
    console.log("Stop loading");

  }
  
  haveErrors(){
    this.haveError=true;
  }
  clearErrors(){
    this.haveError=false;
  }


}
