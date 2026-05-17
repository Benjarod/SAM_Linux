<x-mail::message>
# Señor (a) 

<h1>{{ $data['nombres'] ?? 'Usuario' }}</h1>

<h1>Por medio del presente le queremos informar lo siguiente:</h1>

<x-mail::panel>
{!! $contenido !!}
</x-mail::panel>

</x-mail::message>
