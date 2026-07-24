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
        button.danger { background:#e74c3c; }
        button.danger:hover { background:#c0392b; }

        ul.errors { background:#ffecec; color:#c0392b; padding:15px 20px; margin-bottom:20px; border-left:5px solid #e74c3c; border-radius:5px; }
        .success-msg { background:#eafaf1; color:#27ae60; padding:12px; border-left:5px solid #27ae60; border-radius:5px; margin-bottom:20px; }
        
        .card { background:white; padding:20px; border-radius:8px; max-width:600px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom: 20px; }

        /* Cam Box */
        .cam-box { display:flex; gap:15px; align-items:flex-start; flex-wrap:wrap; }
        video, canvas, #idPreview { width:240px; height:180px; background:#000; border-radius:6px; object-fit:cover; }
        canvas { display:none; }
        #idPreview { display:none; border:2px solid #27ae60; }

        /* Search Results Styles */
        .search-container { position: relative; width: 100%; max-width: 320px; }
        .search-results { 
            position: absolute; background: white; border: 1px solid #ddd; width: 100%; 
            z-index: 100; border-radius: 0 0 5px 5px; max-height: 200px; overflow-y: auto; display:none;
        }
        .search-item { padding: 10px; cursor: pointer; border-bottom: 1px solid #eee; }
        .search-item:hover { background: #f4f6f9; }

        /* Checkout Detail Styles */
        .detail-grid { display: grid; grid-template-columns: 150px 1fr; gap: 20px; margin-top: 20px; }
        .detail-photo { width: 150px; height: 110px; border-radius: 8px; object-fit: cover; border: 1px solid #ddd; }
        .info-row { margin-bottom: 8px; font-size: 14px; }
        .info-label { color: #888; font-weight: bold; width: 120px; display: inline-block; }
        .alert-box { padding: 10px; border-radius: 5px; margin-top: 10px; font-weight: bold; }
        .alert-danger { background: #ffecec; color: #c0392b; border-left: 4px solid #e74c3c; }
        .alert-success { background: #eafaf1; color: #27ae60; border-left: 4px solid #27ae60; }

        .price-line { font-weight:bold; color:#2c3e50; margin-top:10px; padding: 10px; background: #eef2f7; border-radius: 5px; }
        .remaining-line { font-weight:bold; margin-top:5px; padding: 10px; border-radius: 5px; }
        .bal-red { background: #fdf2f2; color: #c0392b; }
        .bal-green { background: #f2fdf5; color: #27ae60; }
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
                <div id="roomResults" class="room-result" style="display:none; padding:10px; border:1px solid #ddd; margin-top:10px;"></div>

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
                <select name="payment_way" id="payment_way_checkin" required>
                    <option value="full">Full Payment</option>
                    <option value="partial">Partial Payment</option>
                </select>
                <label>Amount Paid</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid_checkin" required>
                <div class="remaining-line" id="remainingLineCheckin" style="display:none;"></div>

                <button type="submit">Check In</button>
            </form>
        </div>

        <!-- CHECK OUT PANEL -->
        <div id="panel-checkout" class="panel">
            <h1>Check Out Guest</h1>
            <div class="card">
                <label>Search Guest (Name or Phone)</label>
                <div class="search-container">
                    <input type="text" id="co-search" placeholder="Type name..." autocomplete="off">
                    <div id="co-results" class="search-results"></div>
                </div>

                <div id="checkout-details" style="display:none; margin-top:30px;">
                    <hr>
                    <div class="detail-grid">
                        <img id="co-img" src="" class="detail-photo" alt="Guest ID">
                        <div>
                            <div class="info-row"><span class="info-label">Full Name:</span> <span id="co-name"></span></div>
                            <div class="info-row"><span class="info-label">Phone:</span> <span id="co-phone"></span></div>
                            <div class="info-row"><span class="info-label">Room:</span> <span id="co-room"></span></div>
                            <div class="info-row"><span class="info-label">Check-in:</span> <span id="co-in"></span></div>
                            <div class="info-row"><span class="info-label">Expected Out:</span> <span id="co-out"></span></div>
                        </div>
                    </div>

                    <div id="co-overstay-msg" class="alert-box"></div>
                    <div id="co-payment-msg" class="alert-box"></div>

                    <form action="{{ route('staff.checkout.process') }}" method="POST" onsubmit="return confirm('Confirm guest checkout?')">
                        @csrf
                        <input type="hidden" name="reservation_id" id="co-res-id">
                        <button type="submit" class="danger">Confirm Check Out</button>
                    </form>
                </div>
            </div>
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

        // ---- Check-in Logic (Keep existing) ----
        const roomTypeSelect = document.getElementById('room_type_id');
        const roomSelect = document.getElementById('room_id');
        const priceLine = document.getElementById('priceLine');
        const amtPaidInp = document.getElementById('amount_paid_checkin');
        const payWaySelect = document.getElementById('payment_way_checkin');
        const remLineCheckin = document.getElementById('remainingLineCheckin');
        let roomPrices = {};
        let currentTotal = 0;

        document.getElementById('findRoomsBtn').addEventListener('click', async () => {
            const rtId = roomTypeSelect.value;
            if(!rtId) return alert('Select type');
            const res = await fetch(`{{ route('staff.rooms.available') }}?room_type_id=${rtId}`);
            const rooms = await res.json();
            roomSelect.innerHTML = '<option value="">-- select --</option>';
            roomPrices = {};
            rooms.forEach(r => {
                roomPrices[r.id] = parseFloat(r.price_per_night);
                roomSelect.innerHTML += `<option value="${r.id}">Room ${r.room_number} (${r.price_per_night} ETB)</option>`;
            });
        });

        function updateCheckinPrice() {
            const rid = roomSelect.value;
            const d1 = new Date(document.getElementById('check_in_date').value);
            const d2 = new Date(document.getElementById('check_out_date').value);
            if(!rid || isNaN(d1) || isNaN(d2)) return;
            let nights = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            if(nights < 1) nights = 1;
            currentTotal = nights * roomPrices[rid];
            priceLine.innerHTML = `Total: ${currentTotal.toFixed(2)} ETB (${nights} nights)`;
            priceLine.style.display = 'block';
            if(payWaySelect.value === 'full') {
                amtPaidInp.value = currentTotal.toFixed(2);
                amtPaidInp.readOnly = true;
            } else {
                amtPaidInp.readOnly = false;
            }
            calcBal();
        }
        function calcBal() {
            const paid = parseFloat(amtPaidInp.value) || 0;
            const rem = currentTotal - paid;
            remLineCheckin.style.display = 'block';
            remLineCheckin.textContent = rem <= 0 ? "Fully Paid" : `Remaining: ${rem.toFixed(2)} ETB`;
            remLineCheckin.className = rem <= 0 ? "remaining-line bal-green" : "remaining-line bal-red";
        }
        roomSelect.onchange = updateCheckinPrice;
        amtPaidInp.oninput = calcBal;
        payWaySelect.onchange = updateCheckinPrice;

        // ---- CHECK OUT LOGIC (NEW) ----
        const coSearch = document.getElementById('co-search');
        const coResults = document.getElementById('co-results');
        const coDetails = document.getElementById('checkout-details');

        coSearch.addEventListener('input', async () => {
            const q = coSearch.value;
            if(q.length < 2) { coResults.style.display = 'none'; return; }
            const res = await fetch(`{{ route('staff.checkout.search') }}?query=${q}`);
            const data = await res.json();
            coResults.innerHTML = '';
            if(data.length > 0) {
                coResults.style.display = 'block';
                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    div.textContent = `${item.guest.fullname} (Room ${item.room.room_number})`;
                    div.onclick = () => selectGuestForCheckout(item);
                    coResults.appendChild(div);
                });
            } else {
                coResults.style.display = 'none';
            }
        });

        function selectGuestForCheckout(res) {
            coResults.style.display = 'none';
            coSearch.value = res.guest.fullname;
            coDetails.style.display = 'block';

            // Fill details
            document.getElementById('co-res-id').value = res.id;
            document.getElementById('co-img').src = res.guest.id_photo || '';
            document.getElementById('co-name').textContent = res.guest.fullname;
            document.getElementById('co-phone').textContent = res.guest.phone_no;
            document.getElementById('co-room').textContent = res.room.room_number;
            document.getElementById('co-in').textContent = res.check_in_date.split('T')[0];
            document.getElementById('co-out').textContent = res.check_out_date.split('T')[0];

            // Overstay Logic
            const today = new Date();
            today.setHours(0,0,0,0);
            const outDate = new Date(res.check_out_date);
            const overstayMsg = document.getElementById('co-overstay-msg');
            
            if (today > outDate) {
                const diffTime = Math.abs(today - outDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                overstayMsg.textContent = `ALERT: Overstayed by ${diffDays} day(s)!`;
                overstayMsg.className = "alert-box alert-danger";
            } else {
                overstayMsg.textContent = "Stay duration is within schedule.";
                overstayMsg.className = "alert-box alert-success";
            }

            // Payment Logic
            const payMsg = document.getElementById('co-payment-msg');
            const rem = parseFloat(res.payment.remaining_amount);
            if (rem > 0) {
                payMsg.textContent = `PAYMENT DUE: ${rem.toFixed(2)} ETB must be collected.`;
                payMsg.className = "alert-box alert-danger";
            } else {
                payMsg.textContent = "Payment Status: Fully Paid.";
                payMsg.className = "alert-box alert-success";
            }
        }
    </script>
</body>
</html>