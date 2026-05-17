import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private readonly baseUrl = 'http://3.93.176.212:8000/api';

  constructor(private http: HttpClient) {}

  // 🔹 Perfil
  getPerfil(): Observable<any> {
    return this.http.get(`${this.baseUrl}/perfil`);
  }

  // 🔹 Personas
  getAllPeople(): Observable<any> {
    return this.http.get(`${this.baseUrl}/people`);
  }

  importarPersonasExcel(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('excel', file);
    return this.http.post(`${this.baseUrl}/importar-personas`, formData);
  }

  eliminarTodosLosRegistros(): Observable<any> {
    return this.http.delete(`${this.baseUrl}/people`);
  }

  subirPersonasConUsuario(personas: any[]): Observable<any> {
    return this.http.post(`${this.baseUrl}/personas`, personas);
  }

  // 🔹 Plantillas
  getPlantillasPorUsuario(email: string): Observable<any> {
    return this.http.get(`${this.baseUrl}/plantillas?usuario_email=${email}`);
  }

  guardarPlantilla(payload: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/plantillas`, payload);
  }

  getPlantillaPorId(id: number): Observable<any> {
    return this.http.get(`${this.baseUrl}/plantillas/${id}`);
  }

  actualizarPlantilla(id: number, data: any): Observable<any> {
    return this.http.put(`${this.baseUrl}/plantillas/${id}`, data);
  }

  eliminarPlantilla(id: number): Observable<any> {
    return this.http.delete(`${this.baseUrl}/plantillas/${id}`);
  }

  // 🔹 Correos
  enviarCorreos(data: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/enviar-correos`, data);
  }

  guardarSeleccionados(payload: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/destinatarios-seleccionados`, payload);
  }

  verificarDestinatarios(): Observable<any> {
    return this.http.get(`${this.baseUrl}/destinatarios-temporales/verificar`);
  }

  getCantidadDestinatarios(email: string): Observable<number> {
    return this.http.get<number>(`${this.baseUrl}/cantidad-destinatarios/${email}`);
  }

  getSmtpSettings(): Observable<any> {
    return this.http.get<any>(`${this.baseUrl}/smtp-settings`);
  }

  saveSmtpSettings(data: any): Observable<any> {
    return this.http.post(`${this.baseUrl}/smtp-settings`, data);
  }

  eliminarDestinatariosTemporales(email: string): Observable<any> {
    return this.http.post(`${this.baseUrl}/eliminar-destinatarios-temporales`, {
      usuario_email: email
    });
  }

  esperarTokenDisponible(): Promise<void> {
    return new Promise((resolve) => {
      const checkToken = () => {
        const token = sessionStorage.getItem('access_token');
        if (token) {
          resolve();
        } else {
          setTimeout(checkToken, 50);
        }
      };
      checkToken();
    });
  }

  obtenerCookieSanctum(): Promise<void> {
  return new Promise((resolve, reject) => {
    this.http.get('http://3.93.176.212:8000/sanctum/csrf-cookie', { withCredentials: true })
      .subscribe({
        next: () => resolve(),
        error: (err) => {
          console.error('[Error] al obtener cookie de Sanctum:', err);
          reject(err);
        }
      });
  });
}


}
