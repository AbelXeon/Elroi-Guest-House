<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard - Elroi Guest House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary px-3">
        <span class="navbar-brand font-weight-bold">Elroi Reception / Staff Dashboard</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white">Welcome, {{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container my-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Quick Action Buttons -->
        <div class="d-flex gap-3 mb-4">
            <a href="{{ route('guests.create') }}" class="btn btn-success btn-lg">📷 Register New Guest (Webcam)</a>
            <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-lg">🛏️ New Reservation / Check-In</a>
            <a href="{{ route('guests.index') }}" class="btn btn-secondary btn-lg">👥 View All Guests</a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-info shadow-sm">
                    <div class="card-body">
                        <h5>Total Registered Guests</h5>
                        <h2>{{ $totalGuests }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success shadow-sm">
                    <div class="card-body">
                        <h5>Available Rooms</h5>
                        <h2>{{ $availableRooms }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning shadow-sm">
                    <div class="card-body">
                        <h5>Currently Checked-In</h5>
                        <h2>{{ $activeReservations->count() }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Reservations Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Current Checked-In Guests</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Guest Name</th>
                            <th>Room</th>
                            <th>Check-In Date</th>
                            <th>Check-Out Date</th>
                            <th>Total Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeReservations as $res)
                            <tr>
                                <td>{{ $res->guest->full_name }}</td>
                                <td>Room {{ $res->room->room_number }} ({{ $res->room->room_type }})</td>
                                <td>{{ $res->check_in_at }}</td>
                                <td>{{ $res->check_out_at }}</td>
                                <td>${{ $res->total_price }}</td>
                                <td>
                                    <form action="{{ route('reservations.checkout', $res->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Check out this guest?')">Check Out</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No active checked-in guests right now.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>