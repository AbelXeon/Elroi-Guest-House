<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard — Elroi Guest House</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>
    <style>
        :root{
            --ink:#1c2b29;
            --panel:#20342f;
            --accent:#3f6b52;
            --accent-light:#57876a;
            --accent-soft:#e7efe9;
            --bg:#f6f5f2;
            --card-bg:#ffffff;
            --border:#e0ded7;
            --text:#26312e;
            --muted:#6b756f;
            --error:#b3413a;
            --error-bg:#fbeceb;
            --warn:#c98a2c;
            --shadow:0 2px 12px rgba(28,43,41,0.06);
        }

        *{ margin:0; padding:0; box-sizing:border-box; font-family:'Inter', sans-serif; }
        html,body{ height:100%; }

        body{
            background:var(--bg);
            color:var(--text);
            display:flex;
            height:100vh;
            overflow:hidden;
        }

        h1,h2,h3{ font-family:'Fraunces', serif; font-weight:500; color:var(--ink); }

        /* ================= SIDEBAR (desktop) ================= */
        .sidebar{
            width:240px;
            flex-shrink:0;
            background:linear-gradient(160deg, var(--ink) 0%, var(--panel) 100%);
            color:#f4f2ec;
            display:flex;
            flex-direction:column;
            padding:26px 0;
            height:100vh;
            overflow-y:auto;
        }
        .brand{
            display:flex;
            align-items:center;
            gap:10px;
            padding:0 22px 24px;
            border-bottom:1px solid rgba(244,242,236,0.1);
            margin-bottom:16px;
        }
        .brand span{ font-family:'Fraunces', serif; font-weight:600; font-size:16px; }

        .nav-link{
            display:flex;
            align-items:center;
            gap:12px;
            padding:12px 22px;
            color:#c7cec8;
            text-decoration:none;
            cursor:pointer;
            font-size:14px;
            border-left:3px solid transparent;
            transition:background .15s ease, color .15s ease;
        }
        .nav-link svg{ flex-shrink:0; opacity:.85; }
        .nav-link.active, .nav-link:hover{
            background:rgba(244,242,236,0.06);
            color:#fff;
            border-left-color:var(--accent-light);
        }

        .sidebar-footer{ margin-top:auto; padding:0 22px; }
        .logout-btn{
            width:100%;
            padding:11px;
            background:rgba(179,65,58,0.15);
            border:1px solid rgba(179,65,58,0.35);
            border-radius:8px;
            color:#f2a9a4;
            font-size:13.5px;
            font-weight:600;
            cursor:pointer;
            transition:background .15s ease;
        }
        .logout-btn:hover{ background:rgba(179,65,58,0.28); }

        /* ================= MAIN ================= */
        .main{
            flex:1;
            height:100vh;
            overflow-y:auto;
            padding:32px 36px 100px;
        }

        .topline{
            display:flex;
            justify-content:space-between;
            align-items:baseline;
            margin-bottom:22px;
            flex-wrap:wrap;
            gap:10px;
        }
        .topline h1{ font-size:26px; }
        .topline .datestamp{ font-size:13px; color:var(--muted); }

        .success-msg{
            background:var(--accent-soft);
            color:var(--accent);
            padding:12px 16px;
            border-left:4px solid var(--accent);
            border-radius:7px;
            margin-bottom:20px;
            font-size:14px;
        }
        ul.errors{
            background:var(--error-bg);
            color:var(--error);
            padding:14px 18px;
            border-left:4px solid var(--error);
            border-radius:7px;
            margin-bottom:20px;
            list-style:none;
            font-size:14px;
        }

        .panel{ display:none; }
        .panel.active{ display:block; }

        /* ---- Stat cards ---- */
        .stat-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(170px, 1fr));
            gap:14px;
            margin-bottom:26px;
        }
        .stat-card{
            background:var(--card-bg);
            border:1px solid var(--border);
            border-radius:12px;
            padding:18px 18px 16px;
            box-shadow:var(--shadow);
        }
        .stat-card .label{
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:0.08em;
            color:var(--muted);
            margin-bottom:8px;
        }
        .stat-card .value{
            font-family:'Fraunces', serif;
            font-size:24px;
            color:var(--ink);
            font-weight:600;
        }
        .stat-card .sub{ font-size:12px; color:var(--muted); margin-top:4px; }
        .stat-card.accent{ border-color:var(--accent); background:var(--accent-soft); }
        .stat-card.accent .value{ color:var(--accent); }

        /* ---- Charts ---- */
        .chart-grid{
            display:grid;
            grid-template-columns:1.4fr 1fr;
            gap:16px;
            margin-bottom:10px;
        }
        .chart-card{
            background:var(--card-bg);
            border:1px solid var(--border);
            border-radius:12px;
            padding:20px;
            box-shadow:var(--shadow);
        }
        .chart-card h3{ font-size:15px; margin-bottom:14px; }
        .chart-card canvas{ max-height:230px; }
        .chart-row{ display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px; }

        /* ---- Cards / forms ---- */
        .card{
            background:var(--card-bg);
            border:1px solid var(--border);
            border-radius:12px;
            padding:24px;
            box-shadow:var(--shadow);
            margin-bottom:20px;
        }
        .card h2{ font-size:18px; margin-bottom:4px; }
        .card .hint{ font-size:12.5px; color:var(--muted); margin-bottom:18px; }

        .form-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:16px; align-items:end; }

        .field{ position:relative; }
        .field-label{
            display:block;
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:0.06em;
            color:var(--muted);
            margin-bottom:6px;
        }
        input[type="text"], input[type="number"], input[type="password"], select{
            width:100%;
            padding:11px 12px;
            font-size:14px;
            color:var(--text);
            background:#fff;
            border:1px solid var(--border);
            border-radius:8px;
            outline:none;
            transition:border-color .15s ease;
        }
        input:focus, select:focus{ border-color:var(--accent); }

        button, .btn{
            padding:11px 18px;
            background:var(--accent);
            border:none;
            border-radius:8px;
            color:#fff;
            font-weight:600;
            font-size:13.5px;
            cursor:pointer;
            transition:background .15s ease, transform .1s ease;
        }
        button:hover, .btn:hover{ background:var(--accent-light); }
        button:active{ transform:translateY(1px); }
        .btn-danger{ background:var(--error); }
        .btn-danger:hover{ background:#c65750; }
        .btn-ghost{
            background:transparent;
            border:1px solid var(--border);
            color:var(--text);
        }
        .btn-ghost:hover{ background:#f1f0ec; }

        .radio-row{ display:flex; gap:18px; align-items:center; margin-bottom:14px; }
        .radio-row label{ display:flex; align-items:center; gap:6px; font-size:13.5px; cursor:pointer; }

        /* ---- Filter bar ---- */
        .filter-bar{
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            align-items:center;
            margin-bottom:18px;
        }
        .filter-bar input, .filter-bar select{ max-width:220px; }
        .clear-link{ font-size:12.5px; color:var(--muted); text-decoration:underline; }

        /* ---- Room / staff grids ---- */
        .room-grid, .staff-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(230px, 1fr));
            gap:14px;
            margin-bottom:18px;
        }
        .item-card{
            background:var(--card-bg);
            border:1px solid var(--border);
            border-radius:10px;
            padding:16px;
            box-shadow:var(--shadow);
        }
        .item-card .top-row{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
        .item-card .room-no{ font-family:'Fraunces', serif; font-size:17px; color:var(--ink); font-weight:600; }
        .item-card .floor-tag{ font-size:11px; color:var(--muted); }

        .status-pill{
            display:inline-block;
            font-size:10.5px;
            text-transform:uppercase;
            letter-spacing:0.04em;
            padding:3px 9px;
            border-radius:20px;
            font-weight:600;
        }
        .status-available{ background:#e2f3e8; color:#2b7a4b; }
        .status-booked{ background:#eaeef7; color:#3855a8; }
        .status-reserved{ background:#fdf1de; color:#a9700f; }
        .status-maintenance{ background:#fbeceb; color:var(--error); }
        .status-cleaning{ background:#eef0f0; color:#616a67; }

        .item-card .mini-field{ margin-bottom:8px; }
        .item-card .mini-field label{ font-size:10.5px; text-transform:uppercase; color:var(--muted); display:block; margin-bottom:3px; }
        .item-card select, .item-card input{ padding:8px 9px; font-size:13px; }
        .item-card .actions{ display:flex; gap:8px; margin-top:10px; }
        .item-card .actions button{ flex:1; padding:8px; font-size:12.5px; }

        .staff-avatar{
            width:38px; height:38px; border-radius:50%;
            background:var(--accent-soft); color:var(--accent);
            display:flex; align-items:center; justify-content:center;
            font-weight:700; font-size:14px; margin-bottom:10px;
        }
        .staff-name{ font-weight:600; font-size:14.5px; color:var(--ink); }
        .staff-username{ font-size:12.5px; color:var(--muted); margin-bottom:10px; }

        .empty-note{ color:var(--muted); font-size:13.5px; padding:20px 0; }

        /* ---- Pagination ---- */
        .pagination{ display:flex; align-items:center; gap:12px; justify-content:center; margin-top:6px; }
        .page-btn{
            padding:8px 14px;
            border:1px solid var(--border);
            border-radius:7px;
            font-size:13px;
            color:var(--text);
            text-decoration:none;
            background:#fff;
        }
        .page-btn:hover{ background:#f1f0ec; }
        .page-btn.disabled{ color:#c3c1ba; cursor:default; }
        .page-info{ font-size:12.5px; color:var(--muted); }

        /* ================= MOBILE ================= */
        .bottom-nav{ display:none; }

        @media (max-width:900px){
            body{ display:block; height:auto; overflow:visible; }
            .sidebar{ display:none; }

            .main{
                height:auto;
                overflow:visible;
                padding:20px 16px 90px;
            }

            .mobile-topbar{
                display:flex;
                align-items:center;
                gap:10px;
                margin-bottom:18px;
            }
            .mobile-topbar span{ font-family:'Fraunces', serif; font-weight:600; font-size:16px; color:var(--ink); }

            .chart-grid, .chart-row{ grid-template-columns:1fr; }
            .stat-grid{ grid-template-columns:repeat(2, 1fr); }

            .bottom-nav{
                display:flex;
                position:fixed;
                bottom:0; left:0; right:0;
                background:var(--ink);
                padding:8px 4px calc(8px + env(safe-area-inset-bottom));
                z-index:50;
                box-shadow:0 -4px 16px rgba(0,0,0,0.15);
            }
            .bottom-nav .nav-link{
                flex:1;
                flex-direction:column;
                gap:4px;
                padding:6px 2px;
                font-size:10.5px;
                text-align:center;
                border-left:none;
                border-radius:8px;
            }
            .bottom-nav .nav-link.active{ background:rgba(244,242,236,0.1); color:#fff; }
            .bottom-nav form{ flex:1; }
            .bottom-nav .logout-btn-mobile{
                width:100%;
                background:none;
                border:none;
                color:#c7cec8;
                display:flex;
                flex-direction:column;
                align-items:center;
                gap:4px;
                font-size:10.5px;
                padding:6px 2px;
                cursor:pointer;
            }
        }

        @media (max-width:480px){
            .stat-grid{ grid-template-columns:1fr 1fr; }
            .room-grid, .staff-grid{ grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

    <!-- ================= DESKTOP SIDEBAR ================= -->
    <div class="sidebar">
        <div class="brand">
            <svg width="24" height="24" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="7" fill="#3f6b52"/>
                <path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
            </svg>
            <span>Elroi Guest House</span>
        </div>

        <a class="nav-link" data-panel="overview" onclick="showPanel('overview')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/></svg>
            Overview
        </a>
        <a class="nav-link" data-panel="rooms" onclick="showPanel('rooms')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="10" width="18" height="9" rx="1"/><path d="M3 10V7a2 2 0 0 1 2-2h6v5"/><circle cx="15" cy="14" r="1"/></svg>
            Rooms
        </a>
        <a class="nav-link" data-panel="staff" onclick="showPanel('staff')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
            Staff
        </a>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- ================= MAIN ================= -->
    <div class="main" id="mainContent">

        <div class="mobile-topbar">
            <svg width="22" height="22" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="7" fill="#3f6b52"/>
                <path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
            </svg>
            <span>Elroi Guest House</span>
        </div>

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

        <!-- ============ OVERVIEW PANEL ============ -->
        <div id="panel-overview" class="panel active">
            <div class="topline">
                <h1>Overview</h1>
                <span class="datestamp">{{ now()->format('l, F j, Y') }}</span>
            </div>

            <div class="stat-grid">
                <div class="stat-card accent">
                    <div class="label">Today's Income</div>
                    <div class="value">{{ number_format($incomeStats['today'], 2) }}</div>
                    <div class="sub">ETB</div>
                </div>
                <div class="stat-card">
                    <div class="label">Last 7 Days</div>
                    <div class="value">{{ number_format($incomeStats['week'], 2) }}</div>
                    <div class="sub">ETB</div>
                </div>
                <div class="stat-card">
                    <div class="label">This Month</div>
                    <div class="value">{{ number_format($incomeStats['month'], 2) }}</div>
                    <div class="sub">ETB</div>
                </div>
                <div class="stat-card">
                    <div class="label">This Year</div>
                    <div class="value">{{ number_format($incomeStats['year'], 2) }}</div>
                    <div class="sub">ETB</div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Rooms</div>
                    <div class="value">{{ $roomStats['total'] }}</div>
                    <div class="sub">{{ $roomStats['available'] }} available</div>
                </div>
                <div class="stat-card">
                    <div class="label">Staff Members</div>
                    <div class="value">{{ $staffCount }}</div>
                    <div class="sub">active accounts</div>
                </div>
            </div>

            <div class="chart-grid">
                <div class="chart-card">
                    <h3>Income — Last 7 Days</h3>
                    <canvas id="incomeChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Room Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="chart-row">
                <div class="chart-card">
                    <h3>Rooms by Type</h3>
                    <canvas id="typeChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Quick Snapshot</h3>
                    <div class="stat-grid" style="grid-template-columns:1fr 1fr; margin-bottom:0;">
                        <div>
                            <div class="label" style="margin-bottom:4px;">Booked</div>
                            <div class="value" style="font-size:19px;">{{ $roomStats['booked'] }}</div>
                        </div>
                        <div>
                            <div class="label" style="margin-bottom:4px;">Reserved</div>
                            <div class="value" style="font-size:19px;">{{ $roomStats['reserved'] }}</div>
                        </div>
                        <div>
                            <div class="label" style="margin-bottom:4px;">Maintenance</div>
                            <div class="value" style="font-size:19px;">{{ $roomStats['maintenance'] }}</div>
                        </div>
                        <div>
                            <div class="label" style="margin-bottom:4px;">Cleaning</div>
                            <div class="value" style="font-size:19px;">{{ $roomStats['cleaning'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ ROOMS PANEL ============ -->
        <div id="panel-rooms" class="panel">
            <div class="topline"><h1>Rooms</h1></div>

            <div class="card">
                <h2>Batch Create Rooms</h2>
                <p class="hint">Generate a sequential run of rooms in one go.</p>
                <form action="{{ route('rooms.batchStore') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_page" value="{{ $rooms->currentPage() }}">
                    <input type="hidden" name="return_q" value="{{ request('room_q') }}">
                    <input type="hidden" name="return_status" value="{{ request('room_status') }}">

                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Floor Number</label>
                            <input type="number" name="floor_number">
                        </div>
                        <div class="field">
                            <label class="field-label">Start Room No</label>
                            <input type="number" name="start_room_no" required>
                        </div>
                        <div class="field">
                            <label class="field-label">End Room No</label>
                            <input type="number" name="end_room_no" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Room Type</label>
                            <select name="room_type_id" required>
                                <option value="">-- select --</option>
                                @foreach ($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Price / Night</label>
                            <input type="number" step="0.01" name="price_per_night" required>
                        </div>
                        <div class="field">
                            <button type="submit">Generate Rooms</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <h2>Bulk Price Update</h2>
                <p class="hint">Change the price for every room at once, or just one room type — no need to edit them one by one.</p>
                <form action="{{ route('rooms.bulkPrice') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_page" value="{{ $rooms->currentPage() }}">
                    <input type="hidden" name="return_q" value="{{ request('room_q') }}">
                    <input type="hidden" name="return_status" value="{{ request('room_status') }}">

                    <div class="radio-row">
                        <label><input type="radio" name="scope" value="all" checked onchange="toggleBulkType()"> All Rooms</label>
                        <label><input type="radio" name="scope" value="type" onchange="toggleBulkType()"> By Room Type</label>
                    </div>

                    <div class="form-grid">
                        <div class="field" id="bulkTypeWrap" style="display:none;">
                            <label class="field-label">Room Type</label>
                            <select name="room_type_id">
                                <option value="">-- select --</option>
                                @foreach ($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">New Price / Night</label>
                            <input type="number" step="0.01" name="price_per_night" required>
                        </div>
                        <div class="field">
                            <button type="submit" onclick="return confirm('Apply this price change now?')">Apply Price Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar">
                <input type="hidden" name="panel" value="rooms">
                <input type="text" name="room_q" placeholder="Search room number..." value="{{ request('room_q') }}">
                <select name="room_status">
                    <option value="">All Statuses</option>
                    @foreach (['available','booked','reserved','maintenance','cleaning'] as $s)
                        <option value="{{ $s }}" @selected(request('room_status') == $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-ghost">Filter</button>
                @if(request('room_q') || request('room_status'))
                    <a href="{{ route('admin.dashboard', ['panel'=>'rooms']) }}" class="clear-link">Clear filters</a>
                @endif
            </form>

            <div class="room-grid">
                @forelse ($rooms as $room)
                    <div class="item-card">
                        <div class="top-row">
                            <span class="room-no">Room {{ $room->room_number }}</span>
                            <span class="status-pill status-{{ $room->status }}">{{ $room->status }}</span>
                        </div>
                        <div class="floor-tag" style="margin-bottom:10px;">Floor: {{ $room->floor_number ?? '—' }}</div>

                        <form action="{{ route('rooms.update', $room->id) }}" method="POST" id="room-form-{{ $room->id }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="return_page" value="{{ $rooms->currentPage() }}">
                            <input type="hidden" name="return_q" value="{{ request('room_q') }}">
                            <input type="hidden" name="return_status" value="{{ request('room_status') }}">
                        </form>

                        <div class="mini-field">
                            <label>Type</label>
                            <select name="room_type_id" form="room-form-{{ $room->id }}">
                                @foreach ($roomTypes as $type)
                                    <option value="{{ $type->id }}" @selected($room->room_type_id == $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mini-field">
                            <label>Price / Night</label>
                            <input type="number" step="0.01" name="price_per_night" value="{{ $room->price_per_night }}" form="room-form-{{ $room->id }}">
                        </div>
                        <div class="mini-field">
                            <label>Status</label>
                            <select name="status" form="room-form-{{ $room->id }}">
                                @foreach (['available','booked','reserved','maintenance','cleaning'] as $status)
                                    <option value="{{ $status }}" @selected($room->status == $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="actions">
                            <button type="submit" form="room-form-{{ $room->id }}">Save</button>
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" style="flex:1;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="return_page" value="{{ $rooms->currentPage() }}">
                                <input type="hidden" name="return_q" value="{{ request('room_q') }}">
                                <input type="hidden" name="return_status" value="{{ request('room_status') }}">
                                <button type="submit" class="btn-danger" style="width:100%;" onclick="return confirm('Delete this room?')">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="empty-note">No rooms match your filters yet.</p>
                @endforelse
            </div>

            @if ($rooms->hasPages())
                <div class="pagination">
                    @if ($rooms->onFirstPage())
                        <span class="page-btn disabled">Prev</span>
                    @else
                        <a href="{{ $rooms->previousPageUrl() }}" class="page-btn">Prev</a>
                    @endif

                    <span class="page-info">Page {{ $rooms->currentPage() }} of {{ $rooms->lastPage() }} &middot; {{ $rooms->total() }} rooms</span>

                    @if ($rooms->hasMorePages())
                        <a href="{{ $rooms->nextPageUrl() }}" class="page-btn">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
                </div>
            @endif
        </div>

        <!-- ============ STAFF PANEL ============ -->
        <div id="panel-staff" class="panel">
            <div class="topline"><h1>Staff</h1></div>

            <div class="card">
                <h2>Create Staff Member</h2>
                <p class="hint">They'll use this username and password to log into the staff dashboard.</p>
                <form action="{{ route('staff.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="return_page" value="{{ $staff->currentPage() }}">
                    <input type="hidden" name="return_q" value="{{ request('staff_q') }}">

                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Full Name</label>
                            <input type="text" name="fullname" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Username</label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Password</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="field">
                            <button type="submit">Create Staff</button>
                        </div>
                    </div>
                </form>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-bar">
                <input type="hidden" name="panel" value="staff">
                <input type="text" name="staff_q" placeholder="Search name or username..." value="{{ request('staff_q') }}">
                <button type="submit" class="btn-ghost">Search</button>
                @if(request('staff_q'))
                    <a href="{{ route('admin.dashboard', ['panel'=>'staff']) }}" class="clear-link">Clear</a>
                @endif
            </form>

            <div class="staff-grid">
                @forelse ($staff as $member)
                    <div class="item-card">
                        <div class="staff-avatar">{{ strtoupper(substr($member->fullname, 0, 1)) }}</div>
                        <div class="staff-name">{{ $member->fullname }}</div>
                        <div class="staff-username">{{ '@' . $member->username }}</div>
                        <form action="{{ route('staff.destroy', $member->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="return_page" value="{{ $staff->currentPage() }}">
                            <input type="hidden" name="return_q" value="{{ request('staff_q') }}">
                            <button type="submit" class="btn-danger" style="width:100%;" onclick="return confirm('Remove this staff member?')">Remove</button>
                        </form>
                    </div>
                @empty
                    <p class="empty-note">No staff members match your search yet.</p>
                @endforelse
            </div>

            @if ($staff->hasPages())
                <div class="pagination">
                    @if ($staff->onFirstPage())
                        <span class="page-btn disabled">Prev</span>
                    @else
                        <a href="{{ $staff->previousPageUrl() }}" class="page-btn">Prev</a>
                    @endif

                    <span class="page-info">Page {{ $staff->currentPage() }} of {{ $staff->lastPage() }} &middot; {{ $staff->total() }} staff</span>

                    @if ($staff->hasMorePages())
                        <a href="{{ $staff->nextPageUrl() }}" class="page-btn">Next</a>
                    @else
                        <span class="page-btn disabled">Next</span>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- ================= MOBILE BOTTOM NAV ================= -->
    <div class="bottom-nav">
        <a class="nav-link" data-panel="overview" onclick="showPanel('overview')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/></svg>
            Overview
        </a>
        <a class="nav-link" data-panel="rooms" onclick="showPanel('rooms')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="10" width="18" height="9" rx="1"/><path d="M3 10V7a2 2 0 0 1 2-2h6v5"/><circle cx="15" cy="14" r="1"/></svg>
            Rooms
        </a>
        <a class="nav-link" data-panel="staff" onclick="showPanel('staff')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
            Staff
        </a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn-mobile">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                Logout
            </button>
        </form>
    </div>


    @php
    $roomStatusData = [
        $roomStats['available'],
        $roomStats['booked'],
        $roomStats['reserved'],
        $roomStats['maintenance'],
        $roomStats['cleaning'],
    ];
@endphp

    <script>
        // ---- Panel switching (shared by desktop sidebar + mobile bottom nav) ----
        function showPanel(name, updateUrl = true) {
            document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(a => a.classList.remove('active'));

            const panelEl = document.getElementById('panel-' + name);
            if (panelEl) panelEl.classList.add('active');
            document.querySelectorAll('.nav-link[data-panel="' + name + '"]').forEach(a => a.classList.add('active'));

            sessionStorage.setItem('activePanel', name);

            if (updateUrl) {
                const url = new URL(window.location);
                url.searchParams.set('panel', name);
                window.history.replaceState({}, '', url);
            }
        }

        function toggleBulkType() {
            const scope = document.querySelector('input[name="scope"]:checked').value;
            document.getElementById('bulkTypeWrap').style.display = (scope === 'type') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Restore active panel: URL param wins, then sessionStorage, then default
            const params = new URLSearchParams(window.location.search);
            const panel = params.get('panel') || sessionStorage.getItem('activePanel') || 'overview';
            showPanel(panel, false);

            // Restore scroll position on desktop main content
            const main = document.getElementById('mainContent');
            const savedScroll = sessionStorage.getItem('scrollPos');
            if (savedScroll) main.scrollTop = parseInt(savedScroll, 10);
            main.addEventListener('scroll', () => {
                sessionStorage.setItem('scrollPos', main.scrollTop);
            });

            // ---- Charts ----
            const incomeLabels = @json($incomeLabels);
            const incomeData = @json($incomeData);
const roomStatusData = @json($roomStatusData);
            const typeLabels = @json($roomsByType->pluck('name'));
            const typeData = @json($roomsByType->pluck('count'));

            new Chart(document.getElementById('incomeChart'), {
                type: 'line',
                data: {
                    labels: incomeLabels,
                    datasets: [{
                        label: 'Income (ETB)',
                        data: incomeData,
                        borderColor: '#3f6b52',
                        backgroundColor: 'rgba(63,107,82,0.12)',
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#3f6b52',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Available', 'Booked', 'Reserved', 'Maintenance', 'Cleaning'],
                    datasets: [{
                        data: roomStatusData,
                        backgroundColor: ['#3f6b52', '#3855a8', '#c98a2c', '#b3413a', '#8a938f']
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
            });

            new Chart(document.getElementById('typeChart'), {
                type: 'bar',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        label: 'Rooms',
                        data: typeData,
                        backgroundColor: '#57876a',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>

</body>
</html>