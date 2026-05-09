<div class="transportation-section">
    <div class="row mb-5">
        <div class="col-12 text-center mb-4">
            <h2 class="section-title">@lang('Premium Transportation Services')</h2>
            <p class="text-muted">@lang('Safe, reliable, and comfortable transfers for all your travel needs.')</p>
        </div>
        <div class="col-md-4">
            <div class="service-card p-4 rounded-4 border bg-white shadow-sm h-100 text-center">
                <div class="icon-box mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px; font-size: 2rem; color: hsl(var(--base));">
                    <i class="las la-plane-arrival"></i>
                </div>
                <h4 class="fw-bold">@lang('Airport Transfers')</h4>
                <p class="small text-muted">@lang('Professional pickup and drop-off services to and from any airport.')</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card p-4 rounded-4 border bg-white shadow-sm h-100 text-center">
                <div class="icon-box mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px; font-size: 2rem; color: hsl(var(--base));">
                    <i class="las la-city"></i>
                </div>
                <h4 class="fw-bold">@lang('City-to-City')</h4>
                <p class="small text-muted">@lang('Comfortable long-distance travel between different cities with private drivers.')</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="service-card p-4 rounded-4 border bg-white shadow-sm h-100 text-center">
                <div class="icon-box mb-3 mx-auto d-flex align-items-center justify-content-center rounded-circle bg-light" style="width: 80px; height: 80px; font-size: 2rem; color: hsl(var(--base));">
                    <i class="las la-car-side"></i>
                </div>
                <h4 class="fw-bold">@lang('Private Chauffeur')</h4>
                <p class="small text-muted">@lang('Dedicated luxury cars with drivers at your service for full or half days.')</p>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="booking-form-wrapper bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                <h3 class="fw-bold mb-4 text-center">@lang('Transportation Booking Request')</h3>
                <form action="{{ route('service.booking.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="transportation">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">@lang('Pickup Location')</label>
                                <input type="text" name="origin" class="form-control rounded-pill px-4" placeholder="@lang('Where should we pick you up?')" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">@lang('Drop-off Location')</label>
                                <input type="text" name="destination" class="form-control rounded-pill px-4" placeholder="@lang('Where is your destination?')" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">@lang('Date')</label>
                                <input type="date" name="service_date" class="form-control rounded-pill px-4" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">@lang('Time')</label>
                                <input type="time" name="service_time" class="form-control rounded-pill px-4" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-bold">@lang('Vehicle Type')</label>
                                <select name="vehicle_type" class="form-select rounded-pill px-4">
                                    <option value="sedan">@lang('Standard Sedan')</option>
                                    <option value="luxury">@lang('Luxury Sedan')</option>
                                    <option value="suv">@lang('SUV / 4x4')</option>
                                    <option value="van">@lang('Minivan (7+ Seater)')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-bold">@lang('Special Requirements')</label>
                                <textarea name="notes" class="form-control rounded-4 px-4 py-3" rows="4" placeholder="@lang('Flight number (for airport pickup), baby seat, extra luggage, etc...')"></textarea>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn--base btn-lg rounded-pill px-5">@lang('Submit Request')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
