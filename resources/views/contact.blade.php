@extends('layouts.vitrine')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Contactez-nous</h1>

    <div class="row">
        <div class="col-md-6 mb-4">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4">Nos coordonnées</h5>
                    <p><i class="fas fa-map-marker-alt text-success me-2"></i> 123 Rue de la santé 51100 Reims</p>
                    <p><i class="fas fa-phone text-success me-2"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope text-success me-2"></i> contact@natura.com</p>
                    
                    <div id="map" class="mt-4" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
#map {
    border-radius: 4px;
}
</style>
@endsection

@section('extra_js')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('map').setView([49.2583, 4.0317], 13); // Coordonnées de Reims

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([49.2583, 4.0317]).addTo(map);
    marker.bindPopup("<b>NaturaCorp</b><br>123 Rue de la santé<br>51100 Reims").openPopup();
});
</script>
@endsection