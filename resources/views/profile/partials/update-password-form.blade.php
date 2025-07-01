{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<section>
    <header class="mb-4">
        <h5 class="card-title fw-semibold text-lg text-gray-900 dark:text-gray-100 mb-0">
            {{ __('Update Kata Sandi') }}
        </h5>
        <p class="text-sm text-secondary-light mt-1">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label">{{ __('Kata Sandi Saat Ini') }}</label>
            <input id="current_password" name="current_password" type="password"
                class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" />
            @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Kata Sandi Baru') }}</label>
            <input id="password" name="password" type="password"
                class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" />
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Kata Sandi') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password" />
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-secondary-light m-0">
                    {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
