@extends('layouts.butcher')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Create Product') }}
                </h2>
            </div>
        </div>
        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        <div class="row row-cards">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Product Image') }}
                                </h3>
                                <img class="img-account-profile mb-2"
                                     src="{{ asset('assets/img/products/default.webp') }}"
                                     id="image-preview" />
                                <div class="small font-italic text-muted mb-2">
                                    JPG or PNG no larger than 2 MB
                                </div>
                                <input type="file"
                                       accept="image/*"
                                       id="image"
                                       name="product_image"
                                       class="form-control @error('product_image') is-invalid @enderror"
                                       onchange="previewImage();">
                                @error('product_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Product Create') }}</h3>
                                <div class="card-actions">
                                    <a href="{{ route('products.index') }}" class="btn-action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M18 6l-12 12"></path>
                                            <path d="M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <x-input name="name" id="name" placeholder="Product name"
                                                 value="{{ old('name') }}" />
                                    </div>

                                    {{-- Meat Cut --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="meat_cut_id" class="form-label">
                                                Meat Cut <span class="text-danger">*</span>
                                            </label>
                                            <select name="meat_cut_id" id="meat_cut_id"
                                                    class="form-select @error('meat_cut_id') is-invalid @enderror">
                                                <option selected disabled>Select a meat cut:</option>
                                                @foreach ($meatCuts as $meatCut)
                                                    <option value="{{ $meatCut->id }}"
                                                        {{ old('meat_cut_id') == $meatCut->id ? 'selected' : '' }}>
                                                        {{ $meatCut->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('meat_cut_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Product Category --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">
                                                Product Category <span class="text-danger">*</span>
                                            </label>
                                            @if ($categories->count() === 1)
                                                <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror"
                                                        readonly>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" selected>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select name="category_id" id="category_id"
                                                        class="form-select @error('category_id') is-invalid @enderror">
                                                    <option selected disabled>Select a category:</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Unit --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="unit_id" class="form-label">
                                                Unit <span class="text-danger">*</span>
                                            </label>
                                            @if ($units->count() === 1)
                                                <select name="unit_id" id="unit_id"
                                                        class="form-select @error('unit_id') is-invalid @enderror"
                                                        readonly>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}" selected>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <select name="unit_id" id="unit_id"
                                                        class="form-select @error('unit_id') is-invalid @enderror">
                                                    <option selected disabled>Select a unit:</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}"
                                                            {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                            {{ $unit->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                            @error('unit_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- New Required Fields --}}
                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Price per KG" name="price_per_kg"
                                                 id="price_per_kg" placeholder="0.00"
                                                 value="{{ old('price_per_kg') }}" step="0.01" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="date" label="Processing Date" name="processing_date"
                                                 id="processing_date" value="{{ old('processing_date') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="text" label="Source" name="source" id="source"
                                                 placeholder="e.g., Local Farm"
                                                 value="{{ old('source') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="text" label="Grade" name="grade" id="grade"
                                                 placeholder="e.g., A, B, Premium"
                                                 value="{{ old('grade') }}" />
                                    </div>

                                    {{-- Other Product Fields --}}
                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Weight per Unit (kg)" name="weight_per_unit"
                                                 id="weight_per_unit" placeholder="0.00"
                                                 value="{{ old('weight_per_unit') }}" step="0.01" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Buying Price" name="buying_price"
                                                 id="buying_price" placeholder="0"
                                                 value="{{ old('buying_price') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Selling Price" name="selling_price"
                                                 id="selling_price" placeholder="0"
                                                 value="{{ old('selling_price') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Quantity" name="quantity" id="quantity"
                                                 placeholder="0" value="{{ old('quantity') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Quantity Alert" name="quantity_alert"
                                                 id="quantity_alert" placeholder="0"
                                                 value="{{ old('quantity_alert') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="text" label="Storage Location" name="storage_location"
                                                 id="storage_location" placeholder="e.g., Freezer 1, Shelf 2"
                                                 value="{{ old('storage_location') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="date" label="Expiration Date" name="expiration_date"
                                                 id="expiration_date" value="{{ old('expiration_date') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <x-input type="number" label="Tax" name="tax" id="tax" placeholder="0"
                                                 value="{{ old('tax') }}" />
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="tax_type">{{ __('Tax Type') }}</label>
                                            <select name="tax_type" id="tax_type"
                                                    class="form-select @error('tax_type') is-invalid @enderror">
                                                <option value="">None</option>
                                                <option value="1" {{ old('tax_type') == 1 ? 'selected' : '' }}>Fixed</option>
                                                <option value="2" {{ old('tax_type') == 2 ? 'selected' : '' }}>Percentage</option>
                                            </select>
                                            @error('tax_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="notes">{{ __('Notes') }}</label>
                                            <textarea name="notes" id="notes"
                                                      class="form-control @error('notes') is-invalid @enderror"
                                                      rows="3" placeholder="Product notes...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    function previewImage() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('#image-preview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function (oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>
@endpush
