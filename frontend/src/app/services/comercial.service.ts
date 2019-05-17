import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class ComercialService {
  readonly URI_API = 'http://127.0.0.1:8000/api/comercial';  //local

  constructor(private http: HttpClient) { }

  getUsuarios() {
    return this.http.get(this.URI_API + "/consultores");
  }
  getAnnos() {
    return this.http.get(this.URI_API + "/annos");
  }
  getRelatorio(filtroValues:any) {
    return this.http.post(this.URI_API + "/relatorio",filtroValues);
  }
  getGrafico(filtroValues: any) {
    return this.http.post(this.URI_API + "/grafico", filtroValues);
  }
  getPizza(filtroValues: any) {
    return this.http.post(this.URI_API + "/pizza", filtroValues);
  }
}
