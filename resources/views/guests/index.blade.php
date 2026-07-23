<!DOCTYPE html>
<html>
<head>
    <title>All Guests - Elroi Guest House</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>👥 Registered Guests</h2>
            <div>
                <a href="{{ route('guests.create') }}" class="btn btn-success">+ Register New Guest</a>
                <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">Dashboard</a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Photo</th>
                            <th>Full Name</th>
                            <th>ID Type & No</th>
                            <th>Phone</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $guest)
                            <tr>
                                <td>
                                    @if($guest->id_photo)
                                        <img src="{{ $guest->id_photo }}" width="80" height="60" class="rounded border">
                                    @else
                                        <span class="text-muted">No Photo</span>
                                    @endif
                                </td>
                                <td><strong>{{ $guest->full_name }}</strong></td>
                                <td>{{ $guest->id_type }}: {{ $guest->id_number }}</td>
                                <td>{{ $guest->phone_no }}</td>
                                <td><span class="badge bg-success">{{ $guest->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No guests registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>