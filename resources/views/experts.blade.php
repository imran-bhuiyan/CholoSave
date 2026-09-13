@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('title', 'Our Experts - CholoSave')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="mb-2" style="color: #1846a3; font-weight: 700; letter-spacing: 1px;">Our Experts</h1>
        <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Glavrida for habitant morbi tristique senectus et netus et malesuada fames</p>
    </div>
    <div class="row justify-content-center">
        @foreach($experts as $expert)
            <div class="col-12 col-md-6 col-lg-4 mb-5 d-flex align-items-stretch">
                <div class="w-100 text-center">
                    <div class="mx-auto mb-4" style="width: 160px; height: 160px; background: #f3f6fa; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ Str::startsWith($expert->image, 'http') ? $expert->image : asset($expert->image) }}" alt="{{ $expert->name }}" style="width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    </div>
                    <div>
                        <div class="text-uppercase font-weight-bold" style="font-size: 1.1rem; letter-spacing: 1px;">{{ $expert->name }}</div>
                        <div class="mb-2">
                            <a href="#" class="text-primary" style="font-weight: 500; text-decoration: none;">{{ $expert->expertise }}</a>
                        </div>
                        <div class="text-muted mb-3" style="font-size: 0.97rem; min-height: 48px;">{{ $expert->bio }}</div>
                        <div class="mb-3">
                            <a href="mailto:{{ $expert->email }}" class="text-secondary mx-1" title="Email"><i class="fas fa-envelope fa-lg"></i></a>
                            <a href="tel:{{ $expert->phone }}" class="text-secondary mx-1" title="Phone"><i class="fas fa-phone fa-lg"></i></a>
                        </div>
                        <div>
                            <a href="#" class="text-secondary mx-2" title="Facebook"><i class="fab fa-facebook-f fa-lg"></i></a>
                            <a href="#" class="text-secondary mx-2" title="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="text-secondary mx-2" title="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
                            <a href="#" class="text-secondary mx-2" title="LinkedIn"><i class="fab fa-linkedin-in fa-lg"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
   
</div>

@push('styles')
<style>
    .font-weight-bold { font-weight: 700 !important; }
    .expert-card-social a:hover { color: #1846a3 !important; }
</style>
@endpush
@endsection 