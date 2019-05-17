import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
//import { Chart } from "chart.js";
import { ChartDataSets, ChartOptions, ChartType } from 'chart.js';
//import * as pluginDataLabels from 'chartjs-plugin-datalabels';
import { Label } from 'ng2-charts';
import { Usuario } from 'src/app/models/usuario';
import { ComercialService } from 'src/app/services/comercial.service';
import { PreloadService } from 'src/app/services/preload.service';



@Component({
  selector: 'app-comercial',
  templateUrl: './comercial.component.html',
  styleUrls: ['./comercial.component.css']
})
export class ComercialComponent implements OnInit {
  usuarios: Usuario[];
  annos: any[];
  meses: string[];
  relatorioList: any;
  //chart=[];
  mostrarRelatorio: boolean = false;
  mostrarGrafico: boolean = false;
  mostrarPizza: boolean = false;


  filtroForm: FormGroup;
  // error messages
  errorMessageResources = {
    anno_desde: {
      required: 'Año desde es requerido.',
    },
    mes_desde: {
      required: 'Mes desde es requerido.',
    },
    anno_hasta: {
      required: 'Año hasta es requerido.',
    },
    mes_hasta: {
      required: 'Mes hasta es requerido.',
    },
    consultores: {
      required: 'Consultores es requerido.',
    },
  };

  public barChartOptions: ChartOptions = {
    
    responsive: true,
    //Valor Máximo Es mayor que 32000
    
    plugins: {
      datalabels: {
        anchor: 'end',
        align: 'end',
      }
    }
  };
  public barChartLabels: Label[]
  public barChartType: ChartType = 'bar';
  public barChartLegend = true;
  //public barChartPlugins = [pluginDataLabels];

  public barChartData: ChartDataSets[];


  constructor(private comercialService: ComercialService,
    private fb: FormBuilder,
    public preloadService: PreloadService) {
    this.usuarios = [];
    this.annos = [];
    this.meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    this.filtroForm = this.fb.group({
      anno_desde: ['', Validators.required],
      mes_desde: ['', Validators.required],
      anno_hasta: ['', Validators.required],
      mes_hasta: ['', Validators.required],
      consultores: ['', Validators.required]
    });

  }

  ngOnInit() {
    this.getUsuarios();
    this.getAnnos();
  }

  getUsuarios() {
    this.comercialService.getUsuarios()
      .subscribe(res => {
        this.usuarios = <Usuario[]>res;
      });
  };
  getAnnos() {
    this.comercialService.getAnnos()
      .subscribe(res => {
        this.annos = <any[]>res;
      });
  };
  relatorio(form: FormGroup) {  
    this.preloadService.fireLoading();
    this.comercialService.getRelatorio(form.value)
      .subscribe(res => {
        this.mostrarRelatorio = true;
        this.mostrarPizza = false;
        this.mostrarGrafico = false;
        this.relatorioList = res;
        this.preloadService.stopLoading();
      }, error => {
          this.preloadService.haveErrors();
          this.preloadService.stopLoading();
      });
  }
  grafico(form: FormGroup) {    
    /* this.barChartLabels = ['enero 2007',
      'febrero 2007', 'marzo 2007', 'abril 2007', 'mayo 2007', 'junio 2007', 'julio 2007'];
    this.barChartData = [
      {
        data: [20, 20, 20, 20, 20, 20, 20], label: 'Custo_fixo', type: 'line', 
        backgroundColor: "transparent", steppedLine:true},
      { data: [65, 59, 80, 81, 56, 55, 40], label: 'Carlos Flabio' },
      { data: [65, 59, 80, 81, 56, 55, 40], label: 'Ana Paula' },
      { data: [65, 59, 80, 81, 56, 55, 40], label: 'Carlos Enrique' },
      { data: [28, 48, 40, 19, 86, 27, 90], label: 'Renato Marcus' },
    ]; */
    this.barChartLabels = [];
    this.barChartData = [];
    this.preloadService.fireLoading();
    this.comercialService.getGrafico(form.value)
      .subscribe(res => {
        this.mostrarRelatorio = false;
        this.mostrarPizza = false;
        this.mostrarGrafico = true;
        this.barChartType = 'bar';
        this.barChartOptions = {
          title: {
            display: true,
            text: ['Performance Comercial' , 
            form.value.anno_desde + ' ' + this.meses[form.value.mes_desde-1] + ' a ' + 
            form.value.anno_hasta + ' ' + this.meses[form.value.mes_hasta-1]]
          },
          responsive: true,
          //Valor Máximo Es mayor que 32000
          scales: {
            xAxes: [{}], yAxes: [{ ticks: { min: 0, max: res["max_value"] } }]
          },
          plugins: {
            datalabels: {
              anchor: 'end',
              align: 'end',
            }
          }};
        this.barChartLabels = res["labels"];
        this.barChartData = res["bar"];
        this.preloadService.stopLoading();
      }, error => {
          this.preloadService.haveErrors();
          this.preloadService.stopLoading();
      });
  }
  pizza(form: FormGroup) {
    this.preloadService.fireLoading();
    this.comercialService.getPizza(form.value)
      .subscribe(res => {
        this.mostrarRelatorio = false;
        this.mostrarPizza = true;
        this.mostrarGrafico = false;
        this.barChartType = 'pie';
        this.barChartOptions = {
          title: {
            display: true,
            text: ['Participação na Receitas',
              form.value.anno_desde + ' ' + this.meses[form.value.mes_desde - 1] + ' a ' +
              form.value.anno_hasta + ' ' + this.meses[form.value.mes_hasta - 1]]
          },
          responsive: true,
          plugins: {
            datalabels: {
              anchor: 'end',
              align: 'end',
            }
          }
        };
        this.barChartLabels =res["labels"];
        this.barChartData = [{ 'data': res["data"], label: 'Dataset 1'}];
        this.preloadService.stopLoading(); 
      }, error => {
          this.preloadService.haveErrors();
          this.preloadService.stopLoading();
      });
    
  }

}
