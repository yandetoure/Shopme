@extends('dashboard.layout')

@section('title', 'Paramètres - ShopMe')
@section('page-title', 'Paramètres du Site')

@section('content')
<div class="max-w-6xl mx-auto">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

        <div class="space-y-6">
            <!-- Informations générales -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Informations générales</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Nom du site *</label>
                        <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name) }}" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('site_name') border-red-500 @enderror">
                        @error('site_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">Email du site</label>
                        <input type="email" id="site_email" name="site_email" value="{{ old('site_email', $settings->site_email) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                <div>
                        <label for="slogan" class="block text-sm font-medium text-gray-700 mb-2">Slogan</label>
                        <input type="text" id="slogan" name="slogan" value="{{ old('slogan', $settings->slogan) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="Votre slogan ici...">
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>Adresse complète
                        </label>
                        <textarea id="address" name="address" rows="3"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                  placeholder="Ex: 123 Rue de Paris, 75001 Paris, France">{{ old('address', $settings->address) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Cette adresse sera affichée dans le footer du site</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone text-orange-500 mr-2"></i>Numéro de téléphone
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $settings->phone) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="Ex: +33 1 23 45 67 89">
                        <p class="text-xs text-gray-500 mt-1">Ce numéro sera affiché dans le footer du site</p>
                </div>

                <div>
                        <label for="email_contact" class="block text-sm font-medium text-gray-700 mb-2">Email de contact</label>
                        <input type="email" id="email_contact" name="email_contact" value="{{ old('email_contact', $settings->email_contact) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Logo du site</label>
                        <div class="flex items-start space-x-4">
                            <div class="flex-1">
                                <input type="file" id="logo" name="logo" accept="image/*"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                       onchange="previewLogo(this)">
                                <p class="text-xs text-gray-500 mt-1">Formats acceptés: JPG, PNG, GIF, SVG (max 2MB)</p>
                            </div>
                            @if($settings->logo)
                                <div class="flex-shrink-0">
                                    <img id="logo-preview" src="{{ asset('storage/' . $settings->logo) }}" alt="Logo actuel" 
                                         class="w-32 h-32 object-contain border rounded-lg p-2">
                                </div>
                            @else
                                <div class="flex-shrink-0">
                                    <img id="logo-preview" src="" alt="Aperçu" 
                                         class="w-32 h-32 object-contain border rounded-lg p-2 hidden">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnalisation des couleurs -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4 pb-2 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">Personnalisation des couleurs</h3>
                    <div class="text-xs text-gray-500">Aperçu des couleurs actuelles</div>
                </div>

                <!-- Aperçu des couleurs actuelles -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-xs font-medium text-gray-700 mb-3">Aperçu des couleurs configurées :</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="flex items-center space-x-2">
                            <div id="preview-navbar" class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $settings->navbar_color ?? '#ffffff' }}"></div>
                            <span class="text-xs text-gray-600">Navbar</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="preview-primary" class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $settings->primary_color ?? '#f97316' }}"></div>
                            <span class="text-xs text-gray-600">Primaire</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="preview-secondary" class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $settings->secondary_color ?? '#6b7280' }}"></div>
                            <span class="text-xs text-gray-600">Secondaire</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="preview-text" class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $settings->text_color ?? '#1f2937' }}"></div>
                            <span class="text-xs text-gray-600">Texte</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="preview-background" class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $settings->background_color ?? '#ffffff' }}"></div>
                            <span class="text-xs text-gray-600">Fond</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div id="preview-navbar-text" class="w-8 h-8 rounded border border-gray-300" style="background-color: {{ $settings->navbar_text_color ?? '#000000' }}"></div>
                            <span class="text-xs text-gray-600">Texte Navbar</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="navbar_color" class="block text-sm font-medium text-gray-700 mb-2">Couleur de la navbar</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="navbar_color" name="navbar_color" value="{{ old('navbar_color', $settings->navbar_color) }}"
                                   class="w-16 h-10 border rounded cursor-pointer">
                            <input type="text" value="{{ old('navbar_color', $settings->navbar_color) }}"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   onchange="document.getElementById('navbar_color').value = this.value">
                        </div>
                    </div>

                    <div>
                        <label for="navbar_text_color" class="block text-sm font-medium text-gray-700 mb-2">Couleur du texte de la navbar</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="navbar_text_color" name="navbar_text_color" value="{{ old('navbar_text_color', $settings->navbar_text_color) }}"
                                   class="w-16 h-10 border rounded cursor-pointer">
                            <input type="text" value="{{ old('navbar_text_color', $settings->navbar_text_color) }}"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   onchange="document.getElementById('navbar_text_color').value = this.value">
                        </div>
                    </div>

                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-2">Couleur principale</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="primary_color" name="primary_color" value="{{ old('primary_color', $settings->primary_color) }}"
                                   class="w-16 h-10 border rounded cursor-pointer">
                            <input type="text" value="{{ old('primary_color', $settings->primary_color) }}"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   onchange="document.getElementById('primary_color').value = this.value">
                        </div>
                    </div>

                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 mb-2">Couleur secondaire</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $settings->secondary_color) }}"
                                   class="w-16 h-10 border rounded cursor-pointer">
                            <input type="text" value="{{ old('secondary_color', $settings->secondary_color) }}"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   onchange="document.getElementById('secondary_color').value = this.value">
                        </div>
                    </div>

                    <div>
                        <label for="text_color" class="block text-sm font-medium text-gray-700 mb-2">Couleur du texte</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="text_color" name="text_color" value="{{ old('text_color', $settings->text_color) }}"
                                   class="w-16 h-10 border rounded cursor-pointer">
                            <input type="text" value="{{ old('text_color', $settings->text_color) }}"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   onchange="document.getElementById('text_color').value = this.value">
                        </div>
                </div>

                <div>
                        <label for="background_color" class="block text-sm font-medium text-gray-700 mb-2">Couleur de fond</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" id="background_color" name="background_color" value="{{ old('background_color', $settings->background_color) }}"
                                   class="w-16 h-10 border rounded cursor-pointer">
                            <input type="text" value="{{ old('background_color', $settings->background_color) }}"
                                   class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                   onchange="document.getElementById('background_color').value = this.value">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typographie -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Typographie</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="font_family" class="block text-sm font-medium text-gray-700 mb-2">Police de caractères principale</label>
                        <select id="font_family" name="font_family"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="Inter, sans-serif" {{ old('font_family', $settings->font_family) == 'Inter, sans-serif' ? 'selected' : '' }}>Inter</option>
                            <option value="Roboto, sans-serif" {{ old('font_family', $settings->font_family) == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                            <option value="Open Sans, sans-serif" {{ old('font_family', $settings->font_family) == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                            <option value="Lato, sans-serif" {{ old('font_family', $settings->font_family) == 'Lato, sans-serif' ? 'selected' : '' }}>Lato</option>
                            <option value="Montserrat, sans-serif" {{ old('font_family', $settings->font_family) == 'Montserrat, sans-serif' ? 'selected' : '' }}>Montserrat</option>
                            <option value="Poppins, sans-serif" {{ old('font_family', $settings->font_family) == 'Poppins, sans-serif' ? 'selected' : '' }}>Poppins</option>
                            <option value="Arial, sans-serif" {{ old('font_family', $settings->font_family) == 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
                            <option value="Georgia, serif" {{ old('font_family', $settings->font_family) == 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
                            <option value="Times New Roman, serif" {{ old('font_family', $settings->font_family) == 'Times New Roman, serif' ? 'selected' : '' }}>Times New Roman</option>
                        </select>
                </div>

                <div>
                        <label for="heading_font" class="block text-sm font-medium text-gray-700 mb-2">Police pour les titres</label>
                        <select id="heading_font" name="heading_font"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="">Même que la police principale</option>
                            <option value="Inter, sans-serif" {{ old('heading_font', $settings->heading_font) == 'Inter, sans-serif' ? 'selected' : '' }}>Inter</option>
                            <option value="Roboto, sans-serif" {{ old('heading_font', $settings->heading_font) == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                            <option value="Open Sans, sans-serif" {{ old('heading_font', $settings->heading_font) == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                            <option value="Montserrat, sans-serif" {{ old('heading_font', $settings->heading_font) == 'Montserrat, sans-serif' ? 'selected' : '' }}>Montserrat</option>
                            <option value="Poppins, sans-serif" {{ old('heading_font', $settings->heading_font) == 'Poppins, sans-serif' ? 'selected' : '' }}>Poppins</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Paramètres financiers -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Paramètres financiers</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Devise</label>
                        <input type="text" id="currency" name="currency" value="{{ old('currency', $settings->currency) }}"
                               class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
                    <p class="text-xs text-gray-500 mt-1">La devise est fixée en FCFA</p>
                    </div>

                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Taux de TVA (%) *</label>
                        <input type="number" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate) }}" 
                               step="0.01" min="0" max="100" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('tax_rate') border-red-500 @enderror">
                        @error('tax_rate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                </div>

                <div>
                        <label for="default_shipping" class="block text-sm font-medium text-gray-700 mb-2">Livraison par défaut (FCFA) *</label>
                        <input type="number" id="default_shipping" name="default_shipping" value="{{ old('default_shipping', $settings->default_shipping) }}" 
                               step="0.01" min="0" required
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('default_shipping') border-red-500 @enderror">
                        @error('default_shipping')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Réseaux sociaux -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Réseaux sociaux</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook
                        </label>
                        <input type="url" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings->facebook_url) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="https://facebook.com/votre-page">
                    </div>

                    <div>
                        <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-twitter text-blue-400 mr-2"></i>Twitter
                        </label>
                        <input type="url" id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $settings->twitter_url) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="https://twitter.com/votre-compte">
                    </div>

                    <div>
                        <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram
                        </label>
                        <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings->instagram_url) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="https://instagram.com/votre-compte">
                </div>

                <div>
                        <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-linkedin text-blue-700 mr-2"></i>LinkedIn
                        </label>
                        <input type="url" id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $settings->linkedin_url) }}"
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                               placeholder="https://linkedin.com/company/votre-entreprise">
                    </div>
                </div>
                </div>

            <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-4">
                <button type="submit" class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Enregistrer les paramètres
                    </button>
                </div>
            </div>
        </form>
    </div>

<script>
    function previewLogo(input) {
        const preview = document.getElementById('logo-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Synchroniser les inputs de couleur et mettre à jour l'aperçu
    const colorMappings = {
        'navbar_color': 'preview-navbar',
        'primary_color': 'preview-primary',
        'secondary_color': 'preview-secondary',
        'text_color': 'preview-text',
        'background_color': 'preview-background',
        'navbar_text_color': 'preview-navbar-text'
    };

    function updateColorPreview(inputName, colorValue) {
        const previewId = colorMappings[inputName];
        if (previewId) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.style.backgroundColor = colorValue;
            }
        }
    }

    document.querySelectorAll('input[type="color"]').forEach(colorInput => {
        colorInput.addEventListener('input', function() {
            const textInput = this.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = this.value;
            }
            // Mettre à jour l'aperçu
            updateColorPreview(this.name, this.value);
        });
    });

    document.querySelectorAll('input[type="text"]').forEach(textInput => {
        if (textInput.previousElementSibling && textInput.previousElementSibling.type === 'color') {
            textInput.addEventListener('input', function() {
                const colorInput = this.parentElement.querySelector('input[type="color"]');
                if (colorInput && /^#[0-9A-F]{6}$/i.test(this.value)) {
                    colorInput.value = this.value;
                    // Mettre à jour l'aperçu
                    const name = colorInput.name;
                    updateColorPreview(name, this.value);
                }
            });
        }
    });
</script>
@endsection
