import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '@app/services/auth.service';
import Swal from 'sweetalert2';


@Component({
  selector: 'app-registro',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './registro.component.html',
  styleUrls: ['./registro.component.scss']
})
export class RegistroComponent {
  registroForm: FormGroup;  // ← Renombrado aquí
  errorMessage: string = '';
  successMessage: string = '';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.registroForm = this.fb.group({  // ← También renombrado aquí
      name: ['', Validators.required],
      institucion: [''],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(8)]],
      password_confirmation: ['', Validators.required]
    });
  }

  onSubmit(): void {
    if (this.registroForm.invalid) {
      this.errorMessage = 'Formulario inválido';
      return;
    }
  
    const data = this.registroForm.value;
  
    if (data.password !== data.password_confirmation) {
      this.errorMessage = 'Las contraseñas no coinciden';
      return;
    }
  
    this.authService.register(data).subscribe({
      next: () => {
        Swal.fire({
          icon: 'success',
          title: '¡Cuenta creada con éxito!',
          text: 'Ahora puedes iniciar sesión.',
          confirmButtonText: 'Continuar'
        }).then(() => {
          this.router.navigate(['/login']);
        });
      },
      error: (err) => {
        this.errorMessage = err.error.message || 'Error al registrar usuario';
      }
    });
  }
  

  goToLogin(): void {
    this.router.navigate(['/login']);
  }
}
