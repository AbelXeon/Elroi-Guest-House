<!DOCTYPE html>
<html>
<head>
    <title>New Reservation - Elroi Guest House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🛏️ Check-In Guest</h2>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Cancel</a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Select Guest</label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">-- Choose Guest --</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}">{{ $guest->full_name }} ({{ $guest->phone_no }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Room</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- Choose Available Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">Room {{ $room->room_number }} - {{ $room->room_type }} (${{ $room->price_per_night }}/night)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Check-In Date & Time</label>
                        <input type="datetime-local" name="check_in_at" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Check-Out Date & Time</label>
                        <input type="datetime-local" name="check_out_at" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Price ($)</label>
                        <input type="number" step="0.01" name="total_price" class="form-control" required placeholder="0.00">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">Confirm Check-In</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>