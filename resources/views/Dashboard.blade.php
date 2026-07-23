<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equb Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9; /* slate-100 */
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #10b981; /* emerald-500 */
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #059669; /* emerald-600 */
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 overflow-hidden" x-data="{
    activeTab: 'dashboard',
    sidebarOpen: false,
    memberMode: 'list',   /* 'list' or 'create' */
    equbMode: 'list',     /* 'list' or 'create' */
    paymentMode: 'list',  /* 'list' or 'create' */
    drawMode: 'list',     /* 'list' or 'create' */
    userMode: 'list'      /* 'list' or 'create' */
}">

    <div class="flex h-screen overflow-hidden">

        <!-- Left Sidebar Navigation (Scrolls Independently) -->
        <!-- Mobile Drawer Overlay -->
        <div x-show="sidebarOpen"
             x-transition:opacity
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
             style="display: none;"></div>

        <!-- Sidebar Body -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-slate-300 z-50 lg:z-30 lg:static transform transition-transform duration-300 ease-in-out flex flex-col justify-between h-full overflow-y-auto">

            <div class="px-4 py-6 flex-1">
                <!-- App Brand Header (Desktop) -->
                <div class="hidden lg:flex items-center space-x-3 mb-8 px-2">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500 flex items-center justify-center text-white flex-shrink-0">
                        <i data-lucide="layers" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-white text-lg tracking-tight">Equb Portal</h1>
                        <span class="text-xs text-slate-400">Management Suite</span>
                    </div>
                </div>

                <!-- Navigation Groups -->
                <nav class="space-y-6">
                    <!-- Core Group -->
                    <div>
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Core</span>
                        <ul class="space-y-1">
                            <li>
                                <button @click="activeTab = 'dashboard'; sidebarOpen = false"
                                        :class="activeTab === 'dashboard' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                    <span>Dashboard</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Equbs Group -->
                    <div>
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Equbs</span>
                        <ul class="space-y-1">
                            <li>
                                <button @click="activeTab = 'equbs'; equbMode = 'list'; sidebarOpen = false"
                                        :class="activeTab === 'equbs' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="users-round" class="w-4 h-4"></i>
                                    <span>Equb Groups</span>
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'equbs'; equbMode = 'create'; sidebarOpen = false"
                                        class="w-full flex items-center space-x-3 px-3 py-2 text-xs font-normal text-slate-400 hover:text-white transition text-left">
                                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                    <span>Create New Equb</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Members Group -->
                    <div>
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Members</span>
                        <ul class="space-y-1">
                            <li>
                                <button @click="activeTab = 'members'; memberMode = 'list'; sidebarOpen = false"
                                        :class="activeTab === 'members' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="user-check" class="w-4 h-4"></i>
                                    <span>All Members</span>
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'members'; memberMode = 'create'; sidebarOpen = false"
                                        class="w-full flex items-center space-x-3 px-3 py-2 text-xs font-normal text-slate-400 hover:text-white transition text-left">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>Add Member</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Payments Group -->
                    <div>
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Finance</span>
                        <ul class="space-y-1">
                            <li>
                                <button @click="activeTab = 'payments'; paymentMode = 'list'; sidebarOpen = false"
                                        :class="activeTab === 'payments' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="wallet" class="w-4 h-4"></i>
                                    <span>Payments History</span>
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'payments'; paymentMode = 'create'; sidebarOpen = false"
                                        class="w-full flex items-center space-x-3 px-3 py-2 text-xs font-normal text-slate-400 hover:text-white transition text-left">
                                    <i data-lucide="banknote" class="w-3.5 h-3.5"></i>
                                    <span>Record Payment</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Draws Group -->
                    <div>
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Draws</span>
                        <ul class="space-y-1">
                            <li>
                                <button @click="activeTab = 'draws'; drawMode = 'list'; sidebarOpen = false"
                                        :class="activeTab === 'draws' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="dices" class="w-4 h-4"></i>
                                    <span>Lottery &amp; Draws</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Administration -->
                    <div>
                        <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Control Panel</span>
                        <ul class="space-y-1">
                            <li>
                                <button @click="activeTab = 'users'; userMode = 'list'; sidebarOpen = false"
                                        :class="activeTab === 'users' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                    <span>Users &amp; Roles</span>
                                </button>
                            </li>
                            <li>
                                <button @click="activeTab = 'settings'; sidebarOpen = false"
                                        :class="activeTab === 'settings' ? 'bg-emerald-600 text-white' : 'hover:bg-slate-800 hover:text-white'"
                                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left">
                                    <i data-lucide="settings" class="w-4 h-4"></i>
                                    <span>System Settings</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-800 flex items-center justify-between flex-shrink-0 bg-slate-950">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center font-bold text-sm text-white">A</div>
                    <div class="text-xs">
                        <p class="font-semibold text-white">Abel Ketema</p>
                        <p class="text-slate-400">System Admin</p>
                    </div>
                </div>
                <button class="text-slate-400 hover:text-rose-400 transition" title="Log out">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </aside>

        <!-- Right Side Main Content Area -->
        <div class="flex-1 flex flex-col h-full overflow-hidden">

            <!-- Mobile Navigation Header -->
            <header class="lg:hidden flex items-center justify-between bg-emerald-700 text-white px-4 py-3 shadow-md flex-shrink-0 z-20">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = true" class="p-1 hover:bg-emerald-800 rounded transition" aria-label="Open menu">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <span class="font-bold text-lg tracking-wide">EqubDash</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs bg-emerald-600 px-2 py-1 rounded">Abel</span>
                    <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-sm">A</div>
                </div>
            </header>

            <!-- Desktop Header -->
            <header class="hidden lg:flex items-center justify-between bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-20 flex-shrink-0">
                <div>
                    <h2 class="text-xs text-gray-500 font-medium">Welcome back,</h2>
                    <h1 class="text-xl font-bold text-gray-900">Abel Ketema</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-md font-medium">
                        <i data-lucide="calendar" class="w-4 h-4 inline-block mr-1.5 -mt-0.5"></i>
                        {{ date('F d, Y') }}
                    </span>
                </div>
            </header>

            <!-- Independent Scrollable Body Panel -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 lg:p-8">

                <!-- 1. DASHBOARD VIEW -->
                <div x-show="activeTab === 'dashboard'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2">
                    <!-- Top Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-400 uppercase">Active Equbs</span>
                                <span class="p-1.5 bg-emerald-50 text-emerald-600 rounded-md"><i data-lucide="repeat" class="w-4 h-4"></i></span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">12</h3>
                            <p class="text-xs text-emerald-600 mt-1 font-medium">4 cycles running</p>
                        </div>
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-400 uppercase">Total Members</span>
                                <span class="p-1.5 bg-blue-50 text-blue-600 rounded-md"><i data-lucide="users" class="w-4 h-4"></i></span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">148</h3>
                            <p class="text-xs text-blue-600 mt-1 font-medium">12 joined this week</p>
                        </div>
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-400 uppercase">Today's Collections</span>
                                <span class="p-1.5 bg-amber-50 text-amber-600 rounded-md"><i data-lucide="circle-dollar-sign" class="w-4 h-4"></i></span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">45,200 ETB</h3>
                            <p class="text-xs text-amber-600 mt-1 font-medium">88% of target met</p>
                        </div>
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-400 uppercase">Pending Payments</span>
                                <span class="p-1.5 bg-rose-50 text-rose-600 rounded-md"><i data-lucide="clock-alert" class="w-4 h-4"></i></span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">9</h3>
                            <p class="text-xs text-rose-600 mt-1 font-medium">Requires follow up</p>
                        </div>
                        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-semibold text-gray-400 uppercase">Completed Equbs</span>
                                <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-md"><i data-lucide="badge-check" class="w-4 h-4"></i></span>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900">34</h3>
                            <p class="text-xs text-indigo-600 mt-1 font-medium">Archived safely</p>
                        </div>
                    </div>

                    <!-- Recent Payments & Upcoming Draws -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm xl:col-span-2">
                            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 flex items-center space-x-2">
                                    <i data-lucide="arrow-down-left-from-circle" class="w-5 h-5 text-emerald-600"></i>
                                    <span>Recent Payments</span>
                                </h3>
                                <button @click="activeTab = 'payments'; paymentMode = 'list'" class="text-xs text-emerald-600 font-semibold hover:underline">View All</button>
                            </div>
                            <!-- Custom Scrollable Table Element (Side-to-Side Indicator) -->
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-100">
                                            <th class="py-3 px-6 font-semibold">Member</th>
                                            <th class="py-3 px-6 font-semibold">Equb</th>
                                            <th class="py-3 px-6 font-semibold">Amount</th>
                                            <th class="py-3 px-6 font-semibold">Date</th>
                                            <th class="py-3 px-6 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 font-medium text-gray-900">Almaz Yosef</td>
                                            <td class="py-4 px-6 text-gray-600">Mercato Weekly Premium</td>
                                            <td class="py-4 px-6 font-semibold text-slate-800">5,000 ETB</td>
                                            <td class="py-4 px-6 text-gray-500 text-xs">Today, 10:14 AM</td>
                                            <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Paid</span></td>
                                        </tr>
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 font-medium text-gray-900">Dawit Tesfaye</td>
                                            <td class="py-4 px-6 text-gray-600">Bole Friends Monthly</td>
                                            <td class="py-4 px-6 font-semibold text-slate-800">10,000 ETB</td>
                                            <td class="py-4 px-6 text-gray-500 text-xs">Yesterday</td>
                                            <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Paid</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 flex items-center space-x-2">
                                    <i data-lucide="sparkles" class="w-5 h-5 text-amber-500"></i>
                                    <span>Upcoming Draws</span>
                                </h3>
                                <button @click="activeTab = 'draws'; drawMode = 'list'" class="text-xs text-amber-600 font-semibold hover:underline">Conduct</button>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100 flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">Mercato Premium</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Winner: Pending Draw</p>
                                    </div>
                                    <span class="text-xs bg-amber-200 text-amber-800 font-bold px-2 py-1 rounded">Oct 28</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. MEMBERS VIEW -->
                <div x-show="activeTab === 'members'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" style="display: none;">

                    <!-- MEMBER LIST SUB-VIEW -->
                    <div x-show="memberMode === 'list'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Members Directory</h1>
                                <p class="text-sm text-gray-500">View and manage registered Equb members.</p>
                            </div>
                            <button @click="memberMode = 'create'" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition space-x-1">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Add Member</span>
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row gap-4 justify-between">
                            <div class="relative flex-1">
                                <i data-lucide="search" class="absolute left-3 top-3 w-5 h-5 text-gray-400"></i>
                                <input type="text" placeholder="Search members by name..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg outline-none text-sm" />
                            </div>
                            <div class="flex items-center space-x-2">
                                <select class="border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none bg-white">
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Responsive Sliding Table -->
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                                            <th class="py-3.5 px-6 font-semibold">ID</th>
                                            <th class="py-3.5 px-6 font-semibold">Full Name</th>
                                            <th class="py-3.5 px-6 font-semibold">Phone</th>
                                            <th class="py-3.5 px-6 font-semibold">Address</th>
                                            <th class="py-3.5 px-6 font-semibold">Joined Date</th>
                                            <th class="py-3.5 px-6 font-semibold">Status</th>
                                            <th class="py-3.5 px-6 font-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 text-gray-500 font-mono">EQB-0082</td>
                                            <td class="py-4 px-6 font-medium text-gray-900">Almaz Yosef</td>
                                            <td class="py-4 px-6 text-gray-600">+251 911 234 567</td>
                                            <td class="py-4 px-6 text-gray-600">Addis Ababa</td>
                                            <td class="py-4 px-6 text-gray-500">Jan 12, 2023</td>
                                            <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Active</span></td>
                                            <td class="py-4 px-6"><button class="text-rose-600 hover:text-rose-800 font-medium">Delete</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ADD MEMBER INLINE FORM VIEW (Perfectly Responsive) -->
                    <div x-show="memberMode === 'create'" class="max-w-3xl bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Add New Equb Member</h2>
                            <button @click="memberMode = 'list'" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                <span>Back to Directory</span>
                            </button>
                        </div>
                        <form class="space-y-4" @submit.prevent="memberMode = 'list'">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">First Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Middle Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Last Name</label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Gender</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Phone Number</label>
                                    <input type="tel" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Address</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Member Photo</label>
                                <input type="file" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm text-gray-400 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100" />
                            </div>
                            <div class="flex space-x-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" @click="memberMode = 'list'" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                                <button type="submit" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Save Profile</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- 3. EQUBS VIEW -->
                <div x-show="activeTab === 'equbs'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" style="display: none;">

                    <!-- EQUB LIST SUB-VIEW -->
                    <div x-show="equbMode === 'list'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Equb Groups</h1>
                                <p class="text-sm text-gray-500">Create, monitor and track overall Equb cycles.</p>
                            </div>
                            <button @click="equbMode = 'create'" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition space-x-1">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Create New Equb</span>
                            </button>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                                            <th class="py-3.5 px-6 font-semibold">Equb Name</th>
                                            <th class="py-3.5 px-6 font-semibold">Amount</th>
                                            <th class="py-3.5 px-6 font-semibold">Members</th>
                                            <th class="py-3.5 px-6 font-semibold">Payment Type</th>
                                            <th class="py-3.5 px-6 font-semibold">Start Date</th>
                                            <th class="py-3.5 px-6 font-semibold">End Date</th>
                                            <th class="py-3.5 px-6 font-semibold">Status</th>
                                            <th class="py-3.5 px-6 font-semibold">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 font-medium text-gray-900">Mercato Premium Group</td>
                                            <td class="py-4 px-6 text-gray-700 font-semibold">5,000 ETB</td>
                                            <td class="py-4 px-6 text-gray-600">20 Members</td>
                                            <td class="py-4 px-6 text-gray-600">Weekly</td>
                                            <td class="py-4 px-6 text-gray-500">Jan 01, 2024</td>
                                            <td class="py-4 px-6 text-gray-500">Dec 31, 2024</td>
                                            <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Active</span></td>
                                            <td class="py-4 px-6"><button class="text-rose-600 hover:text-rose-800 font-medium">Close</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- CREATE EQUB INLINE FORM VIEW -->
                    <div x-show="equbMode === 'create'" class="max-w-3xl bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Define New Equb Group</h2>
                            <button @click="equbMode = 'list'" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                <span>Back to Groups</span>
                            </button>
                        </div>
                        <form class="space-y-4" @submit.prevent="equbMode = 'list'">
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Equb Name</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Description</label>
                                <textarea rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Member Limit</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Contribution Amount</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Payment Type</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Daily</option>
                                        <option>Weekly</option>
                                        <option>Monthly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Draw Method</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Random</option>
                                        <option>Fixed Order</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Start Date</label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">End Date</label>
                                    <input type="date" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Active</option>
                                        <option>Pending</option>
                                        <option>Closed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex space-x-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" @click="equbMode = 'list'" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                                <button type="submit" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Save Group</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- 4. PAYMENTS VIEW -->
                <div x-show="activeTab === 'payments'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" style="display: none;">

                    <!-- PAYMENT HISTORY SUB-VIEW -->
                    <div x-show="paymentMode === 'list'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Payment Transactions</h1>
                                <p class="text-sm text-gray-500">Record payments manually and review past ledger statements.</p>
                            </div>
                            <button @click="paymentMode = 'create'" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition space-x-1">
                                <i data-lucide="banknote" class="w-4 h-4"></i>
                                <span>Record Payment</span>
                            </button>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                                            <th class="py-3.5 px-6 font-semibold">Member</th>
                                            <th class="py-3.5 px-6 font-semibold">Equb</th>
                                            <th class="py-3.5 px-6 font-semibold">Amount</th>
                                            <th class="py-3.5 px-6 font-semibold">Payment Date</th>
                                            <th class="py-3.5 px-6 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 font-medium text-gray-900">Almaz Yosef</td>
                                            <td class="py-4 px-6 text-gray-600">Mercato Weekly Premium</td>
                                            <td class="py-4 px-6 font-semibold text-slate-800">5,000 ETB</td>
                                            <td class="py-4 px-6 text-gray-500">Today at 10:14 AM</td>
                                            <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Paid</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- RECORD PAYMENT INLINE FORM VIEW -->
                    <div x-show="paymentMode === 'create'" class="max-w-3xl bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Record Incoming Payment</h2>
                            <button @click="paymentMode = 'list'" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                <span>Back to Ledger</span>
                            </button>
                        </div>
                        <form class="space-y-4" @submit.prevent="paymentMode = 'list'">
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Select Member</label>
                                <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                    <option>Almaz Yosef</option>
                                    <option>Dawit Tesfaye</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Select Equb</label>
                                <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                    <option>Mercato Weekly Premium</option>
                                    <option>Bole Friends Monthly</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Amount</label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Payment Method</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Cash</option>
                                        <option>Bank</option>
                                        <option>Mobile Money</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Reference Number</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Notes</label>
                                <textarea rows="2" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500"></textarea>
                            </div>
                            <div class="flex space-x-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" @click="paymentMode = 'list'" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                                <button type="submit" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Save Payment</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- 5. DRAWS VIEW -->
                <div x-show="activeTab === 'draws'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" style="display: none;">

                    <!-- DRAWS HISTORY LIST -->
                    <div x-show="drawMode === 'list'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Lottery Rounds &amp; Draws</h1>
                                <p class="text-sm text-gray-500">Conduct real-time random selection cycles transparently.</p>
                            </div>
                            <button @click="drawMode = 'create'" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition space-x-1">
                                <i data-lucide="dices" class="w-4 h-4"></i>
                                <span>Conduct Draw</span>
                            </button>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                                            <th class="py-3.5 px-6 font-semibold">Round</th>
                                            <th class="py-3.5 px-6 font-semibold">Equb</th>
                                            <th class="py-3.5 px-6 font-semibold">Winner</th>
                                            <th class="py-3.5 px-6 font-semibold">Amount</th>
                                            <th class="py-3.5 px-6 font-semibold">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 font-semibold text-gray-900">#04</td>
                                            <td class="py-4 px-6 text-gray-600">Mercato Premium Group</td>
                                            <td class="py-4 px-6 text-gray-950 font-medium">Kidus Yohannes</td>
                                            <td class="py-4 px-6 text-emerald-600 font-bold">150,000 ETB</td>
                                            <td class="py-4 px-6 text-gray-500">Oct 12, 2024</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- CONDUCT DRAW INLINE VIEW -->
                    <div x-show="drawMode === 'create'" class="max-w-3xl bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Conduct Selection Draw</h2>
                            <button @click="drawMode = 'list'" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                <span>Back to Draw History</span>
                            </button>
                        </div>
                        <form class="space-y-4" @submit.prevent="drawMode = 'list'">
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Select Target Equb Group</label>
                                <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                    <option>Mercato Premium Group</option>
                                    <option>Bole Friends Club</option>
                                </select>
                            </div>
                            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center space-x-3">
                                <i data-lucide="sparkles" class="w-6 h-6 text-emerald-600 flex-shrink-0"></i>
                                <div>
                                    <p class="text-xs font-bold text-emerald-800 uppercase">Algorithm Scan ready</p>
                                    <p class="text-xs text-emerald-700">This execution scan selects an eligible member transparently using predefined lottery constraints.</p>
                                </div>
                            </div>
                            <div class="flex space-x-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" @click="drawMode = 'list'" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                                <button type="submit" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Run Engine Draw</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- 6. USERS & ROLES VIEW -->
                <div x-show="activeTab === 'users'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" style="display: none;">

                    <!-- USER LIST VIEW -->
                    <div x-show="userMode === 'list'">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">System Staff Registry</h1>
                                <p class="text-sm text-gray-500">Manage internal administration logins and system security roles.</p>
                            </div>
                            <button @click="userMode = 'create'" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg shadow-sm transition space-x-1">
                                <i data-lucide="user-plus" class="w-4 h-4"></i>
                                <span>Add User</span>
                            </button>
                        </div>

                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto custom-scrollbar pb-2">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-500 border-b border-gray-200">
                                            <th class="py-3.5 px-6 font-semibold">Name</th>
                                            <th class="py-3.5 px-6 font-semibold">Username</th>
                                            <th class="py-3.5 px-6 font-semibold">Role</th>
                                            <th class="py-3.5 px-6 font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="py-4 px-6 font-medium text-gray-900">Abel Ketema</td>
                                            <td class="py-4 px-6 text-gray-600">abel_k</td>
                                            <td class="py-4 px-6 text-gray-600">Admin</td>
                                            <td class="py-4 px-6"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Active</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- CREATE USER INLINE VIEW -->
                    <div x-show="userMode === 'create'" class="max-w-3xl bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Add System User</h2>
                            <button @click="userMode = 'list'" class="text-sm font-semibold text-gray-500 hover:text-gray-800 flex items-center space-x-1">
                                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                <span>Back to Staff Registry</span>
                            </button>
                        </div>
                        <form class="space-y-4" @submit.prevent="userMode = 'list'">
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Name</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Username</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Password</label>
                                    <input type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Confirm Password</label>
                                    <input type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm focus:ring-1 focus:ring-emerald-500" required />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Role</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Admin</option>
                                        <option>Cashier</option>
                                        <option>Manager</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Status</label>
                                    <select class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm bg-white focus:ring-1 focus:ring-emerald-500">
                                        <option>Active</option>
                                        <option>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex space-x-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" @click="userMode = 'list'" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                                <button type="submit" class="w-1/2 md:w-auto px-6 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition">Save User</button>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- 7. SYSTEM SETTINGS VIEW -->
                <div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-2" style="display: none;">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">System Preferences</h1>
                        <p class="text-sm text-gray-500">Configure core organizational information and settings parameters.</p>
                    </div>

                    <div class="bg-white p-6 lg:p-8 rounded-xl border border-gray-200 shadow-sm max-w-4xl">
                        <form @submit.prevent class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                    <input type="text" value="Premium Equb Financial Association" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-sm" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo</label>
                                    <input type="file" class="w-full px-3 py-2 border border-gray-200 rounded-lg outline-none text-sm text-gray-400 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <input type="text" value="Bole Sub-city, Woreda 03, House #142" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-sm" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="text" value="+251 11 555 1234" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-sm" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" value="support@premiumequb.com" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-sm" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                    <select class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-sm bg-white">
                                        <option>Ethiopian Birr (ETB)</option>
                                        <option>USD</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                    <select class="w-full px-4 py-2.5 border border-gray-200 rounded-lg outline-none text-sm bg-white">
                                        <option>Amharic (አማርኛ)</option>
                                        <option selected>English</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row gap-4 items-center justify-between">
                                <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 px-4 rounded-lg shadow-xs transition space-x-1 text-sm">
                                    <i data-lucide="database" class="w-4 h-4"></i>
                                    <span>Backup Database</span>
                                </button>
                                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-sm transition text-sm">
                                    Save Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
