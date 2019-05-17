import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ComercialComponent } from './components/comercial/comercial.component';

const routes: Routes = [
  { path: '', component: ComercialComponent},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
