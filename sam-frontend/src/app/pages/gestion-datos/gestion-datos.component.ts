import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '@app/services/api.service';
import { HttpClientModule } from '@angular/common/http';
import { Router, RouterModule } from '@angular/router';
import Swal from 'sweetalert2';
import { FiltroPersonaPipe } from '@app/filtro-persona.pipe';
import { FormsModule } from '@angular/forms';
import * as XLSX from 'xlsx';
import * as FileSaver from 'file-saver';
import { NgxPaginationModule } from 'ngx-pagination';

@Component({
  selector: 'app-gestion-datos',
  standalone: true,
  imports: [
    CommonModule,
    HttpClientModule,
    FormsModule,
    RouterModule,
    FiltroPersonaPipe,
    NgxPaginationModule
  ],
  templateUrl: './gestion-datos.component.html',
  styleUrls: [
    './gestion-datos.component.scss',
    '../../../styles-bootstrap.scss'
  ]
})
export class GestionDatosComponent implements OnInit {
  registros: any[] = [];
  cargando: boolean = true;
  archivoExcel: File | null = null;

  edadMin?: number;
  edadMax?: number;
  filtroSexo: string = '';
  filtroNombre: string = '';

  seleccionados: any[] = [];
  emailUsuario: string = '';
  nombreUsuario: string = '';

  selectAll: boolean = false;
  page: number = 1;

  columnasRequeridas = [
    'rut', 'nombres', 'ap_pat', 'ap_mat',
    'fecha_nacimiento', 'sexo', 'ficha_clinica',
    'domicilio', 'fono', 'celu', 'email'
  ];

  equivalencias: { [key: string]: string } = {
    'rut': 'rut',
    'nombres': 'nombres',
    'nombre': 'nombres',
    'appat': 'ap_pat',
    'apellido_paterno': 'ap_pat',
    'apmat': 'ap_mat',
    'apellido_materno': 'ap_mat',
    'fechanaci': 'fecha_nacimiento',
    'fechanacimiento': 'fecha_nacimiento',
    'fecha_nacimiento': 'fecha_nacimiento',
    'sexo': 'sexo',
    'fichaclinic': 'ficha_clinica',
    'fichaclinica': 'ficha_clinica',
    'ficha_clinica': 'ficha_clinica',
    'domicilio': 'domicilio',
    'fono': 'fono',
    'celu': 'celu',
    'email': 'email'
  };

  constructor(private apiService: ApiService, private router: Router) {}

  async ngOnInit(): Promise<void> {
    try {
      // Asegura que haya token en localStorage
      await this.apiService.esperarTokenDisponible();

      // Solicita el cookie de Sanctum para sesión stateful
      await this.apiService.obtenerCookieSanctum();

      // Espera extra de 200ms para asegurar sincronización del token
      await new Promise(resolve => setTimeout(resolve, 200));

      const token = sessionStorage.getItem('access_token');
      const usuarioActual = JSON.parse(sessionStorage.getItem('usuarioActual') || '{}');

      if (!token || !usuarioActual.email) {
        this.router.navigate(['/login']);
        return;
      }

      this.emailUsuario = usuarioActual.email;
      this.nombreUsuario = this.emailUsuario.split('@')[0];

      this.apiService.getAllPeople().subscribe({
        next: (data: any[]) => {
          this.registros = data
            .map((persona: any) => ({
              ...persona,
              edad_persona: this.calcularEdad(persona.fecha_nacimiento),
              selected: false
            }));
          this.cargando = false;
        },
        error: (err: any) => {
          console.error('[Error] al obtener registros:', err);
          this.cargando = false;
        }
      });
    } catch (err) {
      console.error('[Error crítico] durante la inicialización:', err);
      this.router.navigate(['/login']);
    }
  }


//
  toggleSeleccionarTodos(): void {
    this.registrosFiltrados.forEach(persona => {
      persona.selected = this.selectAll;
    });
    if (this.selectAll) {
      this.seleccionados = [...this.registrosFiltrados];
    } else {
      this.seleccionados = [];
    }
  }

  toggleSeleccion(persona: any, event: any): void {
    persona.selected = event.target.checked;
    if (persona.selected) {
      this.seleccionados.push(persona);
    } else {
      this.seleccionados = this.seleccionados.filter(p => p !== persona);
    }
  }

  calcularEdad(fechaNacimiento: string): number | null {
    if (!fechaNacimiento) return null;
    const nacimiento = new Date(fechaNacimiento);
    if (isNaN(nacimiento.getTime())) return null;
    const hoy = new Date();
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const mes = hoy.getMonth() - nacimiento.getMonth();
    if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
      edad--;
    }
    return edad;
  }

  cerrarSesion(): void {
    sessionStorage.removeItem('access_token');
    sessionStorage.removeItem('usuarioActual');
    this.router.navigate(['/login']);
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.archivoExcel = input.files[0];
    }
  }

  importarArchivo(): void {
    if (!this.archivoExcel) {
      Swal.fire('Advertencia', 'Debe seleccionar un archivo Excel.', 'warning');
      return;
    }

    // Mostrar alerta de carga
    Swal.fire({
      title: 'Importando...',
      text: 'Estamos procesando su archivo Excel.',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    const reader = new FileReader();
    reader.onload = (e: any) => {
      const data = new Uint8Array(e.target.result);
      const workbook = XLSX.read(data, { type: 'array' });
      const sheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[sheetName];
      const registrosExcel = XLSX.utils.sheet_to_json(worksheet, { defval: '' });

      if (!registrosExcel.length) {
        Swal.close();
        Swal.fire('Error', 'El archivo Excel está vacío.', 'error');
        return;
      }

      const registrosNormalizados = registrosExcel.map((fila: any) => {
        const filaNormalizada: any = {};
        for (const key in fila) {
          if (Object.prototype.hasOwnProperty.call(fila, key)) {
            const keyNormalizado = key
              .toLowerCase()
              .replace(/[._\s]/g, '')
              .trim();

            const campoFinal = this.equivalencias[keyNormalizado];
            if (campoFinal) {
              let valor = fila[key];
              if (campoFinal === 'fecha_nacimiento') {
                if (typeof valor === 'number') {
                  const fechaBase = new Date(1899, 11, 30);
                  fechaBase.setDate(fechaBase.getDate() + valor);
                  valor = fechaBase.toISOString().split('T')[0];
                } else if (typeof valor === 'string' && !valor.includes('-')) {
                  valor = null;
                }
              }
              filaNormalizada[campoFinal] = valor !== '' ? valor : null;
            }
          }
        }
        return filaNormalizada;
      });

      const registrosConUsuario = registrosNormalizados.map((fila: any) => {
        const nuevoRegistro: any = {};
        this.columnasRequeridas.forEach(col => {
          nuevoRegistro[col] = fila[col] !== undefined ? fila[col] : null;
        });
        nuevoRegistro.usuario_email = this.emailUsuario;
        return nuevoRegistro;
      });

      this.apiService.subirPersonasConUsuario(registrosConUsuario).subscribe({
        next: () => {
          Swal.close();
          Swal.fire({
            icon: 'success',
            title: 'Importación exitosa',
            text: 'Los registros se han cargado correctamente.'
          });
          this.archivoExcel = null;
          this.ngOnInit();
        },
        error: (err) => {
          console.error('[Error] al importar Excel:', err);
          Swal.close();
          Swal.fire({
            icon: 'error',
            title: 'Error al importar',
            text: err.error?.message || 'Ocurrió un error durante la importación.'
          });
        }
      });
    };

    reader.readAsArrayBuffer(this.archivoExcel);
  }


  eliminarRegistros(): void {
    Swal.fire({
      title: '¿Estás seguro?',
      text: 'Esto eliminará todos los registros de personas y destinatarios temporales.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Primero eliminar registros de personas
        this.apiService.eliminarTodosLosRegistros().subscribe({
          next: () => {
            // Luego eliminar destinatarios temporales
            this.apiService.eliminarDestinatariosTemporales(this.emailUsuario).subscribe({
              next: () => {
                Swal.fire('Eliminado', 'Todos los registros han sido eliminados.', 'success');
                this.ngOnInit();
              },
              error: (err) => {
                console.error('[Error] al eliminar destinatarios temporales:', err);
                Swal.fire('Error', 'No se pudieron eliminar los destinatarios temporales.', 'error');
              }
            });
          },
          error: (err) => {
            console.error('[Error] al eliminar registros de personas:', err);
            Swal.fire('Error', err.error?.message || 'No se pudieron eliminar los registros de personas.', 'error');
          }
        });
      }
    });
  }

  exportarRegistros(tipo: 'xlsx' | 'csv'): void {
    const datosAExportar = this.seleccionados.length ? this.seleccionados : this.registrosFiltrados;
    const worksheet = XLSX.utils.json_to_sheet(datosAExportar);
    const libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, worksheet, 'Registros');

    const archivo = XLSX.write(libro, { bookType: tipo, type: 'array' });
    const blob = new Blob([archivo], { type: 'application/octet-stream' });
    FileSaver.saveAs(blob, `registros_exportados.${tipo}`);
  }

  get registrosFiltrados(): any[] {
    return this.registros
      .filter(p => this.filtrarPorEdad(p))
      .filter(p => this.filtrarPorSexo(p))
      .filter(p => this.filtrarPorNombre(p));
  }

  filtrarPorEdad(p: any): boolean {
    if (p.edad_persona == null || isNaN(Number(p.edad_persona))) {
      return true;
    }
    const edad = Number(p.edad_persona);
    if (this.edadMin != null && edad < this.edadMin) {
      return false;
    }
    if (this.edadMax != null && edad > this.edadMax) {
      return false;
    }
    return true;
  }

  filtrarPorSexo(p: any): boolean {
    return !this.filtroSexo || p.sexo === this.filtroSexo;
  }

  filtrarPorNombre(p: any): boolean {
    const termino = this.filtroNombre.toLowerCase();
    return (
      !this.filtroNombre ||
      p.nombres.toLowerCase().includes(termino) ||
      p.ap_pat.toLowerCase().includes(termino) ||
      p.ap_mat.toLowerCase().includes(termino)
    );
  }

  irAPlantillas(): void {
    this.router.navigate(['/plantillas']);
  }

  guardarSeleccionados(): void {
    if (!this.seleccionados.length) {
      Swal.fire('Advertencia', 'Debes seleccionar al menos un registro para guardar.', 'warning');
      return;
    }

    const sinCorreo = this.seleccionados.filter(p => !p.email || p.email.trim() === '');

    const continuarGuardado = () => {
      const payload = {
        destinatarios: this.seleccionados.map(dest => ({
          ...dest,
          usuario_email: this.emailUsuario
        }))
      };

      this.apiService.guardarSeleccionados(payload).subscribe({
        next: () => {
          sessionStorage.setItem('publicoObjetivoCargado', 'true');
          Swal.fire({
            icon: 'success',
            title: 'Datos guardados',
            text: 'Los destinatarios han sido guardados correctamente.'
          }).then(() => {
            this.router.navigate(['/plantillas']);
          });
        },
        error: (err) => {
          console.error('[Error] al guardar seleccionados:', err);
          Swal.fire('Error', err.error?.message || 'No se pudo guardar la selección.', 'error');
        }
      });
    };

    if (sinCorreo.length > 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Aviso importante',
        text: `Hay ${sinCorreo.length} personas seleccionadas sin correo electrónico. Estas personas no recibirán la promoción.`,
        confirmButtonText: 'Entendido'
      }).then(() => {
        continuarGuardado();
      });
    } else {
      continuarGuardado();
    }
  }



  confirmarSalir(): void {
    Swal.fire({
      title: '¿Desea salir del sistema?',
      text: 'Se cerrará su sesión actual.',
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