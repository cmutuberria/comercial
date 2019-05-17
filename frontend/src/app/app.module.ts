import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ComercialComponent } from './components/comercial/comercial.component';

import { MzNavbarModule, MzButtonModule, MzSelectModule, MzCardModule,
  MzValidationModule, MzCollapsibleModule, MzSpinnerModule     } from 'ngx-materialize'
import { ReactiveFormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ChartsModule } from 'ng2-charts';

@NgModule({
  declarations: [
    AppComponent,
    ComercialComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    AppRoutingModule,
    FormsModule,
    MzNavbarModule,
    MzButtonModule, 
    MzSelectModule,
    MzCardModule,
    MzValidationModule,
    MzCollapsibleModule,
    MzSpinnerModule, 
    ReactiveFormsModule,
    BrowserAnimationsModule,
    ChartsModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
