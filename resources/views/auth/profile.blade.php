@extends('front.layout')

@section('main')
    <div class="row row-x-center s-styles">
        <div class="column large-6 tab-12">

            <!-- Session Status -->
            <x-auth.session-status :status="session('status')" />

            <!-- Validation Errors -->
            <x-auth.validation-errors :errors="$errors" />

            <h3 class="h-add-bottom">@lang('Profile')</h3>
            <form class="h-add-bottom" method="POST" action="{{ route('profile') }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name">@lang('Nom')</label>
                    <input
                        id="name"
                        class="h-full-width"
                        type="text"
                        name="name"
                        placeholder="@lang('Ton nom')"
                        value="{{ old('name', auth()->user()->name) }}"
                        required
                        autofocus>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email">@lang('Email')</label>
                    <input
                        id="email"
                        class="h-full-width"
                        type="email"
                        name="email"
                        placeholder="@lang('Ton email')"
                        value="{{ old('email', auth()->user()->email) }}"
                        required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password">@lang('Mot de passe') (@lang('optionel'))</label>
                    <input
                        id="password"
                        class="h-full-width"
                        type="password"
                        name="password"
                        placeholder="@lang('Ton mot de passe, si tu veux le changer')">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation">@lang('Confirmer le mot de passe')</label>
                    <input
                        id="password_confirmation"
                        class="h-full-width"
                        type="password"
                        name="password_confirmation"
                        placeholder="@lang('Confirme ton mot de passe')">
                </div>

                <x-auth.submit title="Save" />

                <a id="delete" href="{{ route('deleteAccount') }}" class="btn btn--primary h-full-width" style="background: crimson;">@lang('Supprimer le compte')</a>

            </form>
        </div>
    </div>

@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>

        // Variables
        const headers = {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }

        // Delete account
        const deleteAccount = async e => {
            e.preventDefault();
            Swal.fire({
                title: '@lang('Really delete your account?')',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: '@lang('Yes')',
                cancelButtonText: '@lang('No')',
                preConfirm: () => {
                    return fetch(e.target.getAttribute('href'), {
                        method: 'DELETE',
                        headers: headers
                    })
                        .then(response => {
                            if (response.ok) {
                                document.location.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '@lang('Whoops!')',
                                    text: '@lang('Something went wrong!')'
                                });
                            }
                        });
                }
            });
        }

        document.getElementById('delete').addEventListener('click', e => deleteAccount(e));

    </script>

@endsection
