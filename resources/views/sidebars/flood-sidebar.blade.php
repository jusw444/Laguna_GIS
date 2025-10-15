                <div class="col-md-3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Analysis Options</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ $formAction ?? '' }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="floodRiskFilter" class="form-label">Flood Risk Level</label>
                                    <select class="form-select" name="flood_risk" id="floodRiskFilter">
                                        <option value="all" {{ request('flood_risk') == 'all' ? 'selected' : '' }}>
                                            All Levels</option>
                                        <option value="high" {{ request('flood_risk') == 'high' ? 'selected' : '' }}>
                                            High Risk</option>
                                        <option value="medium"
                                            {{ request('flood_risk') == 'medium' ? 'selected' : '' }}>Medium Risk
                                        </option>
                                        <option value="low" {{ request('flood_risk') == 'low' ? 'selected' : '' }}>
                                            Low Risk</option>
                                        <option value="none" {{ request('flood_risk') == 'none' ? 'selected' : '' }}>
                                            No Risk</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="landUseFilter" class="form-label">Land Use</label>
                                    <select class="form-select" name="land_use" id="landUseFilter">
                                        <option value="all" {{ request('land_use') == 'all' ? 'selected' : '' }}>All
                                            Types</option>
                                        <option value="residential"
                                            {{ request('land_use') == 'residential' ? 'selected' : '' }}>Residential
                                        </option>
                                        <option value="commercial"
                                            {{ request('land_use') == 'commercial' ? 'selected' : '' }}>Commercial
                                        </option>
                                        <option value="agricultural"
                                            {{ request('land_use') == 'agricultural' ? 'selected' : '' }}>Agricultural
                                        </option>
                                        <option value="industrial"
                                            {{ request('land_use') == 'industrial' ? 'selected' : '' }}>Industrial
                                        </option>
                                    </select>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">Apply Filters</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6>Flood Risk Distribution</h6>
                                <canvas id="floodRiskChart" height="200"></canvas>
                            </div>
                            <div class="mb-3">
                                <h6>Areas by Land Use</h6>
                                <canvas id="landUseChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
