<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Dashboard</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, Helvetica, sans-serif; }
        body { background:#f4f6f9; color:#333; display:flex; height:100vh; overflow:hidden; }

        .sidebar {
            width:220px; background:#2c3e50; color:white; flex-shrink:0;
            padding:20px 0; height:100vh; display:flex; flex-direction:column; overflow-y:auto;
        }
        .sidebar h2 { padding:0 20px 20px; font-size:18px; border-bottom:1px solid #34495e; margin-bottom:10px; }
        .sidebar a { display:block; padding:12px 20px; color:#bdc3c7; text-decoration:none; cursor:pointer; }
        .sidebar a.active, .sidebar a:hover { background:#34495e; color:white; }

        .main { flex:1; padding:30px; height:100vh; overflow-y:auto; }
        h1 { margin-bottom:20px; color:#2c3e50; }
        h2 { margin:0 0 15px; color:#34495e; }
        h3 { margin:15px 0 10px; color:#34495e; }
        p { margin-bottom:15px; font-size:15px; }
        hr { margin:25px 0; border:none; border-top:1px solid #ddd; }

        .panel { display:none; }
        .panel.active { display:block; }

        form { margin-bottom:15px; }
        label { display:block; margin-top:12px; margin-bottom:5px; font-weight:bold; }
        input, select {
            width:100%; max-width:320px; padding:8px 10px;
            border:1px solid #ccc; border-radius:5px; font-size:14px;
        }
        button {
            margin-top:15px; padding:10px 18px; border:none; border-radius:5px;
            background:#3498db; color:white; cursor:pointer; transition:.3s;
        }
        button:hover { background:#2980b9; }
        button:disabled { background:#95a5a6; cursor:not-allowed; }

        ul.errors { background:#ffecec; color:#c0392b; padding:15px 20px; margin-bottom:20px; border-left:5px solid #e74c3c; border-radius:5px; }
        .success-msg { background:#eafaf1; color:#27ae60; padding:12px; border-left:5px solid #27ae60; border-radius:5px; margin-bottom:20px; }
        .soon { color:#888; font-style:italic; }

        .card { background:white; padding:20px; border-radius:8px; max-width:600px; box-shadow:0 1px 3px rgba(0,0,0,.08); }

        .cam-box { display:flex; gap:15px; align-items:flex-start; flex-wrap:wrap; }
        video, canvas, #idPreview { width:240px; height:180px; background:#000; border-radius:6px; object-fit:cover; }
        canvas { display:none; }
        #idPreview { display:none; border:2px solid #27ae60; }

        .room-result { padding:10px; border:1px solid #ddd; border-radius:5px; margin-top:8px; background:#f8f9fb; }
        .room-result.none { color:#c0392b; }
        .price-line { font-weight:bold; color:#2c3e50; margin-top:10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Staff Panel</h2>
        <a onclick="showPanel('dashboard')" id="nav-dashboard" class="active">Dashboard</a>
        <a onclick="showPanel('checkin')" id="nav-checkin">Check In</a>
        <a onclick="showPanel('checkout')" id="nav-checkout">Check Out</a>
        <a onclick="showPanel('reservation')" id="nav-reservation">Reservation</a>
        <a onclick="showPanel('reservationlist')" id="nav-reservationlist">Reservation List</a>

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
            <ul class="errors">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <!-- DASHBOARD PANEL -->
        <div id="panel-dashboard" class="panel active">
            <h1>Staff Dashboard</h1>
            <p>Welcome. Use the menu on the left to check guests in, check them out, or manage reservations.</p>
        </div>

        <!-- CHECK IN PANEL -->
        <div id="panel-checkin" class="panel">
            <h1>Check In Guest</h1>

            <form action="{{ route('staff.checkin.store') }}" method="POST" id="checkinForm" class="card">
                @csrf

                <h3>Guest Details</h3>
                <label>Full Name</label>
                <input type="text" name="fullname" required>

                <label>Phone Number</label>
                <input type="text" name="phone_no" required>

                <label>ID Type</label>
                <select name="id_type" required>
                    <option value="national_id">National ID</option>
                    <option value="kebele_id">Kebele ID</option>
                    <option value="passport">Passport</option>
                    <option value="driving_license">Driving License</option>
                </select>

                <h3>ID Photo</h3>
                <div class="cam-box">
                    <video id="camVideo" autoplay playsinline></video>
                    <canvas id="camCanvas"></canvas>
                    <img id="idPreview" alt="Captured ID">
                </div>
                <button type="button" id="startCamBtn">Start Camera</button>
                <button type="button" id="captureBtn" disabled>Capture Photo</button>
                <button type="button" id="retakeBtn" style="display:none;">Retake</button>
                <input type="hidden" name="id_photo" id="id_photo">

                <h3>Stay Details</h3>
                <label>Check-in Date</label>
                <input type="date" name="check_in_date" id="check_in_date" required>

                <label>Check-out Date</label>
                <input type="date" name="check_out_date" id="check_out_date" required>

                <label>Room Type</label>
                <select id="room_type_id" required>
                    <option value="">-- select room type --</option>
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                <button type="button" id="findRoomsBtn">Find Available Rooms</button>

                <div id="roomResults" class="room-result" style="display:none;"></div>

                <label>Selected Room</label>
                <select name="room_id" id="room_id" required>
                    <option value="">-- find rooms first --</option>
                </select>

                <div class="price-line" id="priceLine" style="display:none;"></div>

                <h3>Payment</h3>
                <label>Payment Type</label>
                <select name="payment_type" required>
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="pos">POS</option>
                </select>

                <label>Payment Way</label>
                <select name="payment_way" id="payment_way" required>
                    <option value="full">Full Payment</option>
                    <option value="partial">Partial Payment</option>
                </select>

                <label>Amount Paid</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" required>

                <button type="submit">Check In</button>
            </form>
        </div>

        <!-- COMING SOON PANELS -->
        <div id="panel-checkout" class="panel">
            <h1>Check Out</h1>
            <p class="soon">Coming soon.</p>
        </div>

        <div id="panel-reservation" class="panel">
            <h1>Reservation</h1>
            <p class="soon">Coming soon.</p>
        </div>

        <div id="panel-reservationlist" class="panel">
            <h1>Reservation List</h1>
            <p class="soon">Coming soon.</p>
        </div>

    </div>

    <script>
        // ---- Sidebar panel switching ----
        function showPanel(name) {
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.getElementById('panel-' + name).classList.add('active');
            document.getElementById('nav-' + name).classList.add('active');
        }

        // ---- Webcam capture ----
        const video = document.getElementById('camVideo');
        const canvas = document.getElementById('camCanvas');
        const idPreview = document.getElementById('idPreview');
        const startCamBtn = document.getElementById('startCamBtn');
        const captureBtn = document.getElementById('captureBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        const idPhotoInput = document.getElementById('id_photo');
        let camStream = null;

        startCamBtn.addEventListener('click', async () => {
            try {
                camStream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = camStream;
                video.style.display = 'block';
                captureBtn.disabled = false;
                startCamBtn.disabled = true;
            } catch (err) {
                alert('Could not access camera: ' + err.message);
            }
        });

        captureBtn.addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            idPhotoInput.value = dataUrl;

            idPreview.src = dataUrl;
            idPreview.style.display = 'block';
            video.style.display = 'none';

            captureBtn.style.display = 'none';
            retakeBtn.style.display = 'inline-block';

            if (camStream) camStream.getTracks().forEach(t => t.stop());
        });

        retakeBtn.addEventListener('click', async () => {
            idPreview.style.display = 'none';
            idPhotoInput.value = '';
            captureBtn.style.display = 'inline-block';
            retakeBtn.style.display = 'none';
            startCamBtn.disabled = false;
            startCamBtn.click();
        });

        // ---- Available rooms lookup ----
        const findRoomsBtn = document.getElementById('findRoomsBtn');
        const roomTypeSelect = document.getElementById('room_type_id');
        const roomResults = document.getElementById('roomResults');
        const roomSelect = document.getElementById('room_id');
        const priceLine = document.getElementById('priceLine');
        let roomPrices = {};

        findRoomsBtn.addEventListener('click', async () => {
            const roomTypeId = roomTypeSelect.value;
            if (!roomTypeId) {
                alert('Select a room type first.');
                return;
            }

            roomResults.style.display = 'block';
            roomResults.textContent = 'Searching...';
            roomResults.classList.remove('none');

            try {
                const res = await fetch(`{{ route('staff.rooms.available') }}?room_type_id=${roomTypeId}`);
                const rooms = await res.json();

                roomSelect.innerHTML = '';
                roomPrices = {};

                if (rooms.length === 0) {
                    roomResults.textContent = 'No available rooms of this type right now.';
                    roomResults.classList.add('none');
                    roomSelect.innerHTML = '<option value="">-- none available --</option>';
                    priceLine.style.display = 'none';
                    return;
                }

                roomResults.textContent = `${rooms.length} room(s) available.`;

                roomSelect.innerHTML = '<option value="">-- select a room --</option>';
                rooms.forEach(r => {
                    roomPrices[r.id] = parseFloat(r.price_per_night);
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = `Room ${r.room_number} — ${r.price_per_night} ETB/night`;
                    roomSelect.appendChild(opt);
                });
            } catch (err) {
                roomResults.textContent = 'Error fetching rooms.';
                roomResults.classList.add('none');
            }
        });

        // ---- Price calculation ----
        function updatePrice() {
            const roomId = roomSelect.value;
            const checkIn = document.getElementById('check_in_date').value;
            const checkOut = document.getElementById('check_out_date').value;

            if (!roomId || !checkIn || !checkOut || !roomPrices[roomId]) {
                priceLine.style.display = 'none';
                return;
            }

            const d1 = new Date(checkIn);
            const d2 = new Date(checkOut);
            let nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            if (nights < 1) nights = 1;

            const total = nights * roomPrices[roomId];
            priceLine.textContent = `${nights} night(s) x ${roomPrices[roomId]} ETB = ${total.toFixed(2)} ETB total`;
            priceLine.style.display = 'block';

            if (document.getElementById('payment_way').value === 'full') {
                document.getElementById('amount_paid').value = total.toFixed(2);
            }
        }

        roomSelect.addEventListener('change', updatePrice);
        document.getElementById('check_in_date').addEventListener('change', updatePrice);
        document.getElementById('check_out_date').addEventListener('change', updatePrice);

        document.getElementById('payment_way').addEventListener('change', function () {
            if (this.value === 'full') updatePrice();
        });
    </script>

</body>
</html>