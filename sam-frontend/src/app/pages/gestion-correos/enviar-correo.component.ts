import { Component, OnInit, isDevMode } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ApiService } from '@app/services/api.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import Swal from 'sweetalert2';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

@Component({
  selector: 'app-enviar-correo',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    RouterModule
  ],
  templateUrl: './enviar-correo.component.html',
  styleUrls: ['./enviar-correo.component.scss']
})
export class EnviarCorreoComponent implements OnInit {
  formCorreo: FormGroup;
  plantillas: any[] = [];
  emailUsuario: string = '';
  contenidoPlantilla: SafeHtml = '';
  cantidadDestinatarios: number = 0;
  configuracionSMTP: any = null;

  constructor(
    private fb: FormBuilder,
    private api: ApiService,
    private sanitizer: DomSanitizer
  ) {
    this.formCorreo = this.fb.group({
      plantilla_id: ['', Validators.required],
      nombre_remitente: ['', Validators.required],
      asunto: ['', Validators.required],
    });
  }

  ngOnInit(): void {
    const usuarioActual = JSON.parse(sessionStorage.getItem('usuarioActual') || '{}');
    this.emailUsuario = usuarioActual.email;

    // Cargar la configuración SMTP
    this.api.getSmtpSettings().subscribe({
      next: (config: any) => {
        this.configuracionSMTP = config;
        console.log('Configuración SMTP cargada:', config);
      },
      error: (err) => {
        console.warn('No se pudo cargar configuración SMTP:', err);
      }
    });

    this.api.getCantidadDestinatarios(this.emailUsuario).subscribe({
      next: (cantidad) => {
        this.cantidadDestinatarios = cantidad;
      },
      error: (err) => {
        console.error('Error al obtener cantidad de destinatarios', err);
      }
    });

    this.api.getPlantillasPorUsuario(this.emailUsuario).subscribe({
      next: (data) => {
        this.plantillas = data as any[];
        this.formCorreo.get('plantilla_id')?.valueChanges.subscribe(id => {
          const seleccionada = this.plantillas.find(p => p.id == id);
          this.contenidoPlantilla = this.sanitizer.bypassSecurityTrustHtml(seleccionada?.description || '');
        });
      },
      error: (err) => {
        console.error('Error al obtener plantillas:', err);
        Swal.fire('Error', 'No se pudieron cargar las plantillas.', 'error');
      }
    });
  }

  enviarCorreo(): void {
    if (this.formCorreo.invalid) {
      Swal.fire('Formulario incompleto', 'Todos los campos son obligatorios.', 'warning');
      return;
    }

    if (!this.configuracionSMTP) {
      Swal.fire('Error', 'No hay configuración SMTP. Por favor configura el servidor de correo primero.', 'error');
      return;
    }

    const payload = {
      ...this.formCorreo.value,
      usuario_email: this.emailUsuario
    };

    const plantillaSeleccionada = this.plantillas.find(p => p.id == this.formCorreo.value.plantilla_id);
    const titulo = plantillaSeleccionada?.title || 'Sin título';
    const contenido = plantillaSeleccionada?.description || '';

    // Determinar el remitente a mostrar en la confirmación
    const remitente = this.configuracionSMTP.username || '';
    const esGmail = this.configuracionSMTP.host?.includes('gmail');

    Swal.fire({
      title: '¿Confirmar envío?',
      html: `
        <div style="text-align: left;">
          <p><strong>📧 Remitente:</strong> ${remitente}</p>
          <p><strong>👤 Nombre Remitente:</strong> ${payload.nombre_remitente}</p>
          <p><strong>📌 Asunto:</strong> ${payload.asunto}</p>
          <p><strong>🧑‍🤝‍🧑 Destinatarios:</strong> ${this.cantidadDestinatarios}</p>
          <p><strong>📄 Plantilla:</strong> ${titulo}</p>
          <hr>
          <div style="max-height: 400px; overflow-y: auto; padding: 10px;">
            ${contenido}
          </div>
        </div>
      `,
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'Sí, enviar',
      cancelButtonText: 'Cancelar',
      width: '900px',
      customClass: {
        htmlContainer: 'text-start'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Mostrar carga mientras se envían correos
        Swal.fire({
          title: 'Enviando correos...',
          text: 'Esto puede tardar unos segundos. Por favor, no cierres esta ventana.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        this.api.enviarCorreos(payload).subscribe({
          next: () => {
            Swal.fire('✅ Éxito', 'Los correos han sido enviados correctamente.', 'success');
            this.formCorreo.reset();
            this.contenidoPlantilla = '';
          },
          error: (err) => {
            if (isDevMode()) {
              console.error('Error al enviar correos:', err);
            }

            const mensaje = err.error?.message || err.message || '';
            let detalle = 'Hubo un problema al enviar los correos.';

            if (mensaje.includes('Too many emails')) {
              detalle = 'Mailtrap bloqueó el envío porque se excedió el límite de correos por segundo en el plan gratuito. Intenta de nuevo en unos segundos o reduce la cantidad de destinatarios.';
            }

            Swal.fire('Error', detalle, 'error');
          }
        });
      }
    });
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
        window.location.href = '/login';
      }
    });
  }
}
