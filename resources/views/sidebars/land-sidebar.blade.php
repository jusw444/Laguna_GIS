<div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Analysis Options</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ $formAction ?? '' }}">
                            @csrf
                            <div class="mb-3">
                                <label for="landUseFilter" class="form-label">Land Use Type</label>
                                <select class="form-select" name="land_use" id="landUseFilter">
                                    <option value="all">All Types</option>
                                    <option value="residential" {{ request('land_use') == 'residential' ? 'selected' : '' }}>Residential</option>
                                    <option value="commercial" {{ request('land_use') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="agricultural" {{ request('land_use') == 'agricultural' ? 'selected' : '' }}>Agricultural</option>
                                    <option value="industrial" {{ request('land_use') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                                    <option value="institutional" {{ request('land_use') == 'institutional' ? 'selected' : '' }}>Institutional</option>
                                    <option value="recreational" {{ request('land_use') == 'recreational' ? 'selected' : '' }}>Recreational</option>
                                    <option value="conservation" {{ request('land_use') == 'conservation' ? 'selected' : '' }}>Conservation</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ownershipFilter" class="form-label">Ownership</label>
                                <select class="form-select" name="ownership" id="ownershipFilter">
                                    <option value="all">All Types</option>
                                    <option value="private" {{ request('ownership') == 'private' ? 'selected' : '' }}>Private</option>
                                    <option value="public" {{ request('ownership') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="government" {{ request('ownership') == 'government' ? 'selected' : '' }}>Government</option>
                                    <option value="communal" {{ request('ownership') == 'communal' ? 'selected' : '' }}>Communal</option>
                                </select>
                            </div>
                            <button class="btn btn-danger w-100" type="submit">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statistics</h5>
                    </div>
                    <div class="card-body" id="chartsContainer">
                        <div class="mb-3">
                            <h6>Land Use Distribution</h6>
                            <canvas id="landUseChart" height="200"></canvas>
                        </div>
                        <div class="mb-3">
                            <h6>Ownership Distribution</h6>
                            <canvas id="ownershipChart" height="200"></canvas>
                        </div>
                        <div>
                            <h6>Flood Risk Distribution</h6>
                            <canvas id="floodRiskChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>