import { Routes } from '@angular/router';
import { EnviarCorreoComponent } from './enviar-correo.component';

export const GESTION_CORREOS_ROUTES: Routes = [
  {
    path: '',
    component: EnviarCorreoComponent,
    title: 'Gestión de Correos'
  }
];
