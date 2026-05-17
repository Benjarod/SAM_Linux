import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import Swal from 'sweetalert2';
import { ApiService } from '@app/services/api.service';
import { EditorComponent } from '@tinymce/tinymce-angular';

@Component({
  selector: 'app-editar-plantilla',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, EditorComponent],
  templateUrl: './editar-plantilla.component.html',
  styleUrls: ['./editar-plantilla.component.scss']
})
export class EditarPlantillaComponent implements OnInit {
  titulo: string = '';
  contenido: string = '';
  plantillaId!: number;

  editorConfig: any = {
    base_url: '/tinymce',
    suffix: '.min',
    script_url: '/tinymce/tinymce.min.js',
    height: 400,
    menubar: true,
    branding: false,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
      'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | formatselect | bold italic backcolor | ' +
             'alignleft aligncenter alignright alignjustify | ' +
             'bullist numlist outdent indent | removeformat | image | help',
    automatic_uploads: true,
    image_title: true,
    file_picker_types: 'image',
    file_picker_callback: (cb: any) => {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.onchange = () => {
        const file = input.files?.[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = () => {
            cb(reader.result, { title: file.name });
          };
          reader.readAsDataURL(file);
        }
      };
      input.click();
    }
  };

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private api: ApiService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.plantillaId = id;

    this.api.getPlantillaPorId(id).subscribe({
      next: (plantilla) => {
        this.titulo = plantilla.title;
        this.contenido = plantilla.description;
      },
      error: () => {
        Swal.fire('Error', 'No se pudo cargar la plantilla.', 'error');
        this.router.navigate(['/plantillas']);
      }
    });
  }

  guardarCambios(): void {
    if (!this.titulo.trim() || !this.contenido.trim()) {
      Swal.fire('Campos incompletos', 'Debes completar todos los campos.', 'warning');
      return;
    }

    const payload = {
      titulo: this.titulo,
      contenido: this.contenido
    };

    this.api.actualizarPlantilla(this.plantillaId, payload).subscribe({
      next: () => {
        Swal.fire('Plantilla actualizada', 'Los cambios se guardaron correctamente.', 'success');
        this.router.navigate(['/plantillas']);
      },
      error: (err) => {
        console.error('Error al actualizar plantilla:', err);
        Swal.fire('Error', 'No se pudo actualizar la plantilla.', 'error');
      }
    });
  }

  volver(): void {
    this.router.navigate(['/plantillas']);
  }
}
