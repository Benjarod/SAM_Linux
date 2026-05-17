
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, Router, RouterModule } from '@angular/router';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { ApiService } from '@app/services/api.service';

@Component({
  selector: 'app-ver-plantilla',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './ver-plantilla.component.html',
  styleUrls: ['./ver-plantilla.component.scss']
})
export class VerPlantillaComponent implements OnInit {
  titulo: string = '';
  contenidoSeguro: SafeHtml = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private sanitizer: DomSanitizer,
    private apiService: ApiService
  ) {}

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));

    this.apiService.getPlantillaPorId(id).subscribe({
      next: (data) => {
        this.titulo = data.title;
        // Sanitizar directamente para permitir HTML (incluidas imagenes Base64 o URLs)
        this.contenidoSeguro = this.sanitizer.bypassSecurityTrustHtml(data.description);
      },
      error: (err) => {
        console.error('[Error] al cargar plantilla:', err);
        this.router.navigate(['/plantillas']);
      }
    });
  }
}
