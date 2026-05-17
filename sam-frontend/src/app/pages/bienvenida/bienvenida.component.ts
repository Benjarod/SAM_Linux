import { Component, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-bienvenida',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './bienvenida.component.html',
  styleUrls: ['./bienvenida.component.scss'],
  encapsulation: ViewEncapsulation.None  // ✅ Aplica los estilos SCSS sin restricciones
})
export class BienvenidaComponent {}
