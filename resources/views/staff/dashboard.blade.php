<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Dashboard - Elroi Guest House</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, Helvetica, sans-serif; }
        body { background:#f4f6f9; color:#333; display:flex; height:100vh; overflow:hidden; }

        /* Sidebar */
        .sidebar {
            width:220px; background:#2c3e50; color:white; flex-shrink:0;
            padding:20px 0; height:100vh; display:flex; flex-direction:column; overflow-y:auto;
        }
        .sidebar h2 { padding:0 20px 20px; font-size:18px; border-bottom:1px solid #34495e; margin-bottom:10px; }
        .sidebar a { display:block; padding:12px 20px; color:#bdc3c7; text-decoration:none; cursor:pointer; }
        .sidebar a.active, .sidebar a:hover { background:#34495e; color:white; }

        /* Main Area */
        .main { flex:1; padding:30px; height:100vh; overflow-y:auto; }
        h1 { margin-bottom:20px; color:#2c3e50; }
        h3 { margin:15px 0 10px; color:#34495e; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        
        .panel { display:none; }
        .panel.active { display:block; }

        /* Forms & UI */
        .card { background:white; padding:20px; border-radius:8px; max-width:700px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }
        label { display:block; margin-top:12px; margin-bottom:5px; font-weight:bold; font-size: 14px; }
        input, select {
            width:100%; max-width:350px; padding:10px;
            border:1px solid #ccc; border-radius:5px; font-size:14px;
        }
        button {
            margin-top:15px; padding:10px 18px; border:none; border-radius:5px;
            background:#3498db; color:white; cursor:pointer; transition:.3s; font-weight: bold;
        }
        button:hover { background:#2980b9; }
        button.danger { background:#e74c3c; }
        button.danger:hover { background:#c0392b; }
        button.success { background:#27ae60; }

        /* Messages */
        .success-msg { background:#eafaf1; color:#27ae60; padding:12px; border-left:5px solid #27ae60; border-radius:5px; margin-bottom:20px; }
        .errors { background:#ffecec; color:#c0392b; padding:15px 20px; margin-bottom:20px; border-left:5px solid #e74c3c; border-radius:5px; list-style: none; }

        /* Webcam Area */
        .cam-box { display:flex; gap:15px; align-items:flex-start; flex-wrap:wrap; margin-bottom:10px; }
        video, canvas, .preview-img { width:240px; height:180px; background:#000; border-radius:6px; object-fit:cover; border: 1px solid #ddd; }
        canvas { display:none; }

        /* Search Results */
        .search-container { position: relative; width: 100%; max-width: 350px; }
        .search-results { 
            position: absolute; background: white; border: 1px solid #ddd; width: 100%; 
            z-index: 100; border-radius: 0 0 5px 5px; max-height: 200px; overflow-y: auto; display:none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .search-item { padding: 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 14px; }
        .search-item:hover { background: #f4f6f9; color: #3498db; }

        /* Price & Balance Display */
        .price-line { font-weight:bold; color:#2c3e50; margin-top:10px; padding: 12px; background: #eef2f7; border-radius: 5px; border-left: 4px solid #3498db; }
        .bal-box { font-weight:bold; margin-top:10px; padding: 12px; border-radius: 5px; border-left: 4px solid; }
        .bal-red { background: #fdf2f2; color: #c0392b; border-color: #e74c3c; }
        .bal-green { background: #f2fdf5; color: #27ae60; border-color: #27ae60; }

        /* Checkout Guest Profile */
        .guest-profile { display: flex; gap: 20px; margin-top: 15px; align-items: center; }
        .guest-info p { margin-bottom: 5px; font-size: 14px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Elroi Staff</h2>
        <a onclick="showPanel('dashboard')" id="nav-dashboard" class="active">Dashboard</a>
        <a onclick="showPanel('checkin')" id="nav-checkin">Check In</a>
        <a onclick="showPanel('checkout')" id="nav-checkout">Check Out</a>
        <a onclick="showPanel('reservation')" id="nav-reservation">Reservation</a>
        <a onclick="showPanel('reservationlist')" id="nav-reservationlist">Reservation List</a>

        <form action="{{ route('logout') }}" method="POST" style="margin-top:auto; padding:0 20px;">
            @csrf
            <button type="submit" style="width:100%; background:#e74c3c;">Logout</button>
        </form>
    </div>

    <div class="main">
        @if (session('success')) <p class="success-msg">{{ session('success') }}</p> @endif
        @if ($errors->any())
            <ul class="errors">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        @endif

        <!-- DASHBOARD -->
        <div id="panel-dashboard" class="panel active">
            <h1>Staff Dashboard</h1>
            <p>Welcome. Select an action from the sidebar to manage guests.</p>
        </div>

        <!-- CHECK IN -->
        <div id="panel-checkin" class="panel">
            <h1>Direct Check In</h1>
            <form action="{{ route('staff.checkin.store') }}" method="POST" class="card">
                @csrf
                <h3>Guest Identity</h3>
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

                <label>Capture ID Photo</label>
                <div class="cam-box">
                    <video id="ci-video" autoplay playsinline></video>
                    <img id="ci-preview" class="preview-img" style="display:none;">
                </div>
                <button type="button" onclick="startCamera('ci-video', 'ci-cap-btn')">Start Camera</button>
                <button type="button" id="ci-cap-btn" onclick="takeSnapshot('ci-video', 'ci-preview', 'ci-photo-input')" disabled>Capture</button>
                <input type="hidden" name="id_photo" id="ci-photo-input">

                <h3>Room & Stay</h3>
                <label>Dates</label>
                <div style="display:flex; gap:10px;">
                    <input type="date" name="check_in_date" id="ci-in" required onchange="calculateCheckinPrice()">
                    <input type="date" name="check_out_date" id="ci-out" required onchange="calculateCheckinPrice()">
                </div>

                <label>Room Type</label>
                <select id="ci-type">
                    <option value="">-- select --</option>
                    @foreach ($roomTypes as $type) <option value="{{ $type->id }}">{{ $type->name }}</option> @endforeach
                </select>
                <button type="button" onclick="findRooms('ci-type', 'ci-room-select')">Find Available Rooms</button>

                <label>Select Room</label>
                <select name="room_id" id="ci-room-select" required onchange="calculateCheckinPrice()"></select>
                <div id="ci-price-line" class="price-line" style="display:none;"></div>

                <h3>Payment</h3>
                <label>Payment Method</label>
                <select name="payment_type">
                    <option value="cash">Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="pos">POS</option>
                </select>
                <label>Payment Mode</label>
                <select name="payment_way" id="ci-pay-way" onchange="calculateCheckinPrice()">
                    <option value="full">Full Payment</option>
                    <option value="partial">Partial Payment</option>
                </select>
                <label>Amount Paid Now</label>
                <input type="number" step="0.01" name="amount_paid" id="ci-paid" required oninput="calculateCheckinPrice()">
                <div id="ci-bal-line" class="bal-box" style="display:none;"></div>

                <button type="submit" class="success">Complete Check In</button>
            </form>
        </div>

        <!-- CHECK OUT -->
        <div id="panel-checkout" class="panel">
            <h1>Guest Check Out</h1>
            <div class="card">
                <label>Search Guest Name or Room</label>
                <div class="search-container">
                    <input type="text" id="co-search" placeholder="Search name or room..." oninput="searchCheckout()">
                    <div id="co-results" class="search-results"></div>
                </div>

                <div id="co-details" style="display:none; margin-top:20px;">
                    <hr>
                    <div class="guest-profile">
                        <img id="co-img" src="" class="preview-img">
                        <div class="guest-info">
                            <p><strong>Name:</strong> <span id="co-name"></span></p>
                            <p><strong>Room:</strong> <span id="co-room"></span></p>
                            <p><strong>Dates:</strong> <span id="co-dates"></span></p>
                        </div>
                    </div>
                    <div id="co-overstay" class="bal-box bal-red" style="display:none;"></div>
                    <div id="co-balance" class="bal-box"></div>

                    <form action="{{ route('staff.checkout.process') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reservation_id" id="co-res-id">
                        <button type="submit" class="danger">Confirm Checkout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RESERVATION (Phone Booking) -->
        <div id="panel-reservation" class="panel">
            <h1>Create Phone Reservation</h1>
            <form action="{{ route('staff.reservation.store') }}" method="POST" class="card">
                @csrf
                <h3>Contact Info</h3>
                <label>Guest Full Name</label>
                <input type="text" name="fullname" required>
                <label>Phone Number</label>
                <input type="text" name="phone_no" required>

                <h3>Stay Details</h3>
                <label>Dates</label>
                <div style="display:flex; gap:10px;">
                    <input type="date" name="check_in_date" id="res-in" required onchange="calculateReservationPrice()">
                    <input type="date" name="check_out_date" id="res-out" required onchange="calculateReservationPrice()">
                </div>

                <label>Room Type</label>
                <select id="res-type">
                    <option value="">-- select --</option>
                    @foreach ($roomTypes as $type) <option value="{{ $type->id }}">{{ $type->name }}</option> @endforeach
                </select>
                <button type="button" onclick="findRooms('res-type', 'res-room-select')">Find Rooms</button>

                <label>Select Room</label>
                <select name="room_id" id="res-room-select" required onchange="calculateReservationPrice()"></select>
                <div id="res-price-line" class="price-line" style="display:none;"></div>

                <label>Deposit / Down Payment</label>
                <input type="number" step="0.01" name="amount_paid" placeholder="Enter deposit amount" required>

                <button type="submit">Save Reservation</button>
            </form>
        </div>

        <!-- RESERVATION LIST (Convert to Check-in) -->
        <div id="panel-reservationlist" class="panel">
            <h1>Reservation Arrivals</h1>
            <div class="card">
                <label>Search Reserved Guest (Name or Phone)</label>
                <div class="search-container">
                    <input type="text" id="rl-search" placeholder="Search Name or Phone..." oninput="searchReservation()">
                    <div id="rl-results" class="search-results"></div>
                </div>

                <div id="rl-box" style="display:none; margin-top:20px;">
                    <hr>
                    <h3 id="rl-title"></h3>
                    <p>Room: <span id="rl-room"></span> | Paid Deposit: <span id="rl-deposit"></span> ETB</p>
                    
                    <form action="{{ route('staff.reservation.complete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="reservation_id" id="rl-res-id">
                        <label>ID Type</label>
                        <select name="id_type" required>
                            <option value="national_id">National ID</option>
                            <option value="passport">Passport</option>
                            <option value="kebele_id">Kebele ID</option>
                        </select>
                        <label>Capture Photo Now</label>
                        <div class="cam-box">
                            <video id="rl-video" autoplay playsinline></video>
                            <img id="rl-preview" class="preview-img" style="display:none;">
                        </div>
                        <button type="button" onclick="startCamera('rl-video', 'rl-cap-btn')">Start Camera</button>
                        <button type="button" id="rl-cap-btn" onclick="takeSnapshot('rl-video', 'rl-preview', 'rl-photo-input')" disabled>Capture</button>
                        <input type="hidden" name="id_photo" id="rl-photo-input" required>
                        
                        <button type="submit" class="success">Finish Full Check-In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden canvas for snapshots -->
    <canvas id="main-canvas"></canvas>

    <script>
        // NAVIGATION
        function showPanel(name) {
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.getElementById('panel-' + name).classList.add('active');
            document.getElementById('nav-' + name).classList.add('active');
        }

        // CAMERA HELPERS
        let activeStream = null;
        async function startCamera(videoId, btnId) {
            try {
                if(activeStream) activeStream.getTracks().forEach(t => t.stop());
                activeStream = await navigator.mediaDevices.getUserMedia({ video: true });
                const video = document.getElementById(videoId);
                video.srcObject = activeStream;
                video.style.display = 'block';
                document.getElementById(btnId).disabled = false;
            } catch (e) { alert("Camera Error: " + e.message); }
        }

        function takeSnapshot(videoId, previewId, inputId) {
            const video = document.getElementById(videoId);
            const canvas = document.getElementById('main-canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const data = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById(inputId).value = data;
            const preview = document.getElementById(previewId);
            preview.src = data;
            preview.style.display = 'block';
            video.style.display = 'none';
        }

        // ROOM LOOKUP
        let roomPrices = {};
        async function findRooms(typeSelectId, roomSelectId) {
            const typeId = document.getElementById(typeSelectId).value;
            if(!typeId) return alert("Select room type");
            const res = await fetch(`{{ route('staff.rooms.available') }}?room_type_id=${typeId}`);
            const rooms = await res.json();
            const sel = document.getElementById(roomSelectId);
            sel.innerHTML = '<option value="">-- select room --</option>';
            rooms.forEach(r => {
                roomPrices[r.id] = parseFloat(r.price_per_night);
                sel.innerHTML += `<option value="${r.id}">Room ${r.room_number} - ${r.price_per_night} ETB</option>`;
            });
        }

        // CHECK IN PRICE CALC
        function calculateCheckinPrice() {
            const rid = document.getElementById('ci-room-select').value;
            const d1 = new Date(document.getElementById('ci-in').value);
            const d2 = new Date(document.getElementById('ci-out').value);
            const payWay = document.getElementById('ci-pay-way').value;
            const paidInput = document.getElementById('ci-paid');

            if(!rid || isNaN(d1) || isNaN(d2)) return;

            let nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            if(nights < 1) nights = 1;
            const total = nights * roomPrices[rid];

            const pLine = document.getElementById('ci-price-line');
            pLine.style.display = 'block';
            pLine.innerHTML = `Stay Summary: ${nights} nights x ${roomPrices[rid]} ETB = <strong>${total.toFixed(2)} ETB Total</strong>`;

            if(payWay === 'full') {
                paidInput.value = total.toFixed(2);
                paidInput.readOnly = true;
            } else {
                paidInput.readOnly = false;
            }

            const paid = parseFloat(paidInput.value) || 0;
            const bal = total - paid;
            const bLine = document.getElementById('ci-bal-line');
            bLine.style.display = 'block';
            bLine.className = bal <= 0 ? 'bal-box bal-green' : 'bal-box bal-red';
            bLine.textContent = bal <= 0 ? "Balance: Paid in Full" : `Balance Due: ${bal.toFixed(2)} ETB`;
        }

        // RESERVATION PRICE CALC
        function calculateReservationPrice() {
            const rid = document.getElementById('res-room-select').value;
            const d1 = new Date(document.getElementById('res-in').value);
            const d2 = new Date(document.getElementById('res-out').value);

            if(!rid || isNaN(d1) || isNaN(d2)) return;

            let nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            if(nights < 1) nights = 1;
            const total = nights * roomPrices[rid];

            const pLine = document.getElementById('res-price-line');
            pLine.style.display = 'block';
            pLine.innerHTML = `Reservation Summary: ${nights} nights x ${roomPrices[rid]} ETB = <strong>${total.toFixed(2)} ETB Total</strong>`;
        }

        // CHECK OUT SEARCH
        async function searchCheckout() {
            const q = document.getElementById('co-search').value;
            if(q.length < 2) return document.getElementById('co-results').style.display='none';
            const res = await fetch(`{{ route('staff.checkout.search') }}?query=${q}`);
            const data = await res.json();
            const results = document.getElementById('co-results');
            results.innerHTML = '';
            if(data.length > 0) {
                results.style.display = 'block';
                data.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    div.textContent = `${r.guest.fullname} (Room ${r.room.room_number})`;
                    div.onclick = () => {
                        results.style.display = 'none';
                        document.getElementById('co-details').style.display = 'block';
                        document.getElementById('co-res-id').value = r.id;
                        document.getElementById('co-name').textContent = r.guest.fullname;
                        document.getElementById('co-room').textContent = r.room.room_number;
                        document.getElementById('co-dates').textContent = `${r.check_in_date.split('T')[0]} to ${r.check_out_date.split('T')[0]}`;
                        document.getElementById('co-img').src = r.guest.id_photo;

                        // Overstay Check
                        const outDate = new Date(r.check_out_date);
                        const today = new Date(); today.setHours(0,0,0,0);
                        const overBox = document.getElementById('co-overstay');
                        if(today > outDate) {
                            const days = Math.ceil((today - outDate) / (86400000));
                            overBox.textContent = `OVERSTAY ALERT: Guest is ${days} day(s) late!`;
                            overBox.style.display = 'block';
                        } else { overBox.style.display = 'none'; }

                        // Balance Check
                        const rem = parseFloat(r.payment.remaining_amount);
                        const bBox = document.getElementById('co-balance');
                        bBox.className = rem <= 0 ? 'bal-box bal-green' : 'bal-box bal-red';
                        bBox.textContent = rem <= 0 ? "Payment: Clear" : `COLLECT PAYMENT: ${rem.toFixed(2)} ETB`;
                    };
                    results.appendChild(div);
                });
            }
        }

        // RESERVATION SEARCH (Updated to show Phone in list)
        async function searchReservation() {
            const q = document.getElementById('rl-search').value;
            if(q.length < 2) return document.getElementById('rl-results').style.display = 'none';
            
            const res = await fetch(`{{ route('staff.reservation.search') }}?query=${q}`);
            const data = await res.json();
            const results = document.getElementById('rl-results');
            results.innerHTML = '';
            
            if(data.length > 0) {
                results.style.display = 'block';
                data.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    // Added phone number to the display string
                    div.textContent = `${r.guest.fullname} (${r.guest.phone_no}) - Room ${r.room.room_number}`;
                    div.onclick = () => {
                        results.style.display = 'none';
                        document.getElementById('rl-box').style.display = 'block';
                        document.getElementById('rl-res-id').value = r.id;
                        document.getElementById('rl-title').textContent = "Arriving: " + r.guest.fullname;
                        document.getElementById('rl-room').textContent = r.room.room_number;
                        document.getElementById('rl-deposit').textContent = r.payment.amount_paid;
                    };
                    results.appendChild(div);
                });
            } else {
                results.style.display = 'none';
            }
        }
    </script>
</body>
</html>