<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, Helvetica, sans-serif; }
        body { background:#f4f6f9; color:#333; display:flex; height:100vh; overflow:hidden; }

        /* Sidebar */
        .sidebar {
            width:220px;
            background:#2c3e50;
            color:white;
            flex-shrink:0;
            padding:20px 0;
            height:100vh;
            display:flex;
            flex-direction:column;
            overflow-y:auto;
        }
        .sidebar h2 { padding:0 20px 20px; font-size:18px; border-bottom:1px solid #34495e; margin-bottom:10px; }
        .sidebar a {
            display:block;
            padding:12px 20px;
            color:#bdc3c7;
            text-decoration:none;
            cursor:pointer;
        }
        .sidebar a.active, .sidebar a:hover { background:#34495e; color:white; }

        /* Main content */
        .main { flex:1; padding:30px; height:100vh; overflow-y:auto; }
        h1 { margin-bottom:20px; color:#2c3e50; }
        h2 { margin:0 0 15px; color:#34495e; }
        p { margin-bottom:15px; font-size:15px; }
        hr { margin:25px 0; border:none; border-top:1px solid #ddd; }

        .panel { display:none; }
        .panel.active { display:block; }

        form { margin-bottom:15px; }
        label { display:block; margin-top:12px; margin-bottom:5px; font-weight:bold; }
        input, select {
            width:100%; max-width:250px; padding:8px 10px;
            border:1px solid #ccc; border-radius:5px; font-size:14px;
        }
        button {
            margin-top:15px; padding:10px 18px; border:none; border-radius:5px;
            background:#3498db; color:white; cursor:pointer; transition:.3s;
        }
        button:hover { background:#2980b9; }

        table { width:100%; border-collapse:collapse; background:white; margin-top:15px; }
        table th { background:#2c3e50; color:white; padding:12px; text-align:left; }
        table td { padding:10px; border:1px solid #ddd; }
        table tr:nth-child(even) { background:#f8f8f8; }
        table tr:hover { background:#eef6ff; }
        td input, td select { width:100%; max-width:none; }

        ul { background:#ffecec; color:#c0392b; padding:15px 20px; margin-bottom:20px; border-left:5px solid #e74c3c; border-radius:5px; }
        .success-msg { background:#eafaf1; color:#27ae60; padding:12px; border-left:5px solid #27ae60; border-radius:5px; margin-bottom:20px; }

        .delete-form { display:inline; }
        .delete-form button { background:#e74c3c; margin-left:5px; margin-top:0; }
        .delete-form button:hover { background:#c0392b; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a onclick="showPanel('rooms')" id="nav-rooms" class="active">Rooms</a>
        <a onclick="showPanel('staff')" id="nav-staff">Staff</a>

        <form action="{{ route('logout') }}" method="POST" style="margin-top:auto; padding:0 20px;">
            @csrf
            <button type="submit" style="width:100%; background:#e74c3c; margin-top:20px;">Logout</button>
        </form>
    </div>

    <div class="main">

        @if (session('success'))
            <p class="success-msg">{{ session('success') }}</p>
        @endif
        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <h1>Admin Dashboard</h1>
        <p>
            Total Rooms: {{ $roomStats['total'] }} |
            Available: {{ $roomStats['available'] }} |
            Booked: {{ $roomStats['booked'] }} |
            Maintenance: {{ $roomStats['maintenance'] }} |
            Staff: {{ $staffCount }}
        </p>
        <hr>

        <!-- ROOMS PANEL -->
        <div id="panel-rooms" class="panel active">
            <h2>Batch Create Rooms</h2>
            <form action="{{ route('rooms.batchStore') }}" method="POST">
                @csrf
                <label>Floor Number</label>
                <input type="number" name="floor_number">

                <label>Start Room No</label>
                <input type="number" name="start_room_no" required>

                <label>End Room No</label>
                <input type="number" name="end_room_no" required>

                <label>Room Type</label>
                <select name="room_type_id" required>
                    <option value="">-- select --</option>
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>

                <label>Price per Night</label>
                <input type="number" step="0.01" name="price_per_night" required>

                <button type="submit">Generate Rooms</button>
            </form>

            <hr>

            <h2>All Rooms</h2>
            <table>
                <thead>
                    <tr>
                        <th>Room No</th>
                        <th>Floor</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td>
                                <form action="{{ route('rooms.update', $room->id) }}" method="POST" id="room-form-{{ $room->id }}">
                                    @csrf
                                    @method('PUT')
                                </form>
                                {{ $room->room_number }}
                            </td>
                            <td>{{ $room->floor_number }}</td>
                            <td>
                                <select name="room_type_id" form="room-form-{{ $room->id }}">
                                    @foreach ($roomTypes as $type)
                                        <option value="{{ $type->id }}" @selected($room->room_type_id == $type->id)>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="price_per_night" value="{{ $room->price_per_night }}" form="room-form-{{ $room->id }}">
                            </td>
                            <td>
                                <select name="status" form="room-form-{{ $room->id }}">
                                    @foreach (['available','booked','reserved','maintenance','cleaning'] as $status)
                                        <option value="{{ $status }}" @selected($room->status == $status)>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button type="submit" form="room-form-{{ $room->id }}">Save</button>
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this room?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No rooms yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- STAFF PANEL -->
        <div id="panel-staff" class="panel">
            <h2>Create Staff Member</h2>
            <form action="{{ route('staff.store') }}" method="POST">
                @csrf
                <label>Full Name</label>
                <input type="text" name="fullname" required>

                <label>Username</label>
                <input type="text" name="username" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <button type="submit">Create Staff</button>
            </form>

            <hr>

            <h2>Staff Members</h2>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $member)
                        <tr>
                            <td>{{ $member->fullname }}</td>
                            <td>{{ $member->username }}</td>
                            <td>
                                <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Remove this staff member?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No staff yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function showPanel(name) {
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.getElementById('panel-' + name).classList.add('active');
            document.getElementById('nav-' + name).classList.add('active');
        }
    </script>

</body>
</html>