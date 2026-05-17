import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { ApiService } from '@app/services/api.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-lista-plantillas',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './lista-plantillas.component.html',
  styleUrls: ['./lista-plantillas.component.scss']
})
export class ListaPlantillasComponent implements OnInit {
  plantillas: any[] = [];
  emailUsuario: string = '';

  constructor(
    private router: Router,
    private apiService: ApiService
  ) {}

  ngOnInit(): void {
    const usuarioActual = JSON.parse(sessionStorage.getItem('usuarioActual') || '{}');
    this.emailUsuario = usuarioActual.email;

    this.apiService.getPlantillasPorUsuario(this.emailUsuario).subscribe({
      next: (data) => {
        this.plantillas = data as any[];

        // ✅ Solo mostrar si localStorage indica que se guardaron destinatarios
        const mostrarAlerta = sessionStorage.getItem('publicoObjetivoCargado') === 'true';

        if (mostrarAlerta) {
          this.apiService.verificarDestinatarios().subscribe({
            next: (res) => {
              if (res.existen) {
                Swal.fire({
                  icon: 'success',
                  title: '✅ Público objetivo cargado correctamente',
                  showConfirmButton: false,
                  timer: 2000
                });
              }
              // ✅ Limpia la bandera para no volver a mostrar
              sessionStorage.removeItem('publicoObjetivoCargado');
            },
            error: (err) => console.error('[Error] al verificar destinatarios:', err)
          });
        }
      },
      error: () => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'No se pudieron cargar las plantillas.'
        });
      }
    });
  }

  verPlantilla(plantilla: any): void {
    this.router.navigate(['/plantillas/ver', plantilla.id]);
  }

  editarPlantilla(plantilla: any): void {
    this.router.navigate(['/plantillas/editar', plantilla.id]);
  }

  eliminarPlantilla(plantilla: any): void {
    Swal.fire({
      title: '¿Eliminar plantilla?',
      text: `Esta acción eliminará la plantilla "${plantilla.title}" del sistema.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        this.apiService.eliminarPlantilla(plantilla.id).subscribe({
          next: () => {
            this.plantillas = this.plantillas.filter(p => p.id !== plantilla.id);
            Swal.fire('Eliminada', 'La plantilla ha sido eliminada.', 'success');
          },
          error: (err) => {
            console.error('[Error] al eliminar plantilla:', err);
            Swal.fire('Error', 'No se pudo eliminar la plantilla.', 'error');
          }
        });
      }
    });
  }
  

  continuar(): void {
    this.router.navigate(['/gestion-correos']);
  }

  confirmarSalir(): void {
    Swal.fire({
      title: '¿Desea salir del sistema?',
      text: 'Se cerrará su sesión actual',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, salir',
      cancelButtonText: 'No',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
    }).then((result) => {
      if (result.isConfirmed) {
        sessionStorage.removeItem('access_token');
        sessionStorage.removeItem('usuarioActual');
        this.router.navigate(['/login']);
      }
    });
  }
  
}
