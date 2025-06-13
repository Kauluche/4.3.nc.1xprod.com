@extends('layouts.vitrine')

@section('title', 'Blog - NaturaCorp')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="text-center mb-5">Notre Blog</h1>
            
            <div class="row g-4">
                @foreach($articles as $article)
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="{{ asset($article['image']) }}" class="card-img-top" alt="{{ $article['title'] }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-success">{{ $article['category'] }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($article['date'])->format('d/m/Y') }}</small>
                                </div>
                                <h2 class="h5 card-title">{{ $article['title'] }}</h2>
                                <p class="card-text">{{ $article['excerpt'] }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Par {{ $article['author'] }}</small>
                                    <a href="{{ route('blog.show', $article['id']) }}" class="btn btn-outline-success btn-sm">
                                        Lire la suite <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<style>
.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.card-body {
    padding: 1.5rem;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5em 1em;
}
</style>
@endsection 