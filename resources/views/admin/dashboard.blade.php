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

    <!-- DataTables + Buttons (export) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

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

        /* ================= SIDEBAR ================= */
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

        /* ---- Recent activity ---- */
        .list-header{ display:flex; justify-content:space-between; align-items:center; margin:26px 0 12px; flex-wrap:wrap; gap:10px; }
        .list-header h3{ font-size:16px; }
        .plain-table-wrap{ background:var(--card-bg); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); overflow-x:auto; }
        table.plain-table{ width:100%; border-collapse:collapse; min-width:640px; }
        table.plain-table thead th{ background:var(--ink); color:#f4f2ec; padding:11px 12px; text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:.05em; }
        table.plain-table tbody td{ padding:10px 12px; font-size:13px; border-bottom:1px solid var(--border); }
        table.plain-table tbody tr:last-child td{ border-bottom:none; }
        table.plain-table tbody tr:hover{ background:var(--accent-soft); }

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

        .form-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:16px; align-items:end; }
        .field-actions{ grid-column:1 / -1; display:flex; gap:8px; flex-wrap:wrap; }

        .field{ position:relative; }
        .field-label{
            display:block;
            font-size:11px;
            text-transform:uppercase;
            letter-spacing:0.06em;
            color:var(--muted);
            margin-bottom:6px;
        }
        input[type="text"], input[type="number"], input[type="password"], input[type="date"], select{
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
            white-space:nowrap;
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
        .status-checked_in{ background:#e2f3e8; color:#2b7a4b; }
        .status-checked_out{ background:#eef0f0; color:#616a67; }
        .status-cancelled{ background:#fbeceb; color:var(--error); }

        /* ---- DataTables theming ---- */
        table.dataTable{ border-collapse:collapse !important; width:100% !important; margin-top:12px !important; }
        table.dataTable thead th{
            background:var(--ink); color:#f4f2ec; font-size:11.5px; text-transform:uppercase;
            letter-spacing:.04em; padding:12px !important; border-bottom:none !important;
        }
        table.dataTable tbody td{ padding:11px 12px !important; font-size:13.5px; border-bottom:1px solid var(--border) !important; }
        table.dataTable tbody tr:hover{ background:var(--accent-soft); }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select{
            border:1px solid var(--border); border-radius:7px; padding:7px 10px; margin-left:6px;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            border-radius:6px !important; padding:5px 11px !important; margin-left:3px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current{
            background:var(--accent) !important; color:#fff !important; border:none !important;
        }
        .dt-buttons{ margin-bottom:10px; }
        .dt-buttons .dt-button{
            background:var(--accent) !important; color:#fff !important; border:none !important;
            border-radius:7px !important; padding:8px 14px !important; font-size:12.5px !important;
            font-weight:600 !important; margin-right:6px !important;
        }
        .dt-buttons .dt-button:hover{ background:var(--accent-light) !important; }
        table.dataTable td .btn-ghost, table.dataTable td .btn-danger{ padding:6px 10px; font-size:12px; margin-right:4px; }

        .empty-note{ color:var(--muted); font-size:13.5px; padding:20px 0; }

        /* ---- Custom calendar range picker ---- */
        .calendar-popup{
            position:absolute; top:calc(100% + 6px); left:0; z-index:60;
            background:#fff; border:1px solid var(--border); border-radius:12px;
            box-shadow:0 10px 30px rgba(0,0,0,.15); padding:14px; width:270px;
        }
        .cal-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; font-size:13.5px; font-weight:600; color:var(--ink); }
        .cal-header button{ padding:4px 9px; background:var(--accent-soft); color:var(--accent); font-size:14px; }
        .cal-grid{ display:grid; grid-template-columns:repeat(7, 1fr); gap:2px; }
        .cal-dow{ font-size:10px; text-transform:uppercase; color:var(--muted); text-align:center; padding:4px 0; }
        .cal-day{ text-align:center; padding:7px 0; font-size:12.5px; border-radius:6px; cursor:pointer; color:var(--text); }
        .cal-day:hover{ background:var(--accent-soft); }
        .cal-day.cal-in-range{ background:var(--accent-soft); color:var(--accent); }
        .cal-day.cal-selected{ background:var(--accent); color:#fff; font-weight:700; }
        .cal-day.cal-today{ box-shadow:inset 0 0 0 2px var(--accent); font-weight:700; }
        .cal-footer{ margin-top:10px; font-size:12px; color:var(--muted); text-align:center; }
        .range-field-wrap{ position:relative; }

        /* ---- Mobile overflow hardening ---- */
        html, body{ overflow-x:hidden; }
        .dataTables_wrapper{ overflow-x:auto; -webkit-overflow-scrolling:touch; width:100%; }
        table.dataTable{ min-width:640px; }
        .dt-buttons{ display:flex; flex-wrap:wrap; }
        .form-grid, .field{ min-width:0; }
        input, select{ min-width:0; }

        /* ---- Toast notifications ---- */
        .toast-container{ position:fixed; bottom:22px; right:22px; z-index:500; display:flex; flex-direction:column; gap:10px; }
        .toast{
            background:var(--ink); color:#f4f2ec; padding:13px 18px; border-radius:10px;
            box-shadow:0 8px 24px rgba(0,0,0,.25); min-width:220px; max-width:320px;
            font-size:13.5px; position:relative; overflow:hidden; animation:toastIn .3s ease;
        }
        .toast.success{ border-left:4px solid var(--accent); }
        .toast.error{ border-left:4px solid var(--error); }
        .toast.info{ border-left:4px solid var(--warn); }
        .toast .toast-bar{ position:absolute; bottom:0; left:0; height:3px; background:rgba(244,242,236,0.35); animation:toastBar 3s linear forwards; }
        @keyframes toastIn{ from{ opacity:0; transform:translateX(30px); } to{ opacity:1; transform:translateX(0); } }
        @keyframes toastOut{ from{ opacity:1; transform:translateX(0); } to{ opacity:0; transform:translateX(30px); } }
        @keyframes toastBar{ from{ width:100%; } to{ width:0%; } }

        /* ================= MOBILE: hamburger + off-canvas sidebar ================= */
        .hamburger-btn{ display:none; background:none; border:none; color:var(--ink); cursor:pointer; padding:6px; }
        .sidebar-overlay{ display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:39; }
        .sidebar-overlay.open{ display:block; }

        @media (max-width:900px){
            body{ display:block; height:auto; overflow:visible; }

            .sidebar{
                position:fixed; top:0; left:0; z-index:40;
                width:260px; transform:translateX(-100%);
                transition:transform .25s ease;
            }
            .sidebar.open{ transform:translateX(0); }

            .hamburger-btn{ display:inline-flex; align-items:center; }

            .main{
                height:auto;
                overflow-y:visible;
                overflow-x:hidden;
                max-width:100vw;
                padding:20px 16px 40px;
            }

            .mobile-topbar{
                display:flex;
                align-items:center;
                gap:12px;
                margin-bottom:18px;
                max-width:100%;
            }
            .mobile-topbar span{
                font-family:'Fraunces', serif; font-weight:600; font-size:16px; color:var(--ink);
                overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
            }

            .chart-grid, .chart-row{ grid-template-columns:1fr; }
            .stat-grid{ grid-template-columns:repeat(2, 1fr); }
        }

        @media (max-width:480px){
            .stat-grid{ grid-template-columns:1fr 1fr; }
            .calendar-popup{ width:230px; }
        }
    </style>
</head>
<body>

    <!-- ================= SIDEBAR ================= -->
    <div class="sidebar" id="sidebar">
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
            Manage Rooms
        </a>
        <a class="nav-link" data-panel="staff" onclick="showPanel('staff')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/></svg>
            Manage Staff
        </a>
        <a class="nav-link" data-panel="reports" onclick="showPanel('reports')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>
            Reports
        </a>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ================= MAIN ================= -->
    <div class="main" id="mainContent">

        <div class="mobile-topbar">
            <button type="button" class="hamburger-btn" onclick="toggleSidebar()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            </button>
            <svg width="22" height="22" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="7" fill="#3f6b52"/>
                <path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
            </svg>
            <span>Elroi Guest House</span>
        </div>

        @if (session('success'))
            <script>document.addEventListener('DOMContentLoaded', () => showToast(@json(session('success')), 'success'));</script>
        @endif
        @if ($errors->any())
            <script>document.addEventListener('DOMContentLoaded', () => showToast(@json($errors->first()), 'error'));</script>
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

            <div class="list-header">
                <h3>Recent Activity</h3>
                <button type="button" class="btn-ghost" onclick="showPanel('reports')">View All Reports</button>
            </div>
            <div class="plain-table-wrap">
                <table class="plain-table">
                    <thead>
                        <tr>
                            <th>Staff</th><th>Guest</th><th>Room</th><th>Price/Night</th>
                            <th>Check-in</th><th>Check-out</th><th>Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentReservations as $r)
                            <tr>
                                <td>{{ $r->user->fullname ?? '—' }}</td>
                                <td>{{ $r->guest->fullname ?? '—' }}</td>
                                <td>{{ $r->room->room_number ?? '—' }}</td>
                                <td>{{ number_format($r->room->price_per_night ?? 0, 2) }}</td>
                                <td>{{ optional($r->check_in_date)->format('M j') }}</td>
                                <td>{{ optional($r->check_out_date)->format('M j') }}</td>
                                <td>{{ $r->payment ? number_format($r->payment->remaining_amount, 2) : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center; color:var(--muted);">No activity yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============ ROOMS PANEL ============ -->
        <div id="panel-rooms" class="panel">
            <div class="topline"><h1>Manage Rooms</h1></div>

            <!-- Edit Room (hidden until Edit is clicked) -->
            <div class="card" id="editRoomCard" style="display:none;">
                <h2>Edit Room</h2>
                <p class="hint">Update this room, then save. This form clears itself after saving.</p>
                <form id="editRoomForm" method="POST" onsubmit="showToast('Saving changes...', 'info', true)">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Room Number</label>
                            <input type="text" name="room_number" id="edit_room_number" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Room Type</label>
                            <input type="text" name="room_type_name" id="edit_room_type" list="roomTypeSuggestions" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Price / Night</label>
                            <input type="number" step="0.01" name="price_per_night" id="edit_price_per_night" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Status</label>
                            <select name="status" id="edit_status" required>
                                @foreach (['available','booked','reserved','maintenance','cleaning'] as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field-actions">
                            <button type="submit">Save Changes</button>
                            <button type="button" class="btn-ghost" onclick="cancelEditRoom()">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <h2>Create Rooms</h2>
                <p class="hint">Type a room type name (new or existing), a starting room number like G001 or F101, and how many to generate.</p>
                <form action="{{ route('rooms.batchStore') }}" method="POST" onsubmit="showToast('Creating rooms...', 'info', true)">
                    @csrf
                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Room Type</label>
                            <input type="text" name="room_type_name" list="roomTypeSuggestions" placeholder="e.g. Single, Double, Single With Shower" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Starting Room No</label>
                            <input type="text" name="start_room_number" placeholder="e.g. G001 or F101" required>
                        </div>
                        <div class="field">
                            <label class="field-label">How Many Rooms</label>
                            <input type="number" name="count" min="1" placeholder="e.g. 10" required>
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

                <datalist id="roomTypeSuggestions">
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="card">
                <h2>Bulk Price Update</h2>
                <p class="hint">Pick a room type and set a new price for every room of that type at once.</p>
                <form action="{{ route('rooms.bulkPrice') }}" method="POST" onsubmit="showToast('Updating prices...', 'info', true)">
                    @csrf
                    <div class="form-grid">
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
                            <label class="field-label">New Price / Night</label>
                            <input type="number" step="0.01" name="price_per_night" required>
                        </div>
                        <div class="field">
                            <button type="submit" onclick="return confirm('Apply this price change now?')">Apply Price Update</button>
                        </div>
                    </div>
                </form>
            </div>

            <table id="roomsTable" class="display" style="width:100%">
                <thead>
                    <tr><th>Room No</th><th>Floor</th><th>Type</th><th>Price/Night</th><th>Status</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>

        <!-- ============ STAFF PANEL ============ -->
        <div id="panel-staff" class="panel">
            <div class="topline"><h1>Manage Staff</h1></div>

            <!-- Edit Staff (hidden until Edit is clicked) -->
            <div class="card" id="editStaffCard" style="display:none;">
                <h2>Edit Staff Member</h2>
                <p class="hint">Leave password blank to keep it unchanged.</p>
                <form id="editStaffForm" method="POST" onsubmit="showToast('Saving changes...', 'info', true)">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="field">
                            <label class="field-label">Full Name</label>
                            <input type="text" name="fullname" id="edit_staff_fullname" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Username</label>
                            <input type="text" name="username" id="edit_staff_username" required>
                        </div>
                        <div class="field">
                            <label class="field-label">New Password</label>
                            <input type="password" name="password" id="edit_staff_password" placeholder="leave blank to keep current">
                        </div>
                        <div class="field-actions">
                            <button type="submit">Save Changes</button>
                            <button type="button" class="btn-ghost" onclick="cancelEditStaff()">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card">
                <h2>Create Staff Member</h2>
                <p class="hint">They'll use this username and password to log into the staff dashboard.</p>
                <form action="{{ route('staff.store') }}" method="POST" onsubmit="showToast('Creating staff member...', 'info', true)">
                    @csrf
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

            <table id="staffTable" class="display" style="width:100%">
                <thead>
                    <tr><th>Full Name</th><th>Username</th><th>Actions</th></tr>
                </thead>
            </table>
        </div>

        <!-- ============ REPORTS PANEL ============ -->
        <div id="panel-reports" class="panel">
            <div class="topline"><h1>Reports</h1></div>

            <div class="card">
                <h2>Reservation Report</h2>
                <p class="hint">The report updates automatically as soon as you change the range — export with the buttons above the table.</p>
                <div class="form-grid">
                    <div class="field">
                        <label class="field-label">Range</label>
                        <select id="reportRange" onchange="onRangeChange()">
                            <option value="today">Today</option>
                            <option value="week">Last 7 Days</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div class="field range-field-wrap" id="customRangeWrap" style="display:none;">
                        <label class="field-label">Custom Range</label>
                        <button type="button" class="btn-ghost" onclick="toggleRangeCalendar()">Pick Dates</button>
                        <div id="rangeCalendarPopup" class="calendar-popup" style="display:none;">
                            <div class="cal-header">
                                <button type="button" onclick="calNav(-1)">‹</button>
                                <span id="calMonthLabel"></span>
                                <button type="button" onclick="calNav(1)">›</button>
                            </div>
                            <div class="cal-grid" id="calGrid"></div>
                            <div class="cal-footer" id="calRangeLabel">Select start date</div>
                        </div>
                        <input type="hidden" id="reportFrom">
                        <input type="hidden" id="reportTo">
                    </div>
                </div>
            </div>

            <div class="stat-grid" id="reportSummary" style="display:none;">
                <div class="stat-card accent">
                    <div class="label">Reservations</div>
                    <div class="value" id="reportCount">0</div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Revenue</div>
                    <div class="value" id="reportRevenue">0</div>
                    <div class="sub">ETB</div>
                </div>
                <div class="stat-card">
                    <div class="label">Total Collected</div>
                    <div class="value" id="reportCollected">0</div>
                    <div class="sub">ETB</div>
                </div>
            </div>

            <table id="reportsTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Guest</th><th>Phone</th><th>Staff</th><th>Room</th><th>Room Price/Night</th>
                        <th>Check-in</th><th>Check-out</th><th>Actual Check-out</th><th>Total (ETB)</th>
                        <th>Paid (ETB)</th><th>Remaining (ETB)</th><th>Payment Status</th><th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>

    <!-- Hidden forms used by row action buttons -->
    <form id="deleteRoomForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <form id="deleteStaffForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <div class="toast-container" id="toastContainer"></div>

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
        // ---- Sidebar / panel switching ----
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

            if (name === 'rooms' && roomsTableInstance) roomsTableInstance.columns.adjust();
            if (name === 'staff' && staffTableInstance) staffTableInstance.columns.adjust();
            if (name === 'reports') {
                if (reportsTableInstance) reportsTableInstance.columns.adjust();
                else runReport();
            }

            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }

        // ---- Toasts ----
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

        // ==================== ROOMS DATATABLE ====================
        let roomsTableInstance = null;
        let roomsDataCache = [];

        async function loadRoomsTable() {
            const res = await fetch('{{ route('rooms.data') }}');
            const json = await res.json();
            roomsDataCache = json.data;

            if (roomsTableInstance) {
                roomsTableInstance.clear();
                roomsTableInstance.rows.add(roomsDataCache).draw();
                return;
            }

            roomsTableInstance = $('#roomsTable').DataTable({
                data: roomsDataCache,
                columns: [
                    { data: 'room_number' },
                    { data: 'floor_number' },
                    { data: 'room_type' },
                    { data: 'price_per_night' },
                    { data: 'status', render: s => `<span class="status-pill status-${s}">${s}</span>` },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (data) => `
                            <button type="button" class="btn-ghost" onclick="editRoomById(${data.id})">Edit</button>
                            <button type="button" class="btn-danger" onclick="deleteRoom(${data.id})">Delete</button>
                        `
                    },
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print'],
                pageLength: 10,
            });
        }

        function editRoomById(id) {
            const data = roomsDataCache.find(r => r.id === id);
            if (!data) return;

            document.getElementById('editRoomForm').action = `{{ url('/admin/rooms') }}/${id}`;
            document.getElementById('edit_room_number').value = data.room_number;
            document.getElementById('edit_room_type').value = data.room_type;
            document.getElementById('edit_price_per_night').value = data.price_raw;
            document.getElementById('edit_status').value = data.status;

            document.getElementById('editRoomCard').style.display = 'block';
            document.getElementById('mainContent').scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelEditRoom() {
            document.getElementById('editRoomCard').style.display = 'none';
        }

        function deleteRoom(id) {
            if (!confirm('Delete this room?')) return;
            document.getElementById('deleteRoomForm').action = `{{ url('/admin/rooms') }}/${id}`;
            document.getElementById('deleteRoomForm').submit();
        }

        // ==================== STAFF DATATABLE ====================
        let staffTableInstance = null;
        let staffDataCache = [];

        async function loadStaffTable() {
            const res = await fetch('{{ route('staff.data') }}');
            const json = await res.json();
            staffDataCache = json.data;

            if (staffTableInstance) {
                staffTableInstance.clear();
                staffTableInstance.rows.add(staffDataCache).draw();
                return;
            }

            staffTableInstance = $('#staffTable').DataTable({
                data: staffDataCache,
                columns: [
                    { data: 'fullname' },
                    { data: 'username' },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (data) => `
                            <button type="button" class="btn-ghost" onclick="editStaffById(${data.id})">Edit</button>
                            <button type="button" class="btn-danger" onclick="deleteStaff(${data.id})">Remove</button>
                        `
                    },
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print'],
                pageLength: 10,
            });
        }

        function editStaffById(id) {
            const data = staffDataCache.find(s => s.id === id);
            if (!data) return;

            document.getElementById('editStaffForm').action = `{{ url('/admin/staff') }}/${id}`;
            document.getElementById('edit_staff_fullname').value = data.fullname;
            document.getElementById('edit_staff_username').value = data.username;
            document.getElementById('edit_staff_password').value = '';

            document.getElementById('editStaffCard').style.display = 'block';
            document.getElementById('mainContent').scrollTo({ top: 0, behavior: 'smooth' });
        }

        function cancelEditStaff() {
            document.getElementById('editStaffCard').style.display = 'none';
        }

        function deleteStaff(id) {
            if (!confirm('Remove this staff member?')) return;
            document.getElementById('deleteStaffForm').action = `{{ url('/admin/staff') }}/${id}`;
            document.getElementById('deleteStaffForm').submit();
        }

        // ==================== REPORTS ====================
        let reportsTableInstance = null;

        function onRangeChange() {
            const isCustom = document.getElementById('reportRange').value === 'custom';
            document.getElementById('customRangeWrap').style.display = isCustom ? 'block' : 'none';
            if (!isCustom) runReport();
        }

        async function runReport() {
            const range = document.getElementById('reportRange').value;
            let url = `{{ route('admin.reports.data') }}?range=${range}`;

            if (range === 'custom') {
                const from = document.getElementById('reportFrom').value;
                const to = document.getElementById('reportTo').value;
                if (!from || !to) return; // wait until both picked
                url += `&from=${from}&to=${to}`;
            }

            const res = await fetch(url);
            const json = await res.json();

            document.getElementById('reportSummary').style.display = 'grid';
            document.getElementById('reportCount').textContent = json.summary.count;
            document.getElementById('reportRevenue').textContent = json.summary.total_revenue;
            document.getElementById('reportCollected').textContent = json.summary.total_collected;

            if (reportsTableInstance) {
                reportsTableInstance.clear();
                reportsTableInstance.rows.add(json.data).draw();
                return;
            }

            reportsTableInstance = $('#reportsTable').DataTable({
                data: json.data,
                columns: [
                    { data: 'guest_name' },
                    { data: 'phone' },
                    { data: 'staff' },
                    { data: 'room' },
                    { data: 'room_price' },
                    { data: 'check_in' },
                    { data: 'check_out' },
                    { data: 'actual_check_out' },
                    { data: 'total_price' },
                    { data: 'paid' },
                    { data: 'remaining' },
                    { data: 'payment_status', render: s => s === 'paid'
                        ? `<span class="status-pill status-available">Paid</span>`
                        : `<span class="status-pill status-maintenance">Remaining</span>` },
                    { data: 'status', render: s => `<span class="status-pill status-${s}">${s}</span>` },
                ],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'print'],
                pageLength: 10,
            });
        }

        // ---- Custom calendar range picker ----
        let calViewDate = new Date();
        let rangeStart = null, rangeEnd = null, rangeHoverDate = null;
        let calEventsBound = false;

        function toggleRangeCalendar() {
            const popup = document.getElementById('rangeCalendarPopup');
            const isOpen = popup.style.display === 'block';
            popup.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) renderCalendar();
        }

        function calNav(dir) {
            calViewDate.setMonth(calViewDate.getMonth() + dir);
            renderCalendar();
        }

        function isSameDate(a, b) {
            return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
        }

        // Builds the grid HTML once. Does NOT get called on every hover — only on
        // open/navigate/pick — so a click target never gets swapped out from under the user.
        function renderCalendar() {
            const grid = document.getElementById('calGrid');
            const label = document.getElementById('calMonthLabel');
            const year = calViewDate.getFullYear();
            const month = calViewDate.getMonth();
            label.textContent = calViewDate.toLocaleString('default', { month: 'long', year: 'numeric' });

            const today = new Date();
            const firstDay = new Date(year, month, 1);
            const startWeekday = firstDay.getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let html = '';
            ['S','M','T','W','T','F','S'].forEach(d => html += `<div class="cal-dow">${d}</div>`);
            for (let i = 0; i < startWeekday; i++) html += `<div></div>`;

            for (let d = 1; d <= daysInMonth; d++) {
                const dateObj = new Date(year, month, d);
                const iso = dateObj.toISOString().split('T')[0];
                let cls = 'cal-day';

                if (isSameDate(dateObj, today)) cls += ' cal-today';
                if (rangeStart && rangeEnd && dateObj >= rangeStart && dateObj <= rangeEnd) cls += ' cal-in-range';
                if (rangeStart && isSameDate(dateObj, rangeStart)) cls += ' cal-selected';
                if (rangeEnd && isSameDate(dateObj, rangeEnd)) cls += ' cal-selected';

                html += `<div class="${cls}" data-iso="${iso}">${d}</div>`;
            }
            grid.innerHTML = html;

            if (!calEventsBound) {
                // Event delegation: listeners live on the container, not on individual day
                // cells, so re-rendering the grid later never detaches them.
                grid.addEventListener('click', (e) => {
                    const dayEl = e.target.closest('.cal-day');
                    if (dayEl) calPick(dayEl.dataset.iso);
                });
                grid.addEventListener('mouseover', (e) => {
                    const dayEl = e.target.closest('.cal-day');
                    if (dayEl) calHover(dayEl.dataset.iso);
                });
                calEventsBound = true;
            }
        }

        function calPick(iso) {
            const picked = new Date(iso + 'T00:00:00');
            if (!rangeStart || (rangeStart && rangeEnd)) {
                rangeStart = picked; rangeEnd = null; rangeHoverDate = null;
                document.getElementById('calRangeLabel').textContent = 'Select end date';
                renderCalendar();
            } else {
                if (picked < rangeStart) { rangeEnd = rangeStart; rangeStart = picked; }
                else { rangeEnd = picked; }

                const fromIso = rangeStart.toISOString().split('T')[0];
                const toIso = rangeEnd.toISOString().split('T')[0];
                document.getElementById('calRangeLabel').textContent = `${fromIso} → ${toIso}`;
                document.getElementById('reportFrom').value = fromIso;
                document.getElementById('reportTo').value = toIso;
                renderCalendar();
                document.getElementById('rangeCalendarPopup').style.display = 'none';
                runReport();
            }
        }

        // Only toggles a CSS class on already-existing elements — no rebuild, so it
        // can't interrupt a click gesture in progress.
        function calHover(iso) {
            if (!rangeStart || rangeEnd) return;
            rangeHoverDate = new Date(iso + 'T00:00:00');
            const lo = rangeStart < rangeHoverDate ? rangeStart : rangeHoverDate;
            const hi = rangeStart < rangeHoverDate ? rangeHoverDate : rangeStart;
            document.querySelectorAll('#calGrid .cal-day').forEach(el => {
                const d = new Date(el.dataset.iso + 'T00:00:00');
                el.classList.toggle('cal-in-range', d >= lo && d <= hi);
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const panel = params.get('panel') || sessionStorage.getItem('activePanel') || 'overview';
            showPanel(panel, false);

            const main = document.getElementById('mainContent');
            const savedScroll = sessionStorage.getItem('scrollPos');
            if (savedScroll) main.scrollTop = parseInt(savedScroll, 10);
            main.addEventListener('scroll', () => {
                sessionStorage.setItem('scrollPos', main.scrollTop);
            });

            loadRoomsTable();
            loadStaffTable();

            // ---- Charts (unchanged) ----
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