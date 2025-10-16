<div class="col-md-3">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Analysis Options</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ $formAction ?? '' }}">
                @csrf
                <div class="mb-3">
                    <label for="healthStatusFilter" class="form-label">Health Status</label>
                    <select class="form-select" name="health_status" id="healthStatusFilter">
                        <option value="all" {{ request('health_status') == 'all' ? 'selected' : '' }}>All Statuses
                        </option>
                        <option value="excellent" {{ request('health_status') == 'excellent' ? 'selected' : '' }}>
                            Excellent</option>
                        <option value="good" {{ request('health_status') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ request('health_status') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="poor" {{ request('health_status') == 'poor' ? 'selected' : '' }}>Poor</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="diseaseFilter" class="form-label">Disease Cases</label>
                    <select class="form-select" name="disease_level" id="diseaseFilter">
                        <option value="all" {{ request('disease_level') == 'all' ? 'selected' : '' }}>All Levels
                        </option>
                        <option value="high" {{ request('disease_level') == 'high' ? 'selected' : '' }}>High (50+
                            cases)</option>
                        <option value="medium" {{ request('disease_level') == 'medium' ? 'selected' : '' }}>Medium
                            (10-49 cases)</option>
                        <option value="low" {{ request('disease_level') == 'low' ? 'selected' : '' }}>Low (1-9 cases)
                        </option>
                        <option value="none" {{ request('disease_level') == 'none' ? 'selected' : '' }}>No Cases
                        </option>
                    </select>
                </div>
                <button class="btn btn-danger w-100" type="submit">Apply Filters</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Statistics</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <h6>Health Status Distribution</h6>
                <canvas id="healthStatusChart" height="200"></canvas>
            </div>
            <div class="mb-3">
                <h6>Disease Cases</h6>
                <canvas id="diseaseChart" height="200"></canvas>
            </div>
            <div class="mb-3">
                <h6>Clinics Available</h6>
                <p id="clinicsTotal" class="fw-bold text-primary">0</p>
            </div>
        </div>
    </div>
</div>
