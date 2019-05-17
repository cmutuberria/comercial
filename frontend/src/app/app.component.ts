import { Component } from '@angular/core';
import { PreloadService } from './services/preload.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  title = 'Comercial Web';

  constructor(public preloadService: PreloadService){
  }

  ngOnInit() {
    this.preloadService.stopLoading();
  }
}
