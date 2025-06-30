{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Profil Pengguna</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Profil</li>
        </ul>
    </div>

    <div class="row row-cols-1 gy-4">
        {{-- Form Informasi Profil --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        {{-- Form Update Password --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        {{-- Form Hapus Akun --}}
        <div class="col-lg-12">
            <div class="card h-100">
                <div class="card-body p-24">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
