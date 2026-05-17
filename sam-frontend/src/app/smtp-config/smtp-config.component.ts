import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ApiService } from '@app/services/api.service';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-smtp-config',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './smtp-config.component.html',
  styleUrls: ['./smtp-config.component.scss']
})
export class SmtpConfigComponent implements OnInit {
  smtpForm!: FormGroup;
  loading = false;
  mostrarAlerta = true;
  isGmail = false;

  constructor(
    private fb: FormBuilder,
    private api: ApiService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.smtpForm = this.fb.group({
      mailer: ['smtp', Validators.required],
      host: ['', Validators.required],
      port: [587, [Validators.required, Validators.min(1)]],
      username: ['', Validators.required],
      password: ['', Validators.required],
      encryption: ['tls'],
      email_from: ['', [Validators.email]]
    });

    this.cargarConfiguracion();
    
    // Detectar cuando el host cambia para controlar lógica específica de Gmail
    this.smtpForm.get('host')?.valueChanges.subscribe(host => {
      this.isGmail = host && host.includes('gmail');
      
      if (this.isGmail) {
        const username = this.smtpForm.get('username')?.value;
        // Solo si el username tiene formato email, lo usamos
        if (username && username.includes('@')) {
          this.smtpForm.get('email_from')?.setValue(username);
        }
        
        // Para Gmail, hacemos el campo email_from opcional
        this.smtpForm.get('email_from')?.clearValidators();
        this.smtpForm.get('email_from')?.addValidators(Validators.email);
      } else {
        // Para otros servidores, email_from es requerido
        this.smtpForm.get('email_from')?.clearValidators();
        this.smtpForm.get('email_from')?.addValidators([Validators.required, Validators.email]);
      }
      
      this.smtpForm.get('email_from')?.updateValueAndValidity();
    });
    
    // Observar cambios en el username para autocompletar email_from en Gmail
    this.smtpForm.get('username')?.valueChanges.subscribe(username => {
      if (this.isGmail && username && username.includes('@')) {
        this.smtpForm.get('email_from')?.setValue(username);
      }
    });
  }

  cargarConfiguracion(): void {
    this.api.getSmtpSettings().subscribe({
      next: (data) => {
        this.smtpForm.patchValue(data);
        // Verificar si es Gmail después de cargar los datos
        const host = data?.host || '';
        this.isGmail = host.includes('gmail');
      },
      error: () => {
        // No mostrar error si no tiene configuración aún
      }
    });
  }

  guardar(): void {
    // Si es Gmail y el email_from está vacío, usar el username
    if (this.isGmail && (!this.smtpForm.get('email_from')?.value || this.smtpForm.get('email_from')?.value === '')) {
      this.smtpForm.get('email_from')?.setValue(this.smtpForm.get('username')?.value);
    }
    
    if (this.smtpForm.invalid) {
      Swal.fire('Error', 'Por favor completa todos los campos obligatorios.', 'error');
      return;
    }

    this.loading = true;
    this.api.saveSmtpSettings(this.smtpForm.value).subscribe({
      next: () => {
        this.loading = false;
        Swal.fire({
          title: '¡Listo!', 
          text: 'Configuración guardada exitosamente.',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          // Redirigir a la página de gestión de correos
          this.router.navigate(['/gestion-correos']);
        });
      },
      error: () => {
        this.loading = false;
        Swal.fire('Error', 'No se pudo guardar la configuración.', 'error');
      }
    });
  }

  volver(): void {
    window.history.back();
  }

  cerrarAlerta() {
    this.mostrarAlerta = false;
  }
}
