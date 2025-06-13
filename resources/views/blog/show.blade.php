@extends('layouts.vitrine')

@section('title', $article['title'] . ' - NaturaCorp')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <article class="blog-post">
                <header class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-success">{{ $article['category'] }}</span>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($article['date'])->format('d/m/Y') }}</small>
                    </div>
                    <h1 class="mb-4">{{ $article['title'] }}</h1>
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-user-circle me-2 text-success"></i>
                        <span class="text-muted">Par {{ $article['author'] }}</span>
                    </div>
                </header>

                <div class="blog-post-image mb-4">
                    <img src="{{ asset($article['image']) }}" alt="{{ $article['title'] }}" class="img-fluid rounded">
                </div>

                <div class="blog-post-content">
                    {!! nl2br(e($article['content'])) !!}
                </div>

                <footer class="mt-5 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-arrow-left me-2"></i>Retour au blog
                        </a>
                        <div class="social-share">
                            <span class="me-2">Partager :</span>
                            <a href="#" class="text-success me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-success me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-success"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </footer>
            </article>
        </div>
    </div>
</div>
@endsection

@section('extra_css')
<style>
.blog-post {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.blog-post-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.blog-post-content {
    line-height: 1.8;
    font-size: 1.1rem;
}

.blog-post-content p {
    margin-bottom: 1.5rem;
}

.blog-post-content ul,
.blog-post-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.social-share a {
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.social-share a:hover {
    opacity: 0.8;
}
</style>
@endsection 