<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $meatCut->name ?? '') }}" 
                   required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="animal_type" class="form-label">Animal Type</label>
            <select name="animal_type" 
                    id="animal_type" 
                    class="form-select @error('animal_type') is-invalid @enderror" 
                    required>
                <option value="">Select Animal Type</option>
                @foreach(['Beef', 'Pork', 'Chicken', 'Lamb', 'Goat'] as $type)
                    <option value="{{ strtolower($type) }}" 
                            {{ old('animal_type', $meatCut->animal_type ?? '') == strtolower($type) ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('animal_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="cut_type" class="form-label">Cut Type</label>
            <select name="cut_type" 
                    id="cut_type" 
                    class="form-select @error('cut_type') is-invalid @enderror" 
                    required>
                <option value="">Select Cut Type</option>
                @foreach(['Prime', 'Choice', 'Select', 'Standard'] as $type)
                    <option value="{{ strtolower($type) }}" 
                            {{ old('cut_type', $meatCut->cut_type ?? '') == strtolower($type) ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
            @error('cut_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" 
                      id="description" 
                      class="form-control @error('description') is-invalid @enderror" 
                      rows="3">{{ old('description', $meatCut->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label for="default_price_per_kg" class="form-label">Price per Kilogram (₱)</label>
            <input type="number" 
                   name="default_price_per_kg" 
                   id="default_price_per_kg" 
                   class="form-control @error('default_price_per_kg') is-invalid @enderror" 
                   value="{{ old('default_price_per_kg', $meatCut->default_price_per_kg ?? '') }}" 
                   step="0.01" 
                   min="0" 
                   required>
            @error('default_price_per_kg')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="minimum_stock_level" class="form-label">Minimum Stock Level</label>
            <input type="number" 
                   name="minimum_stock_level" 
                   id="minimum_stock_level" 
                   class="form-control @error('minimum_stock_level') is-invalid @enderror" 
                   value="{{ old('minimum_stock_level', $meatCut->minimum_stock_level ?? '10') }}" 
                   min="0" 
                   required>
            @error('minimum_stock_level')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" 
                   name="image" 
                   id="image" 
                   class="form-control @error('image') is-invalid @enderror" 
                   accept="image/*">
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if(isset($meatCut) && $meatCut->image_path)
                <div class="mt-2">
                    <img src="{{ Storage::url($meatCut->image_path) }}" 
                         alt="{{ $meatCut->name }}" 
                         class="img-thumbnail" 
                         style="max-width: 200px;">
                </div>
            @endif
        </div>

        @if(isset($meatCut))
            <div class="form-group mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" 
                           class="form-check-input" 
                           id="is_available" 
                           name="is_available" 
                           value="1" 
                           {{ old('is_available', $meatCut->is_available) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_available">Available for Sale</label>
                </div>
            </div>
        @endif
    </div>
</div> 