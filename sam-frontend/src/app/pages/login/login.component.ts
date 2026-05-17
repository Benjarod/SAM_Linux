import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule]
})
export class LoginComponent {
  loginForm: FormGroup;
  errorMessage: string = '';

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.loginForm = this.fb.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', Validators.required]
    });
  }

  onSubmit(): void {
    if (this.loginForm.invalid) {
      this.errorMessage = 'Formulario inválido';
      return;
    }

    const { email, password } = this.loginForm.value;

    // ✅ Mostrar spinner de carga
    Swal.fire({
      title: 'Entrando...',
      text: 'Validando credenciales',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    this.authService.login({ email, password }).subscribe({
      next: (response) => {
        // ✅ Guardar token y usuario
        sessionStorage.setItem('access_token', response.access_token);
        sessionStorage.setItem('usuarioActual', JSON.stringify({ email }));

        // ✅ Cerrar el SweetAlert al completar
        Swal.close();

        // Esperar para evitar error 401
        setTimeout(() => {
          this.router.navigate(['/gestion-datos']);
        }, 300);
      },
      error: (err) => {
        Swal.close(); // Cierra el spinner si hay error

        const mensaje = err.error?.message || '';
        if (mensaje.includes('Credenciales incorrectas')) {
          Swal.fire({
            icon: 'error',
            title: 'Credenciales incorrectas',
            text: 'Correo o contraseña no válidos. Intente nuevamente.',
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo iniciar sesión. Intente más tarde.',
          });
        }
      }
    });
  }
}
