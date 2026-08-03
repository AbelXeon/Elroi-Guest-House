<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard — Elroi Guest House</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --ink:#1c2b29; --panel:#20342f; --accent:#3f6b52; --accent-light:#57876a;
            --accent-soft:#e7efe9; --bg:#f6f5f2; --card-bg:#ffffff; --border:#e0ded7;
            --text:#26312e; --muted:#6b756f; --error:#b3413a; --error-bg:#fbeceb;
            --warn:#c98a2c; --warn-bg:#fdf1de; --shadow:0 2px 12px rgba(28,43,41,0.06);
        }
        *{ margin:0; padding:0; box-sizing:border-box; font-family:'Inter', sans-serif; }
        html,body{ height:100%; overflow-x:hidden; }
        body{ background:var(--bg); color:var(--text); display:flex; height:100vh; overflow:hidden; }
        h1,h2,h3{ font-family:'Fraunces', serif; font-weight:500; color:var(--ink); }

        /* ===== Sidebar ===== */
        .sidebar{ width:230px; flex-shrink:0; background:linear-gradient(160deg, var(--ink) 0%, var(--panel) 100%);
            color:#f4f2ec; display:flex; flex-direction:column; padding:26px 0; height:100vh; overflow-y:auto; }
        .brand{ display:flex; align-items:center; gap:10px; padding:0 22px 22px;
            border-bottom:1px solid rgba(244,242,236,0.1); margin-bottom:14px; }
        .brand span{ font-family:'Fraunces', serif; font-weight:600; font-size:15px; }
        .nav-link{ display:flex; align-items:center; gap:11px; padding:11px 22px; color:#c7cec8;
            text-decoration:none; cursor:pointer; font-size:13.5px; border-left:3px solid transparent;
            transition:background .15s, color .15s; }
        .nav-link svg{ flex-shrink:0; opacity:.85; }
        .nav-link.active, .nav-link:hover{ background:rgba(244,242,236,0.06); color:#fff; border-left-color:var(--accent-light); }
        .sidebar-footer{ margin-top:auto; padding:0 22px; }
        .logout-btn{ width:100%; padding:11px; background:rgba(179,65,58,0.15); border:1px solid rgba(179,65,58,0.35);
            border-radius:8px; color:#f2a9a4; font-size:13px; font-weight:600; cursor:pointer; }
        .logout-btn:hover{ background:rgba(179,65,58,0.28); }

        /* ===== Main ===== */
        .main{ flex:1; height:100vh; overflow-y:auto; overflow-x:hidden; padding:30px 34px 100px; }
        .topline{ position:sticky; top:0; z-index:15; background:var(--bg); padding:0 0 16px; margin-bottom:6px;
            display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
        .topline h1{ font-size:24px; }

        .segmented{ display:flex; gap:5px; background:#eceae4; padding:4px; border-radius:10px; }
        .seg-btn{ padding:9px 16px; background:transparent; color:var(--muted); border:none; border-radius:8px;
            font-size:13px; font-weight:600; cursor:pointer; white-space:nowrap; }
        .seg-btn.active{ background:#fff; color:var(--ink); box-shadow:var(--shadow); }

        .panel{ display:none; }
        .panel.active{ display:block; }
        .fd-view{ display:none; }
        .fd-view.active{ display:block; }

        .empty-panel{ background:var(--card-bg); border:1px dashed var(--border); border-radius:12px; padding:40px 24px;
            text-align:center; color:var(--muted); }
        .empty-panel h3{ margin-bottom:6px; color:var(--text); }

        .card{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; padding:26px;
            box-shadow:var(--shadow); margin-bottom:20px; min-width:0; }
        label{ display:block; font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin:0 0 6px; }
        input, select{ width:100%; padding:11px 12px; font-size:14px; color:var(--text); background:#fff;
            border:1px solid var(--border); border-radius:8px; outline:none; transition:border-color .15s; min-width:0; }
        input:focus, select:focus{ border-color:var(--accent); }
        .field{ margin-bottom:16px; min-width:0; }

        .two-col{ display:grid; grid-template-columns:1fr 1fr; gap:26px; align-items:start; }
        @media (max-width:900px){ .two-col{ grid-template-columns:1fr; } }

        button{ padding:11px 18px; background:var(--accent); border:none; border-radius:8px; color:#fff;
            font-weight:600; font-size:13.5px; cursor:pointer; transition:background .15s; }
        button:hover{ background:var(--accent-light); }
        button.danger{ background:var(--error); }
        button.danger:hover{ background:#c65750; }
        button.success{ background:var(--accent); }
        button.ghost{ background:transparent; border:1px solid var(--border); color:var(--text); }
        button.ghost:hover{ background:#f1f0ec; }
        button:disabled{ opacity:.5; cursor:not-allowed; }

        .ban-indicator{ display:none; padding:10px 14px; border-radius:8px; font-size:13px; font-weight:600; margin-top:2px; margin-bottom:14px; }
        .ban-indicator.checking{ background:#f1f0ec; color:var(--muted); display:block; }
        .ban-indicator.green{ background:var(--accent-soft); color:var(--accent); display:block; }
        .ban-indicator.red{ background:var(--error-bg); color:var(--error); display:block; }

        .chip-row{ display:flex; gap:8px; flex-wrap:wrap; margin:10px 0 4px; }
        .chip{ padding:7px 13px; border-radius:20px; border:1px solid var(--border); background:#fff;
            font-size:12.5px; color:var(--text); cursor:pointer; transition:.15s; }
        .chip:hover{ border-color:var(--accent); color:var(--accent); }

        .cam-box{ display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap; margin-bottom:10px; }
        video, .preview-img{ width:220px; height:165px; background:#0e1a17; border-radius:10px; object-fit:cover; border:1px solid var(--border); max-width:100%; }
        .cam-actions{ display:flex; gap:8px; flex-wrap:wrap; }
        canvas{ display:none; }

        .price-line{ font-weight:600; color:var(--ink); margin-top:12px; padding:12px 14px; background:var(--accent-soft);
            border-radius:8px; border-left:4px solid var(--accent); font-size:13.5px; }
        .bal-box{ font-weight:600; margin-top:10px; padding:12px 14px; border-radius:8px; border-left:4px solid; font-size:13.5px; word-break:break-word; }
        .bal-red{ background:var(--error-bg); color:var(--error); border-color:var(--error); }
        .bal-green{ background:var(--accent-soft); color:var(--accent); border-color:var(--accent); }

        /* ---- Inline calendar ---- */
        .calendar-inline{ border:1px solid var(--border); border-radius:12px; padding:14px; background:#fff; margin-top:6px; max-width:100%; overflow:hidden; }
        .cal-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; font-size:13.5px; font-weight:600; color:var(--ink); }
        .cal-header button{ padding:4px 9px; background:var(--accent-soft); color:var(--accent); font-size:14px; }
        .cal-grid{ display:grid; grid-template-columns:repeat(7, 1fr); gap:2px; }
        .cal-dow{ font-size:10px; text-transform:uppercase; color:var(--muted); text-align:center; padding:4px 0; }
        .cal-day{ text-align:center; padding:8px 0; font-size:12.5px; border-radius:6px; cursor:pointer; color:var(--text); }
        .cal-day:hover{ background:var(--accent-soft); }
        .cal-day.cal-in-range{ background:var(--accent-soft); color:var(--accent); }
        .cal-day.cal-selected{ background:var(--accent); color:#fff; font-weight:700; }
        .cal-day.cal-today{ box-shadow:inset 0 0 0 2px var(--accent); font-weight:700; }
        .cal-footer{ margin-top:10px; font-size:12px; color:var(--muted); text-align:center; }

        /* ---- Room chips ---- */
        .rooms-box{ display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; min-height:20px; }
        .room-chip{ padding:10px 14px; border:1px solid var(--border); border-radius:10px; cursor:pointer;
            font-size:12.5px; display:flex; flex-direction:column; gap:2px; transition:.15s; background:#fff; }
        .room-chip:hover{ border-color:var(--accent); }
        .room-chip.selected{ background:var(--accent); border-color:var(--accent); color:#fff; }
        .room-chip span{ font-size:11px; opacity:.8; }
        .rooms-loading{ font-size:12.5px; color:var(--muted); padding:10px 0; animation:pulse 1.2s infinite; }
        .rooms-empty{ font-size:12.5px; color:var(--error); padding:10px 0; }
        @keyframes pulse{ 0%,100%{ opacity:.5; } 50%{ opacity:1; } }

        .search-container{ position:relative; max-width:420px; margin-bottom:6px; width:100%; }
        .search-results{ position:absolute; background:#fff; border:1px solid var(--border); width:100%;
            z-index:100; border-radius:0 0 10px 10px; max-height:220px; overflow-y:auto; display:none; box-shadow:var(--shadow); }
        .search-item{ padding:12px 14px; cursor:pointer; border-bottom:1px solid var(--border); font-size:13.5px; }
        .search-item:hover{ background:var(--accent-soft); color:var(--accent); }

        .list-header{ display:flex; justify-content:space-between; align-items:center; margin:22px 0 12px; flex-wrap:wrap; gap:8px; }
        .list-header h3{ font-size:15px; }
        .list-header span{ font-size:12px; color:var(--muted); }

        .card-grid{ display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:14px; }
        .list-card{ background:var(--card-bg); border:1px solid var(--border); border-radius:10px; padding:16px;
            box-shadow:var(--shadow); cursor:pointer; transition:.15s; position:relative; min-width:0; overflow:hidden; }
        .list-card:hover{ border-color:var(--accent); transform:translateY(-2px); }
        .list-card .top-row{ display:flex; justify-content:space-between; align-items:flex-start; }
        .list-card .name{ font-weight:600; font-size:14.5px; color:var(--ink); margin-bottom:4px; word-break:break-word; }
        .list-card .meta{ font-size:12.5px; color:var(--muted); margin-bottom:3px; word-break:break-word; }
        .list-card .tag{ display:inline-block; font-size:10.5px; text-transform:uppercase; padding:3px 9px;
            border-radius:20px; background:var(--accent-soft); color:var(--accent); font-weight:600; margin-top:6px; }
        .list-card .tag.banned{ background:var(--error-bg); color:var(--error); }

        .dots-btn{ background:none; border:none; padding:2px 6px; font-size:16px; line-height:1; color:var(--muted);
            cursor:pointer; border-radius:6px; margin:0; }
        .dots-btn:hover{ background:#f1f0ec; }
        .dots-menu{ position:absolute; top:36px; right:14px; background:#fff; border:1px solid var(--border);
            border-radius:8px; box-shadow:var(--shadow); z-index:20; display:none; min-width:150px; overflow:hidden; }
        .dots-menu.open{ display:block; }
        .dots-menu button{ width:100%; text-align:left; background:none; color:var(--error); border:none;
            border-radius:0; padding:10px 14px; font-size:12.5px; font-weight:600; margin:0; }
        .dots-menu button:hover{ background:var(--error-bg); }

        .guest-profile{ display:flex; gap:18px; margin-top:14px; align-items:center; flex-wrap:wrap; }
        .guest-info p{ margin-bottom:5px; font-size:13.5px; word-break:break-word; }

        .modal-overlay{ position:fixed; inset:0; background:rgba(28,43,41,0.5); display:none; align-items:center;
            justify-content:center; z-index:200; padding:16px; }
        .modal-overlay.open{ display:flex; }
        .modal-box{ background:#fff; border-radius:12px; padding:26px; max-width:360px; width:100%; box-shadow:0 10px 40px rgba(0,0,0,.25); }
        .modal-box h3{ font-size:17px; margin-bottom:6px; }
        .modal-box p{ font-size:13px; color:var(--muted); margin-bottom:16px; }
        .modal-actions{ display:flex; gap:10px; margin-top:16px; }
        .modal-actions button{ flex:1; }

        /* ---- Toasts ---- */
        .toast-container{ position:fixed; bottom:22px; right:22px; z-index:500; display:flex; flex-direction:column; gap:10px; }
        .toast{ background:var(--ink); color:#f4f2ec; padding:13px 18px; border-radius:10px;
            box-shadow:0 8px 24px rgba(0,0,0,.25); min-width:220px; max-width:320px;
            font-size:13.5px; position:relative; overflow:hidden; animation:toastIn .3s ease; }
        .toast.success{ border-left:4px solid var(--accent); }
        .toast.error{ border-left:4px solid var(--error); }
        .toast.info{ border-left:4px solid var(--warn); }
        .toast .toast-bar{ position:absolute; bottom:0; left:0; height:3px; background:rgba(244,242,236,0.35); animation:toastBar 3s linear forwards; }
        @keyframes toastIn{ from{ opacity:0; transform:translateX(30px); } to{ opacity:1; transform:translateX(0); } }
        @keyframes toastOut{ from{ opacity:1; transform:translateX(0); } to{ opacity:0; transform:translateX(30px); } }
        @keyframes toastBar{ from{ width:100%; } to{ width:0%; } }

        /* ===== Mobile: hamburger + off-canvas sidebar ===== */
        .hamburger-btn{ display:none; background:none; border:none; color:var(--ink); cursor:pointer; padding:6px; }
        .sidebar-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:39; }
        .sidebar-overlay.open{ display:block; }
        .mobile-topbar{ display:none; }

        @media (max-width:900px){
            body{ display:block; height:auto; overflow:visible; }

            .sidebar{ position:fixed; top:0; left:0; z-index:40; width:260px;
                transform:translateX(-100%); transition:transform .25s ease; }
            .sidebar.open{ transform:translateX(0); }

            .hamburger-btn{ display:inline-flex; align-items:center; }

            .main{ height:auto; overflow-y:visible; overflow-x:hidden; max-width:100vw; padding:76px 16px 40px; }

            .mobile-topbar{
                display:flex; align-items:center; gap:12px;
                position:fixed; top:0; left:0; right:0; height:60px; padding:0 16px;
                background:rgba(246, 245, 242, 0.85);
                backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
                border-bottom:1px solid var(--border); z-index:38; max-width:100%;
            }
            .mobile-topbar span{ font-family:'Fraunces', serif; font-weight:600; font-size:16px; color:var(--ink);
                overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

            video, .preview-img{ width:100%; max-width:100%; }
        }
        @media (max-width:480px){
            .card-grid{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="brand">
            <svg width="22" height="22" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="7" fill="#3f6b52"/><path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
            </svg>
            <span>Elroi — Staff</span>
        </div>
        <a class="nav-link" data-panel="frontdesk" onclick="showPanel('frontdesk')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/></svg>
            Front Desk
        </a>
        <a class="nav-link" data-panel="reservationlist" onclick="showPanel('reservationlist')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>
            Reservation List
        </a>
        <a class="nav-link" data-panel="bannedguests" onclick="showPanel('bannedguests')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M5 5l14 14"/></svg>
            Banned Guests
        </a>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="main" id="mainContent">
        <div class="mobile-topbar">
            <button type="button" class="hamburger-btn" onclick="toggleSidebar()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
            <svg width="20" height="20" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="7" fill="#3f6b52"/><path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
            </svg>
            <span>Elroi — Staff</span>
        </div>

        @if (session('success'))
            <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));</script>
        @endif
        @if ($errors->any())
            <script>document.addEventListener('DOMContentLoaded', () => showToast(@json($errors->first()), 'error'));</script>
        @endif

        <!-- ===== FRONT DESK (Check In / Check Out / Reservation) ===== -->
        <div id="panel-frontdesk" class="panel active">
            <div class="topline">
                <h1>Front Desk</h1>
                <div class="segmented">
                    <button type="button" class="seg-btn active" data-fd="checkin" onclick="showFrontDesk('checkin')">Check In</button>
                    <button type="button" class="seg-btn" data-fd="checkout" onclick="showFrontDesk('checkout')">Check Out</button>
                    <button type="button" class="seg-btn" data-fd="reservation" onclick="showFrontDesk('reservation')">Reservation</button>
                </div>
            </div>

            <!-- ---- CHECK IN ---- -->
            <div id="fd-checkin" class="fd-view active">
                <form action="{{ route('staff.checkin.store') }}" method="POST" class="card" id="checkinForm">
                    @csrf
                    <input type="hidden" name="check_in_date" id="ci-in">
                    <input type="hidden" name="check_out_date" id="ci-out">
                    <input type="hidden" name="room_id" id="ci-room-id">
                    <input type="hidden" id="ci-banned-flag" value="false">

                    <div class="two-col">
                        <div>
                            <h3 style="margin-bottom:16px;">Guest</h3>
                            <div class="field"><label>Full Name</label><input type="text" name="fullname" id="ci-fullname" required oninput="onPhoneInput('ci')"></div>
                            <div class="field"><label>Phone Number</label><input type="text" name="phone_no" id="ci-phone" required pattern="[0-9+\-\s]{6,20}" oninput="onPhoneInput('ci')"></div>
                            <div id="ci-ban-status" class="ban-indicator"></div>
                            <div class="field">
                                <label>ID Type</label>
                                <select name="id_type" required>
                                    <option value="national_id">National ID</option>
                                    <option value="kebele_id">Kebele ID</option>
                                    <option value="passport">Passport</option>
                                    <option value="driving_license">Driving License</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Capture ID Photo</label>
                                <div class="cam-box">
                                    <video id="ci-video" autoplay playsinline></video>
                                    <img id="ci-preview" class="preview-img" style="display:none;">
                                </div>
                                <div class="cam-actions">
                                    <button type="button" class="ghost" onclick="startCamera('ci-video','ci-cap-btn')">Start Camera</button>
                                    <button type="button" id="ci-cap-btn" onclick="takeSnapshot('ci-video','ci-preview','ci-photo-input')" disabled>Capture</button>
                                </div>
                                <input type="hidden" name="id_photo" id="ci-photo-input">
                            </div>
                        </div>

                        <div>
                            <h3 style="margin-bottom:16px;">Stay &amp; Payment</h3>
                            <div class="field">
                                <label>Stay Dates</label>
                                <div class="calendar-inline">
                                    <div class="cal-header">
                                        <button type="button" onclick="calNavGeneric('ci',-1)">‹</button>
                                        <span id="ci-cal-month"></span>
                                        <button type="button" onclick="calNavGeneric('ci',1)">›</button>
                                    </div>
                                    <div class="cal-grid" id="ci-cal-grid"></div>
                                    <div class="cal-footer" id="ci-cal-label">Select check-in date</div>
                                </div>
                                <div class="chip-row">
                                    <span class="chip" onclick="setNights('ci',1)">+1 night</span>
                                    <span class="chip" onclick="setNights('ci',2)">+2 nights</span>
                                    <span class="chip" onclick="setNights('ci',3)">+3 nights</span>
                                    <span class="chip" onclick="setNights('ci',7)">+1 week</span>
                                </div>
                            </div>

                            <div class="field">
                                <label>Room Type</label>
                                <select id="ci-type" onchange="doFindRooms('ci')">
                                    <option value="">-- select --</option>
                                    @foreach ($roomTypes as $type) <option value="{{ $type->id }}">{{ $type->name }}</option> @endforeach
                                </select>
                                <div class="rooms-box" id="ci-rooms-box"></div>
                            </div>
                            <div id="ci-price-line" class="price-line" style="display:none;"></div>

                            <div class="field" style="margin-top:16px;">
                                <label>Payment Method</label>
                                <select name="payment_type">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="pos">POS</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Payment Mode</label>
                                <select name="payment_way" id="ci-pay-way" onchange="calculateCheckinPrice()">
                                    <option value="full">Full Payment</option>
                                    <option value="partial">Partial Payment</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Amount Paid Now</label>
                                <input type="number" step="0.01" min="0" name="amount_paid" id="ci-paid" required oninput="calculateCheckinPrice()">
                            </div>
                            <div id="ci-bal-line" class="bal-box" style="display:none;"></div>

                            <button type="submit" class="success" style="width:100%; margin-top:18px;">Complete Check In</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ---- CHECK OUT ---- -->
            <div id="fd-checkout" class="fd-view">
                <div class="card" style="max-width:680px;">
                    <label>Search Guest Name or Room</label>
                    <div class="search-container">
                        <input type="text" id="co-search" placeholder="Search name or room..." oninput="searchCheckout()" autocomplete="off">
                        <div id="co-results" class="search-results"></div>
                    </div>

                    <div id="co-details" style="display:none; margin-top:18px;">
                        <hr style="border:none; border-top:1px solid var(--border); margin-bottom:14px;">
                        <div class="guest-profile">
                            <img id="co-img" src="" class="preview-img" alt="Guest ID">
                            <div class="guest-info">
                                <p><strong>Name:</strong> <span id="co-name"></span></p>
                                <p><strong>Phone:</strong> <span id="co-phone"></span></p>
                                <p><strong>Room:</strong> <span id="co-room"></span></p>
                                <p><strong>Dates:</strong> <span id="co-dates"></span></p>
                            </div>
                        </div>
                        <div id="co-overstay" class="bal-box bal-red" style="display:none;"></div>
                        <div id="co-balance" class="bal-box"></div>

                        <form action="{{ route('staff.checkout.process') }}" method="POST" onsubmit="showToast('Checking out...', 'info', true)">
                            @csrf
                            <input type="hidden" name="reservation_id" id="co-res-id">
                            <button type="submit" class="danger" style="margin-top:10px;">Confirm Checkout</button>
                        </form>
                    </div>
                </div>

                <div class="list-header"><h3>Currently Staying</h3><span>tap a guest to check out</span></div>
                @isset($activeStays)
                    @if ($activeStays->count())
                        <div class="card-grid">
                            @foreach ($activeStays as $r)
                                <div class="list-card">
                                    <div class="top-row">
                                        <div onclick="loadCheckoutDetails({{ $r->id }})" style="flex:1; cursor:pointer;">
                                            <div class="name">{{ $r->guest->fullname }}</div>
                                            <div class="meta">Room {{ $r->room->room_number }}</div>
                                            <div class="meta">{{ $r->guest->phone_no }}</div>
                                            <div class="meta">{{ \Carbon\Carbon::parse($r->check_in_date)->format('M j') }} → {{ \Carbon\Carbon::parse($r->check_out_date)->format('M j') }}</div>
                                            <span class="tag">In House</span>
                                        </div>
                                        <button type="button" class="dots-btn" onclick="event.stopPropagation(); toggleDots(this)">⋮</button>
                                    </div>
                                    <div class="dots-menu">
                                        <button type="button" onclick="event.stopPropagation(); openBanModal({{ $r->guest->id }}, '{{ addslashes($r->guest->fullname) }}')">Ban Guest</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-panel"><h3>No one to check out</h3><p>Guests currently staying will appear here.</p></div>
                    @endif
                @endisset
            </div>

            <!-- ---- RESERVATION (Phone Booking) ---- -->
            <div id="fd-reservation" class="fd-view">
                <form action="{{ route('staff.reservation.store') }}" method="POST" class="card" id="reservationForm">
                    @csrf
                    <input type="hidden" name="check_in_date" id="res-in">
                    <input type="hidden" name="check_out_date" id="res-out">
                    <input type="hidden" name="room_id" id="res-room-id">
                    <input type="hidden" id="res-banned-flag" value="false">

                    <div class="two-col">
                        <div>
                            <h3 style="margin-bottom:16px;">Guest &amp; Contact</h3>
                            <div class="field"><label>Guest Full Name</label><input type="text" name="fullname" id="res-fullname" required oninput="onPhoneInput('res')"></div>
                            <div class="field"><label>Phone Number</label><input type="text" name="phone_no" id="res-phone" required pattern="[0-9+\-\s]{6,20}" oninput="onPhoneInput('res')"></div>
                            <div id="res-ban-status" class="ban-indicator"></div>

                            <div class="field">
                                <label>Deposit / Down Payment</label>
                                <input type="number" step="0.01" min="0" name="amount_paid" placeholder="Enter deposit amount" required>
                            </div>
                        </div>

                        <div>
                            <h3 style="margin-bottom:16px;">Stay Details</h3>
                            <div class="field">
                                <label>Stay Dates</label>
                                <div class="calendar-inline">
                                    <div class="cal-header">
                                        <button type="button" onclick="calNavGeneric('res',-1)">‹</button>
                                        <span id="res-cal-month"></span>
                                        <button type="button" onclick="calNavGeneric('res',1)">›</button>
                                    </div>
                                    <div class="cal-grid" id="res-cal-grid"></div>
                                    <div class="cal-footer" id="res-cal-label">Select check-in date</div>
                                </div>
                                <div class="chip-row">
                                    <span class="chip" onclick="setNights('res',1)">+1 night</span>
                                    <span class="chip" onclick="setNights('res',2)">+2 nights</span>
                                    <span class="chip" onclick="setNights('res',3)">+3 nights</span>
                                    <span class="chip" onclick="setNights('res',7)">+1 week</span>
                                </div>
                            </div>

                            <div class="field">
                                <label>Room Type</label>
                                <select id="res-type" onchange="doFindRooms('res')">
                                    <option value="">-- select --</option>
                                    @foreach ($roomTypes as $type) <option value="{{ $type->id }}">{{ $type->name }}</option> @endforeach
                                </select>
                                <div class="rooms-box" id="res-rooms-box"></div>
                            </div>
                            <div id="res-price-line" class="price-line" style="display:none;"></div>

                            <button type="submit" style="width:100%; margin-top:18px;">Save Reservation</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ===== RESERVATION LIST ===== -->
        <div id="panel-reservationlist" class="panel">
            <div class="topline"><h1>Reservation Arrivals</h1></div>

            <div class="card" style="max-width:680px;">
                <label>Search Reserved Guest (Name or Phone)</label>
                <div class="search-container">
                    <input type="text" id="rl-search" placeholder="Search name or phone..." oninput="searchReservation()" autocomplete="off">
                    <div id="rl-results" class="search-results"></div>
                </div>

                <div id="rl-box" style="display:none; margin-top:18px;">
                    <hr style="border:none; border-top:1px solid var(--border); margin-bottom:14px;">
                    <h3 id="rl-title" style="font-size:16px; margin-bottom:2px;"></h3>
                    <p style="font-size:12.5px; color:var(--muted); margin-bottom:6px;">Phone: <span id="rl-phone"></span></p>
                    <p style="font-size:13.5px; color:var(--muted); margin-bottom:14px;">Room: <span id="rl-room"></span> &middot; Paid Deposit: <span id="rl-deposit"></span> ETB</p>

                    <form action="{{ route('staff.reservation.complete') }}" method="POST" onsubmit="showToast('Completing check-in...', 'info', true)">
                        @csrf
                        <input type="hidden" name="reservation_id" id="rl-res-id">
                        <div class="field">
                            <label>ID Type</label>
                            <select name="id_type" required>
                                <option value="national_id">National ID</option>
                                <option value="passport">Passport</option>
                                <option value="kebele_id">Kebele ID</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Capture Photo Now</label>
                            <div class="cam-box">
                                <video id="rl-video" autoplay playsinline></video>
                                <img id="rl-preview" class="preview-img" style="display:none;">
                            </div>
                            <div class="cam-actions">
                                <button type="button" class="ghost" onclick="startCamera('rl-video','rl-cap-btn')">Start Camera</button>
                                <button type="button" id="rl-cap-btn" onclick="takeSnapshot('rl-video','rl-preview','rl-photo-input')" disabled>Capture</button>
                            </div>
                            <input type="hidden" name="id_photo" id="rl-photo-input" required>
                        </div>
                        <button type="submit" class="success">Finish Full Check-In</button>
                    </form>
                </div>
            </div>

            <div class="list-header"><h3>Awaiting Arrival</h3><span>tap a reservation to check them in</span></div>
            @isset($pendingArrivals)
                @if ($pendingArrivals->count())
                    <div class="card-grid">
                        @foreach ($pendingArrivals as $r)
                            <div class="list-card">
                                <div class="top-row">
                                    <div onclick="loadReservationDetails({{ $r->id }})" style="flex:1; cursor:pointer;">
                                        <div class="name">{{ $r->guest->fullname }}</div>
                                        <div class="meta">{{ $r->guest->phone_no }}</div>
                                        <div class="meta">Room {{ $r->room->room_number }}</div>
                                        <span class="tag">Reserved</span>
                                    </div>
                                    <button type="button" class="dots-btn" onclick="event.stopPropagation(); toggleDots(this)">⋮</button>
                                </div>
                                <div class="dots-menu">
                                    <button type="button" onclick="event.stopPropagation(); openCancelReservationModal({{ $r->id }}, '{{ addslashes($r->guest->fullname) }}')">Cancel Reservation</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-panel"><h3>No pending arrivals</h3><p>Phone reservations waiting to check in will appear here.</p></div>
                @endif
            @endisset
        </div>

        <!-- ===== BANNED GUESTS ===== -->
        <div id="panel-bannedguests" class="panel">
            <div class="topline"><h1>Banned Guests</h1></div>

            @isset($bannedGuests)
                @if ($bannedGuests->count())
                    <div class="card-grid">
                        @foreach ($bannedGuests as $g)
                            <div class="list-card" style="cursor:default;">
                                <div class="name">{{ $g->fullname }}</div>
                                <div class="meta">{{ $g->phone_no }}</div>
                                <span class="tag banned">Banned</span>
                                <button type="button" class="ghost" style="width:100%; margin-top:12px;" onclick="openUnbanModal({{ $g->id }}, '{{ addslashes($g->fullname) }}')">Unban</button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-panel"><h3>No banned guests</h3><p>Guests you ban will show up here.</p></div>
                @endif
            @endisset
        </div>

    </div>

    <canvas id="main-canvas"></canvas>

    <div class="modal-overlay" id="banModalOverlay">
        <div class="modal-box">
            <h3 id="banModalTitle">Ban Guest</h3>
            <p id="banModalText">Enter your staff password to confirm.</p>
            <form id="banModalForm" method="POST" onsubmit="showToast('Processing...', 'info', true)">
                @csrf
                <input type="hidden" name="guest_id" id="banModalGuestId">
                <input type="hidden" name="reservation_id" id="banModalReservationId">
                <div class="field">
                    <label>Your Password</label>
                    <input type="password" name="password" required autofocus>
                </div>
                <div class="modal-actions">
                    <button type="button" class="ghost" onclick="closeBanModal()">Cancel</button>
                    <button type="submit" class="danger" id="banModalSubmit">Confirm Ban</button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ===== NAVIGATION =====
        function showPanel(name) {
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));
            document.getElementById('panel-' + name)?.classList.add('active');
            document.querySelectorAll('.nav-link[data-panel="' + name + '"]').forEach(a => a.classList.add('active'));
            sessionStorage.setItem('staffActivePanel', name);
            document.querySelectorAll('.dots-menu.open').forEach(m => m.classList.remove('open'));
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }

        function showFrontDesk(name) {
            document.querySelectorAll('.fd-view').forEach(v => v.classList.remove('active'));
            document.querySelectorAll('.seg-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('fd-' + name).classList.add('active');
            document.querySelector(`.seg-btn[data-fd="${name}"]`).classList.add('active');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }

        // ===== TOASTS =====
        function showToast(message, type = 'success', persist = false) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${message}</span>` + (persist ? '' : '<div class="toast-bar"></div>');
            container.appendChild(toast);
            if (!persist) {
                setTimeout(() => {
                    toast.style.animation = 'toastOut .3s ease forwards';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
            return toast;
        }

        document.addEventListener('DOMContentLoaded', () => {
            showPanel(sessionStorage.getItem('staffActivePanel') || 'frontdesk');
            initRangeCalendar('ci');
            initRangeCalendar('res');

            // Refresh rooms instantly when dates change (no artificial delay — these are discrete actions)
            ['ci', 'res'].forEach(prefix => {
                document.getElementById(prefix + '-in').addEventListener('change', () => {
                    if (document.getElementById(prefix + '-type').value) doFindRooms(prefix);
                });
                document.getElementById(prefix + '-out').addEventListener('change', () => {
                    if (document.getElementById(prefix + '-type').value) doFindRooms(prefix);
                });
            });

            document.getElementById('checkinForm').addEventListener('submit', (e) => {
                if (!document.getElementById('ci-in').value || !document.getElementById('ci-out').value) {
                    e.preventDefault(); alert('Please select check-in and check-out dates.'); return;
                }
                if (!document.getElementById('ci-room-id').value) {
                    e.preventDefault(); alert('Please select a room.'); return;
                }
                if (document.getElementById('ci-banned-flag').value === 'true') {
                    e.preventDefault(); alert('This guest is banned and cannot be checked in.'); return;
                }
                showToast('Checking in guest...', 'info', true);
            });

            document.getElementById('reservationForm').addEventListener('submit', (e) => {
                if (!document.getElementById('res-in').value || !document.getElementById('res-out').value) {
                    e.preventDefault(); alert('Please select check-in and check-out dates.'); return;
                }
                if (!document.getElementById('res-room-id').value) {
                    e.preventDefault(); alert('Please select a room.'); return;
                }
                if (document.getElementById('res-banned-flag').value === 'true') {
                    e.preventDefault(); alert('This guest is banned and cannot be reserved for.'); return;
                }
                showToast('Saving reservation...', 'info', true);
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.dots-menu.open').forEach(m => m.classList.remove('open'));
        });

        function toggleDots(btn) {
            const menu = btn.closest('.list-card').querySelector('.dots-menu');
            if (!menu) return;
            const isOpen = menu.classList.contains('open');
            document.querySelectorAll('.dots-menu.open').forEach(m => m.classList.remove('open'));
            if (!isOpen) menu.classList.add('open');
        }

        function debounce(fn, delay = 250) {
            let t;
            return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
        }

        // ===== TIMEZONE-SAFE DATE HELPERS =====
        // toISOString() converts to UTC and shifts the date backward in any timezone
        // ahead of UTC (like Ethiopia, UTC+3) — that was the root cause of the
        // calendar highlight/drift bug. These two helpers work in LOCAL time only.
        function toLocalISO(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }
        function parseLocalDate(isoStr) {
            const [y, m, d] = isoStr.split('-').map(Number);
            return new Date(y, m - 1, d);
        }

        // ===== SHARED INLINE CALENDAR (used by ci- and res- prefixes) =====
        const calendars = {};

        function isSameDate(a, b) {
            return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
        }

        function initRangeCalendar(prefix) {
            calendars[prefix] = { viewDate: new Date(), start: null, end: null, hover: null, bound: false };
            renderCalGeneric(prefix);
        }

        function calNavGeneric(prefix, dir) {
            calendars[prefix].viewDate.setMonth(calendars[prefix].viewDate.getMonth() + dir);
            renderCalGeneric(prefix);
        }

        function renderCalGeneric(prefix) {
            const state = calendars[prefix];
            const grid = document.getElementById(prefix + '-cal-grid');
            const label = document.getElementById(prefix + '-cal-month');
            const year = state.viewDate.getFullYear();
            const month = state.viewDate.getMonth();
            label.textContent = state.viewDate.toLocaleString('default', { month: 'long', year: 'numeric' });

            const today = new Date();
            const firstDay = new Date(year, month, 1);
            const startWeekday = firstDay.getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let html = '';
            ['S','M','T','W','T','F','S'].forEach(d => html += `<div class="cal-dow">${d}</div>`);
            for (let i = 0; i < startWeekday; i++) html += `<div></div>`;

            for (let d = 1; d <= daysInMonth; d++) {
                const dateObj = new Date(year, month, d);
                const iso = toLocalISO(dateObj);
                let cls = 'cal-day';

                if (isSameDate(dateObj, today)) cls += ' cal-today';
                if (state.start && state.end && dateObj >= state.start && dateObj <= state.end) cls += ' cal-in-range';
                if (state.start && isSameDate(dateObj, state.start)) cls += ' cal-selected';
                if (state.end && isSameDate(dateObj, state.end)) cls += ' cal-selected';

                html += `<div class="${cls}" data-iso="${iso}">${d}</div>`;
            }
            grid.innerHTML = html;

            if (!state.bound) {
                grid.addEventListener('click', (e) => {
                    const dayEl = e.target.closest('.cal-day');
                    if (dayEl) calPickGeneric(prefix, dayEl.dataset.iso);
                });
                grid.addEventListener('mouseover', (e) => {
                    const dayEl = e.target.closest('.cal-day');
                    if (dayEl) calHoverGeneric(prefix, dayEl.dataset.iso);
                });
                state.bound = true;
            }
        }

        function calPickGeneric(prefix, iso) {
            const state = calendars[prefix];
            const picked = parseLocalDate(iso);

            if (!state.start || (state.start && state.end)) {
                state.start = picked; state.end = null;
                document.getElementById(prefix + '-cal-label').textContent = 'Select check-out date';
            } else {
                if (picked < state.start) { state.end = state.start; state.start = picked; }
                else { state.end = picked; }

                const fromIso = toLocalISO(state.start);
                const toIso = toLocalISO(state.end);
                document.getElementById(prefix + '-cal-label').textContent = `${fromIso} → ${toIso}`;
                document.getElementById(prefix + '-in').value = fromIso;
                document.getElementById(prefix + '-out').value = toIso;
                document.getElementById(prefix + '-in').dispatchEvent(new Event('change'));
                document.getElementById(prefix + '-out').dispatchEvent(new Event('change'));
            }
            renderCalGeneric(prefix);
        }

        function calHoverGeneric(prefix, iso) {
            const state = calendars[prefix];
            if (!state.start || state.end) return;
            state.hover = parseLocalDate(iso);
            const lo = state.start < state.hover ? state.start : state.hover;
            const hi = state.start < state.hover ? state.hover : state.start;
            document.querySelectorAll(`#${prefix}-cal-grid .cal-day`).forEach(el => {
                const d = parseLocalDate(el.dataset.iso);
                el.classList.toggle('cal-in-range', d >= lo && d <= hi);
            });
        }

        // Quick chips: ADD nights to the current selection (cumulative).
        // First click with nothing selected: check-in = today, check-out = today + N.
        // Clicking the same or another chip again extends the existing check-out
        // by N more days, so "+1 week" twice = 2 weeks total.
        function setNights(prefix, nights) {
            const inEl = document.getElementById(prefix + '-in');
            const outEl = document.getElementById(prefix + '-out');

            let checkIn = inEl.value ? parseLocalDate(inEl.value) : (() => { const d = new Date(); d.setHours(0,0,0,0); return d; })();
            let checkOut = outEl.value ? parseLocalDate(outEl.value) : new Date(checkIn);
            checkOut.setDate(checkOut.getDate() + nights);

            const fromIso = toLocalISO(checkIn);
            const toIso = toLocalISO(checkOut);
            inEl.value = fromIso;
            outEl.value = toIso;

            const state = calendars[prefix];
            if (state) {
                state.start = checkIn; state.end = checkOut; state.viewDate = new Date(checkOut);
                renderCalGeneric(prefix);
                document.getElementById(prefix + '-cal-label').textContent = `${fromIso} → ${toIso}`;
            }
            inEl.dispatchEvent(new Event('change'));
            outEl.dispatchEvent(new Event('change'));
        }

        // ===== ROOM AUTO-LOAD — instant, no debounce (discrete select/calendar actions) =====
        let roomPrices = {};

        async function doFindRooms(prefix) {
            const typeId = document.getElementById(prefix + '-type').value;
            const box = document.getElementById(prefix + '-rooms-box');
            const hiddenRoomId = document.getElementById(prefix + '-room-id');
            hiddenRoomId.value = '';

            if (!typeId) { box.innerHTML = ''; return; }

            box.innerHTML = '<div class="rooms-loading">Finding available rooms…</div>';

            let url = `{{ route('staff.rooms.available') }}?room_type_id=${encodeURIComponent(typeId)}`;
            const inVal = document.getElementById(prefix + '-in').value;
            const outVal = document.getElementById(prefix + '-out').value;
            if (inVal && outVal) url += `&check_in_date=${encodeURIComponent(inVal)}&check_out_date=${encodeURIComponent(outVal)}`;

            try {
                const res = await fetch(url);
                const rooms = await res.json();
                box.innerHTML = '';

                if (rooms.length === 0) {
                    box.innerHTML = '<div class="rooms-empty">No rooms available for this selection.</div>';
                    return;
                }

                rooms.forEach(r => {
                    roomPrices[r.id] = parseFloat(r.price_per_night);
                    const chip = document.createElement('div');
                    chip.className = 'room-chip';
                    chip.innerHTML = `<strong>Room ${r.room_number}</strong><span>${r.price_per_night} ETB/night</span>`;
                    chip.onclick = () => {
                        box.querySelectorAll('.room-chip').forEach(c => c.classList.remove('selected'));
                        chip.classList.add('selected');
                        hiddenRoomId.value = r.id;
                        if (prefix === 'ci') calculateCheckinPrice(); else calculateReservationPrice();
                    };
                    box.appendChild(chip);
                });
            } catch (e) {
                box.innerHTML = '<div class="rooms-empty">Could not load rooms. Try again.</div>';
            }
        }

        // ===== BAN CHECK (debounced — this one is genuinely continuous typing) =====
        const debouncedBanCheck = {};

        function onPhoneInput(prefix) {
            if (!debouncedBanCheck[prefix]) {
                debouncedBanCheck[prefix] = debounce(() => checkGuestBan(prefix), 250);
            }
            debouncedBanCheck[prefix]();
        }

        async function checkGuestBan(prefix) {
            const fullname = document.getElementById(prefix + '-fullname').value.trim();
            const phone = document.getElementById(prefix + '-phone').value.trim();
            const box = document.getElementById(prefix + '-ban-status');
            const flag = document.getElementById(prefix + '-banned-flag');

            if (!fullname || !phone) { box.className = 'ban-indicator'; box.style.display = 'none'; flag.value = 'false'; return; }

            box.className = 'ban-indicator checking';
            box.textContent = 'Checking guest status…';

            try {
                const res = await fetch(`{{ route('staff.guest.check') }}?fullname=${encodeURIComponent(fullname)}&phone_no=${encodeURIComponent(phone)}`);
                const data = await res.json();
                if (data.found && data.status === 'blacklisted') {
                    box.className = 'ban-indicator red';
                    box.textContent = 'This guest is banned and cannot proceed.';
                    flag.value = 'true';
                } else {
                    box.className = 'ban-indicator green';
                    box.textContent = data.found ? 'Guest recognized — clear to proceed.' : 'New guest — clear to proceed.';
                    flag.value = 'false';
                }
            } catch (e) { box.style.display = 'none'; }
        }

        // ===== CAMERA =====
        let activeStream = null;
        async function startCamera(videoId, btnId) {
            try {
                if (activeStream) activeStream.getTracks().forEach(t => t.stop());
                activeStream = await navigator.mediaDevices.getUserMedia({ video: true });
                const video = document.getElementById(videoId);
                video.srcObject = activeStream;
                video.style.display = 'block';
                video.play().catch(() => {});
                document.getElementById(btnId).disabled = false;
            } catch (e) { alert('Camera error: ' + e.message); }
        }
        function takeSnapshot(videoId, previewId, inputId) {
            const video = document.getElementById(videoId);
            const canvas = document.getElementById('main-canvas');
            const maxWidth = 480;
            const scale = Math.min(1, maxWidth / video.videoWidth);
            canvas.width = video.videoWidth * scale;
            canvas.height = video.videoHeight * scale;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const data = canvas.toDataURL('image/jpeg', 0.6);
            document.getElementById(inputId).value = data;
            const preview = document.getElementById(previewId);
            preview.src = data;
            preview.style.display = 'block';
            video.style.display = 'none';
            if (activeStream) activeStream.getTracks().forEach(t => t.stop());
        }

        // ===== PRICE CALC =====
        function calculateCheckinPrice() {
            const rid = document.getElementById('ci-room-id').value;
            const d1 = new Date(document.getElementById('ci-in').value);
            const d2 = new Date(document.getElementById('ci-out').value);
            const payWay = document.getElementById('ci-pay-way').value;
            const paidInput = document.getElementById('ci-paid');
            if (!rid || isNaN(d1) || isNaN(d2)) return;

            let nights = Math.round((d2 - d1) / 86400000);
            if (nights < 1) nights = 1;
            const total = nights * roomPrices[rid];

            const pLine = document.getElementById('ci-price-line');
            pLine.style.display = 'block';
            pLine.innerHTML = `${nights} night(s) × ${roomPrices[rid]} ETB = <strong>${total.toFixed(2)} ETB total</strong>`;

            if (payWay === 'full') { paidInput.value = total.toFixed(2); paidInput.readOnly = true; }
            else { paidInput.readOnly = false; }

            const paid = parseFloat(paidInput.value) || 0;
            const bal = total - paid;
            const bLine = document.getElementById('ci-bal-line');
            bLine.style.display = 'block';
            bLine.className = bal <= 0 ? 'bal-box bal-green' : 'bal-box bal-red';
            bLine.textContent = bal <= 0 ? 'Balance: Paid in Full' : `Balance Due: ${bal.toFixed(2)} ETB`;
        }

        function calculateReservationPrice() {
            const rid = document.getElementById('res-room-id').value;
            const d1 = new Date(document.getElementById('res-in').value);
            const d2 = new Date(document.getElementById('res-out').value);
            if (!rid || isNaN(d1) || isNaN(d2)) return;
            let nights = Math.round((d2 - d1) / 86400000);
            if (nights < 1) nights = 1;
            const total = nights * roomPrices[rid];
            const pLine = document.getElementById('res-price-line');
            pLine.style.display = 'block';
            pLine.innerHTML = `${nights} night(s) × ${roomPrices[rid]} ETB = <strong>${total.toFixed(2)} ETB total</strong>`;
        }

        // ===== CHECK OUT =====
        const CHECKOUT_SHOW_BASE = "{{ url('/staff/checkout/detail') }}";
        const RESERVATION_SHOW_BASE = "{{ url('/staff/reservation/detail') }}";

        async function loadCheckoutDetails(id) {
            try {
                const res = await fetch(`${CHECKOUT_SHOW_BASE}/${id}`);
                const data = await res.json();
                showCheckoutDetails(data);
            } catch (e) { alert('Could not load guest details.'); }
        }

        function showCheckoutDetails(r) {
            document.getElementById('co-details').style.display = 'block';
            document.getElementById('co-res-id').value = r.id;
            document.getElementById('co-name').textContent = r.guest.fullname;
            document.getElementById('co-phone').textContent = r.guest.phone_no;
            document.getElementById('co-room').textContent = r.room.room_number;
            document.getElementById('co-dates').textContent = `${r.check_in_date.split('T')[0]} to ${r.check_out_date.split('T')[0]}`;
            document.getElementById('co-img').src = r.guest.id_photo || '';

            const outDate = new Date(r.check_out_date);
            const today = new Date(); today.setHours(0,0,0,0);
            const overBox = document.getElementById('co-overstay');
            if (today > outDate) {
                const days = Math.ceil((today - outDate) / 86400000);
                overBox.textContent = `OVERSTAY ALERT: Guest is ${days} day(s) late!`;
                overBox.style.display = 'block';
            } else { overBox.style.display = 'none'; }

            const bBox = document.getElementById('co-balance');
            if (r.payment) {
                const rem = parseFloat(r.payment.remaining_amount);
                bBox.className = rem <= 0 ? 'bal-box bal-green' : 'bal-box bal-red';
                bBox.textContent = rem <= 0 ? 'Payment: Clear' : `Collect Payment: ${rem.toFixed(2)} ETB`;
            } else {
                bBox.className = 'bal-box bal-red';
                bBox.textContent = 'No payment record found.';
            }
            document.getElementById('co-results').style.display = 'none';
        }

        async function searchCheckout() {
            const q = document.getElementById('co-search').value;
            const results = document.getElementById('co-results');
            if (q.length < 2) { results.style.display = 'none'; return; }
            const res = await fetch(`{{ route('staff.checkout.search') }}?query=${encodeURIComponent(q)}`);
            const data = await res.json();
            results.innerHTML = '';
            if (data.length > 0) {
                results.style.display = 'block';
                data.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    div.textContent = `${r.guest.fullname} (${r.guest.phone_no}) — Room ${r.room.room_number}`;
                    div.onclick = () => showCheckoutDetails(r);
                    results.appendChild(div);
                });
            } else { results.style.display = 'none'; }
        }

        // ===== RESERVATION LIST =====
        async function loadReservationDetails(id) {
            try {
                const res = await fetch(`${RESERVATION_SHOW_BASE}/${id}`);
                const data = await res.json();
                showReservationDetails(data);
            } catch (e) { alert('Could not load reservation details.'); }
        }

        function showReservationDetails(r) {
            document.getElementById('rl-box').style.display = 'block';
            document.getElementById('rl-res-id').value = r.id;
            document.getElementById('rl-title').textContent = 'Arriving: ' + r.guest.fullname;
            document.getElementById('rl-phone').textContent = r.guest.phone_no;
            document.getElementById('rl-room').textContent = r.room.room_number;
            document.getElementById('rl-deposit').textContent = r.payment ? r.payment.amount_paid : '0.00';
            document.getElementById('rl-results').style.display = 'none';
        }

        async function searchReservation() {
            const q = document.getElementById('rl-search').value;
            const results = document.getElementById('rl-results');
            if (q.length < 2) { results.style.display = 'none'; return; }
            const res = await fetch(`{{ route('staff.reservation.search') }}?query=${encodeURIComponent(q)}`);
            const data = await res.json();
            results.innerHTML = '';
            if (data.length > 0) {
                results.style.display = 'block';
                data.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'search-item';
                    div.textContent = `${r.guest.fullname} (${r.guest.phone_no}) — Room ${r.room.room_number}`;
                    div.onclick = () => showReservationDetails(r);
                    results.appendChild(div);
                });
            } else { results.style.display = 'none'; }
        }

        // ===== BAN / UNBAN / CANCEL MODAL (shared) =====
        function openBanModal(guestId, guestName) {
            document.getElementById('banModalTitle').textContent = 'Ban ' + guestName + '?';
            document.getElementById('banModalText').textContent = 'This guest will be blocked from future check-ins. Enter your staff password to confirm.';
            document.getElementById('banModalGuestId').value = guestId;
            document.getElementById('banModalReservationId').value = '';
            document.getElementById('banModalForm').action = "{{ route('staff.guest.ban') }}";
            document.getElementById('banModalSubmit').textContent = 'Confirm Ban';
            document.getElementById('banModalOverlay').classList.add('open');
        }
        function openUnbanModal(guestId, guestName) {
            document.getElementById('banModalTitle').textContent = 'Unban ' + guestName + '?';
            document.getElementById('banModalText').textContent = 'This guest will be allowed to check in again. Enter your staff password to confirm.';
            document.getElementById('banModalGuestId').value = guestId;
            document.getElementById('banModalReservationId').value = '';
            document.getElementById('banModalForm').action = "{{ route('staff.guest.unban') }}";
            document.getElementById('banModalSubmit').textContent = 'Confirm Unban';
            document.getElementById('banModalOverlay').classList.add('open');
        }
        function openCancelReservationModal(reservationId, guestName) {
            document.getElementById('banModalTitle').textContent = 'Cancel reservation for ' + guestName + '?';
            document.getElementById('banModalText').textContent = 'This will cancel the reservation and free the room. Enter your staff password to confirm.';
            document.getElementById('banModalGuestId').value = '';
            document.getElementById('banModalReservationId').value = reservationId;
            document.getElementById('banModalForm').action = "{{ route('staff.reservation.cancel') }}";
            document.getElementById('banModalSubmit').textContent = 'Confirm Cancel';
            document.getElementById('banModalOverlay').classList.add('open');
        }
        function closeBanModal() {
            document.getElementById('banModalOverlay').classList.remove('open');
        }
    </script>
</body>
</html>
