@extends('layouts.vitrine')

@section('content')
<div class="hero position-relative">
    <div class="hero-image">
        <img src="{{ asset('images/vitrine/hero/background.jpg') }}" alt="Nature background" class="w-100">
    </div>
    <div class="hero-content position-absolute top-50 start-50 translate-middle text-center text-white">
        <h1 class="display-4">NaturaCorp : l'innovation naturelle pour votre santé</h1>
        <div class="mt-4">
            <a href="{{ route('contact') }}" class="btn btn-success me-2">Nous Contacter</a>
            <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#newsletterModal">
                S'inscrire
            </button>
        </div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Notre Produit</h2>
        <div class="row align-items-center">
            <div class="col-md-4">
                <img src="{{ asset('images/vitrine/products/mushblue.png') }}" alt="MushBlue" class="img-fluid">
            </div>
            <div class="col-md-8">
                <h3>MushBlue</h3>
                <p class="lead">Complément à base d'extrait de champignon</p>
                <div class="mt-3">
                    <a href="{{ route('products.mushblue') }}" class="btn btn-success me-2">En savoir plus</a>
                    <a href="{{ asset('pdf/mushblue.pdf') }}" class="btn btn-outline-success" download>
                        <i class="fas fa-download"></i> Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Blog</h2>
        <div class="row">
            @foreach($latestArticles as $article)
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="{{ asset($article['image']) }}" class="card-img-top" alt="{{ $article['title'] }}" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <span class="badge bg-success">{{ $article['category'] }}</span>
                            <small class="text-muted ms-2">{{ \Carbon\Carbon::parse($article['date'])->format('d/m/Y') }}</small>
                        </div>
                        <h5 class="card-title">{{ $article['title'] }}</h5>
                        <p class="card-text">{{ Str::limit($article['excerpt'], 100) }}</p>
                        <div class="mt-auto">
                            <a href="{{ route('blog.show', $article['id']) }}" class="btn btn-success">En savoir plus</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('blog.index') }}" class="btn btn-outline-success">Voir tous les articles</a>
        </div>
    </div>
</section> 

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">L'entreprise</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <a href="{{ route('enterprise') }}" class="btn btn-success">Découvrir</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Modal -->
<div class="modal fade" id="newsletterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">S'inscrire à la newsletter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('newsletter.subscribe') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Entrez votre E-mail" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">S'inscrire</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<style>
.hero {
    height: 500px;
    overflow: hidden;
}

.hero-image {
    position: relative;
    height: 100%;
}

.hero-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-image::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
}

.hero-content {
    z-index: 1;
    width: 80%;
}

.card {
    height: 100%;
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}
</style>
@endsection