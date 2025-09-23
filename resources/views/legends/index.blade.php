<!-- resources/views/legends/index.blade.php -->
@extends('layouts.app')

@section('title', 'Legends')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-palette"></i> Map Legends</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLegendModal">
                <i class="fas fa-plus"></i> Add Legend
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            @foreach($legends as $legend)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $legend->title }}</h5>
                            <span class="badge bg-secondary">{{ $legend->type }}</span>
                        </div>
                        <div class="card-body">
                            <div class="legend-item mb-3">
                                <span class="legend-color" style="background-color: {{ $legend->color }}"></span>
                                <span class="legend-label">{{ $legend->title }}</span>
                            </div>
                            <p class="card-text">{{ $legend->description ?: 'No description provided.' }}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-outline-primary edit-legend" data-bs-toggle="modal" 
                                    data-bs-target="#editLegendModal" data-legend-id="{{ $legend->id }}"
                                    data-legend-title="{{ $legend->title }}" data-legend-color="{{ $legend->color }}"
                                    data-legend-type="{{ $legend->type }}" data-legend-description="{{ $legend->description }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('legends.destroy', $legend->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this legend?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Add Legend Modal -->
<div class="modal fade" id="addLegendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Legend</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('legends.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" class="form-control" id="color" name="color" value="#3388ff" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select type</option>
                            <option value="land_use">Land Use</option>
                            <option value="hazard">Hazard</option>
                            <option value="health">Health</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Legend</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Legend Modal -->
<div class="modal fade" id="editLegendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Legend</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLegendForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editColor" class="form-label">Color</label>
                        <input type="color" class="form-control" id="editColor" name="color" required>
                    </div>
                    <div class="mb-3">
                        <label for="editType" class="form-label">Type</label>
                        <select class="form-select" id="editType" name="type" required>
                            <option value="land_use">Land Use</option>
                            <option value="hazard">Hazard</option>
                            <option value="health">Health</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle edit legend button clicks
        document.querySelectorAll('.edit-legend').forEach(button => {
            button.addEventListener('click', function() {
                var legendId = this.getAttribute('data-legend-id');
                var title = this.getAttribute('data-legend-title');
                var color = this.getAttribute('data-legend-color');
                var type = this.getAttribute('data-legend-type');
                var description = this.getAttribute('data-legend-description');
                
                document.getElementById('editTitle').value = title;
                document.getElementById('editColor').value = color;
                document.getElementById('editType').value = type;
                document.getElementById('editDescription').value = description;
                
                // Update form action
                document.getElementById('editLegendForm').action = '/legends/' + legendId;
            });
        });
    });
</script>
@endpush