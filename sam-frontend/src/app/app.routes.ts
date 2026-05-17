import { Routes } from '@angular/router';
import { BienvenidaComponent } from './pages/bienvenida/bienvenida.component';
import { LoginComponent } from './pages/login/login.component';
import { RegistroComponent } from './pages/registro/registro.component';
import { GestionDatosComponent } from './pages/gestion-datos/gestion-datos.component';

import { ListaPlantillasComponent } from './pages/plantillas/lista-plantillas/lista-plantillas.component';
import { CrearPlantillaComponent } from './pages/plantillas/crear-plantilla/crear-plantilla.component';
import { VerPlantillaComponent } from './pages/plantillas/ver-plantilla/ver-plantilla.component';
import { EditarPlantillaComponent } from './pages/plantillas/editar-plantilla/editar-plantilla.component';

import { SmtpConfigComponent } from './smtp-config/smtp-config.component'; // ¡Importado!

export const routes: Routes = [
  { path: '', component: BienvenidaComponent },
  { path: 'login', component: LoginComponent },
  { path: 'registro', component: RegistroComponent },
  { path: 'gestion-datos', component: GestionDatosComponent },

  // Módulo Plantillas con rutas hijas
  {
    path: 'plantillas',
    children: [
      { path: '', component: ListaPlantillasComponent },          // /plantillas
      { path: 'crear', component: CrearPlantillaComponent },      // /plantillas/crear
      { path: 'ver/:id', component: VerPlantillaComponent },      // /plantillas/ver/1
      { path: 'editar/:id', component: EditarPlantillaComponent } // /plantillas/editar/1
    ]
  },

  // Módulo Gestión de Correos con carga diferida
  {
    path: 'gestion-correos',
    loadChildren: () =>
      import('./pages/gestion-correos/gestion-correos.routes').then(m => m.GESTION_CORREOS_ROUTES)
  },

  // Ruta para configuración SMTP
  { path: 'smtp-config', component: SmtpConfigComponent }, // Agregado

  { path: '**', redirectTo: '' }
];
