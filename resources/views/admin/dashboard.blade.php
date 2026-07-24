<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#f4f6f9;
            color:#333;
            padding:30px;
        }

        h1{
            margin-bottom:20px;
            color:#2c3e50;
        }

        h2{
            margin:25px 0 15px;
            color:#34495e;
        }

        p{
            margin-bottom:15px;
            font-size:15px;
        }

        hr{
            margin:25px 0;
            border:none;
            border-top:1px solid #ddd;
        }

        form{
            margin-bottom:15px;
        }

        label{
            display:block;
            margin-top:12px;
            margin-bottom:5px;
            font-weight:bold;
        }

        input,
        select{
            width:100%;
            max-width:250px;
            padding:8px 10px;
            border:1px solid #ccc;
            border-radius:5px;
            font-size:14px;
        }

        button{
            margin-top:15px;
            padding:10px 18px;
            border:none;
            border-radius:5px;
            background:#3498db;
            color:white;
            cursor:pointer;
            transition:.3s;
        }

        button:hover{
            background:#2980b9;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:white;
            margin-top:15px;
        }

        table th{
            background:#2c3e50;
            color:white;
            padding:12px;
            text-align:left;
        }

        table td{
            padding:10px;
            border:1px solid #ddd;
        }

        table tr:nth-child(even){
            background:#f8f8f8;
        }

        table tr:hover{
            background:#eef6ff;
        }

        td input,
        td select{
            width:100%;
            max-width:none;
        }

        ul{
            background:#ffecec;
            color:#c0392b;
            padding:15px 20px;
            margin-bottom:20px;
            border-left:5px solid #e74c3c;
            border-radius:5px;
        }

        p[style*="green"]{
            background:#eafaf1;
            color:#27ae60 !important;
            padding:12px;
            border-left:5px solid #27ae60;
            border-radius:5px;
        }

        form[style*="display:inline"]{
            display:inline;
        }

        form[style*="display:inline"] button{
            background:#e74c3c;
            margin-left:5px;
        }

        form[style*="display:inline"] button:hover{
            background:#c0392b;
        }
    </style>

</head>
<body>

    @if (session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif
    @if ($errors->any())
        <ul style="color:red">
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
    <table border="1" cellpadding="6">
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
                    <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->floor_number }}</td>
                        <td>
                            <select name="room_type_id">
                                @foreach ($roomTypes as $type)
                                    <option value="{{ $type->id }}" @selected($room->room_type_id == $type->id)>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="price_per_night" value="{{ $room->price_per_night }}" style="width:90px">
                        </td>
                        <td>
                            <select name="status">
                                @foreach (['available','booked','reserved','maintenance','cleaning'] as $status)
                                    <option value="{{ $status }}" @selected($room->status == $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <button type="submit">Save</button>
                    </form>
                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" style="display:inline">
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

</body>
</html>