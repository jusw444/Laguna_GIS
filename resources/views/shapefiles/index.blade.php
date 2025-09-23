@extends('layouts.app')

@section('title', 'Shapefiles')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-layer-group"></i> Shapefiles</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadShapefileModal">
                <i class="fas fa-upload"></i> Upload Shapefile
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            @forelse($shapefiles as $shapefile)
                <div class="col-md-4 mb-4">
                    <div class="card shapefile-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">{{ $shapefile->name }}</h5>
                            <span class="badge bg-secondary">{{ $shapefile->type }}</span>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $shapefile->description ?: 'No description provided.' }}</p>
                            <p class="text-muted">
                                <small>Uploaded by: {{ $shapefile->user->name }}</small><br>
                                <small>Created: {{ $shapefile->created_at->format('M d, Y') }}</small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('shapefiles.show', $shapefile->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <form action="{{ route('shapefiles.destroy', $shapefile->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this shapefile?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No shapefiles uploaded yet. 
                        <a href="#" data-bs-toggle="modal" data-bs-target="#uploadShapefileModal">Upload your first shapefile</a>.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Upload Shapefile Modal -->
<div class="modal fade" id="uploadShapefileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Shapefile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('shapefiles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Select type</option>
                            <option value="point">Point</option>
                            <option value="line">Line</option>
                            <option value="polygon">Polygon</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="shapefile" class="form-label">Shapefile (ZIP)</label>
                        <input type="file" class="form-control" id="shapefile" name="shapefile" accept=".zip">
                        <div class="form-text">Upload a zipped shapefile containing .shp, .shx, .dbf, and .prj files</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection