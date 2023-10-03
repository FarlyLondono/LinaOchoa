{{--@extends('layouts.app')  Puedes extender de la plantilla base de tu aplicación si la tienes --}}

@section('content')
    <div class="container">
        @if(isset($servicios))
        <h1>{{ $servicios->name }}</h1>
        <p>Precio: {{ $servicios->precio }}</p>
        <p>Detalle: {{ $servicios->detalle }}</p>
        <img src="{{ asset('storage/app/servicios/' . $servicios->imagen) }}" alt="{{ $servicios->name }}" width="300">
        {{-- La línea anterior utiliza la función "asset" para generar la URL de la imagen almacenada en storage --}}
        @else
        <p>No se encontraron datos del servicio.</p>
    @endif
    </div>
@endsection