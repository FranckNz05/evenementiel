@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Modifier mon profil</h1>

        <form action="{{ route('organizer.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-6">
            @csrf
            @method('PUT')

            <!-- Informations de base -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Informations de base</h2>

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Nom *</label>
                    <input type="text" name="name" id="name"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="bio" class="block text-gray-700 font-medium mb-2">Biographie</label>
                    <textarea name="bio" id="bio" rows="4"
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="profil_image" class="block text-gray-700 font-medium mb-2">Photo de profil</label>
                    @if($user->profil_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $user->profil_image) }}"
                                 alt="Profile"
                                 class="w-32 h-32 object-cover rounded-lg">
                        </div>
                    @endif
                    <input type="file" name="profil_image" id="profil_image"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           accept="image/*">
                    @error('profil_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Réseaux sociaux -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Réseaux sociaux</h2>
                <p class="text-gray-600 mb-4">Ajoutez jusqu'à 5 liens vers vos réseaux sociaux</p>

                <div id="social-links-container">
                    @foreach($user->socialLinks as $index => $link)
                    <div class="social-link-group mb-4">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <select name="social_links[{{ $index }}][platform]"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="facebook" {{ $link->platform == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="twitter" {{ $link->platform == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                    <option value="instagram" {{ $link->platform == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="linkedin" {{ $link->platform == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                    <option value="youtube" {{ $link->platform == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="tiktok" {{ $link->platform == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <input type="url" name="social_links[{{ $index }}][url]"
                                       value="{{ $link->url }}"
                                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                       placeholder="https://">
                            </div>
                        </div>
                        <button type="button" class="remove-social-link text-red-500 text-sm mt-1">Supprimer</button>
                    </div>
                    @endforeach
                </div>

                <button type="button" id="add-social-link"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
                        {{ $user->socialLinks->count() >= 5 ? 'disabled' : '' }}>
                    Ajouter un réseau social
                </button>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('social-links-container');
        const addButton = document.getElementById('add-social-link');
        let linkCount = {{ $user->socialLinks->count() }};

        addButton.addEventListener('click', function() {
            if (linkCount >= 5) return;

            const index = linkCount;
            const template = `
                <div class="social-link-group mb-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <select name="social_links[${index}][platform]"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="facebook">Facebook</option>
                                <option value="twitter">Twitter</option>
                                <option value="instagram">Instagram</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="youtube">YouTube</option>
                                <option value="tiktok">TikTok</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="url" name="social_links[${index}][url]"
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                   placeholder="https://">
                        </div>
                    </div>
                    <button type="button" class="remove-social-link text-red-500 text-sm mt-1">Supprimer</button>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', template);
            linkCount++;

            if (linkCount >= 5) {
                addButton.setAttribute('disabled', 'disabled');
            }
        });

        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-social-link')) {
                e.target.closest('.social-link-group').remove();
                linkCount--;
                addButton.removeAttribute('disabled');
            }
        });
    });
</script>
@endpush
@endsection
