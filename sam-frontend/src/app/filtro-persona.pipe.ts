import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'filtroPersona',
  standalone: true
})
export class FiltroPersonaPipe implements PipeTransform {

  transform(personas: any[], edadMin?: number, edadMax?: number, sexo?: string, nombre?: string): any[] {
    if (!personas) return [];

    return personas.filter(persona => {
      const edad = this.calcularEdad(persona.fecha_nacimiento);

      const cumpleEdadMin = edadMin ? edad >= edadMin : true;
      const cumpleEdadMax = edadMax ? edad <= edadMax : true;
      const cumpleSexo = sexo ? persona.sexo?.toLowerCase() === sexo.toLowerCase() : true;

      const nombreCompleto = `${persona.nombres} ${persona.ap_pat ?? ''} ${persona.ap_mat ?? ''}`.toLowerCase();
      const cumpleNombre = nombre ? nombreCompleto.includes(nombre.toLowerCase()) : true;

      return cumpleEdadMin && cumpleEdadMax && cumpleSexo && cumpleNombre;
    });
  }

  private calcularEdad(fechaNacimiento: string): number {
    if (!fechaNacimiento) return 0;
    const nacimiento = new Date(fechaNacimiento);
    const hoy = new Date();
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const m = hoy.getMonth() - nacimiento.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
      edad--;
    }
    return edad;
  }
}
