<div x-data="reportExportManager()" class="space-y-6 pb-12">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-slate-950">Report & Visualization</h2>
            <p class="mt-1 text-sm text-slate-500">Overview of staff, trainees, and structural analytics.</p>
        </div>
        <button @click="openModal()" class="inline-flex min-h-[2.75rem] items-center justify-center gap-2 rounded-xl bg-[#356AE6] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export Report
        </button>
    </div>

    <!-- 1. Dashboard Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Total Trainees -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Trainees</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['totalApplicants'] ?? 0) }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs font-medium text-emerald-600">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                <span>Applications tracked</span>
            </div>
        </div>
        <!-- Grade 3 Staff -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Grade 3 Staff</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['totalTestTakingStaffRegistrations'] ?? 0) }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-500">
                <span>Active Personnel</span>
            </div>
        </div>
        <!-- Intern Staff -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Intern Staff</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format($stats['totalTeamStaff'] ?? 0) }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs font-medium text-emerald-600">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                <span>Registered Staff</span>
            </div>
        </div>
        <!-- Uploaded Documents -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Documents</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ number_format(($stats['totalTeamStaffDocuments'] ?? 0) + ($stats['totalTestTakingStaffDocuments'] ?? 0) + ($stats['totalApplicants'] ?? 0)) }}</p>
            <div class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-500">
                <span>Total Uploaded Files</span>
            </div>
        </div>
    </div>

    <!-- 2. Charts -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Pie Chart (real data) -->
        @php
            $rptTotal = max(1, ($stats['totalApplicants'] ?? 0) + ($stats['totalTestTakingStaffRegistrations'] ?? 0) + ($stats['totalTeamStaff'] ?? 0));
            $rptTraineePct  = round(($stats['totalApplicants'] ?? 0) / $rptTotal * 100, 1);
            $rptGrade3Pct   = round(($stats['totalTestTakingStaffRegistrations'] ?? 0) / $rptTotal * 100, 1);
            $rptInternPct   = max(0, 100 - $rptTraineePct - $rptGrade3Pct);
            $rptGrade3Start = $rptTraineePct;
            $rptInternStart = $rptTraineePct + $rptGrade3Pct;
            $rptPieStyle    = "conic-gradient(#356AE6 0% {$rptTraineePct}%, #10B981 {$rptGrade3Start}% {$rptInternStart}%, #F59E0B {$rptInternStart}% 100%)";
        @endphp
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">Personnel Distribution</h3>
            <p class="text-xs text-slate-500">Trainees vs Grade 3 vs Interns</p>
            <div class="mt-6 flex h-64 items-center justify-center">
                <div
                    id="reports-pie-chart"
                    class="relative h-48 w-48 rounded-full"
                    data-gradient="{{ $rptPieStyle }}"
                >
                    <div class="absolute inset-[30px] rounded-full bg-white flex items-center justify-center">
                        <span class="text-sm font-semibold text-slate-500">{{ number_format($rptTotal) }}</span>
                    </div>
                </div>
            </div>
            <script>
                (function () {
                    var el = document.getElementById('reports-pie-chart');
                    if (el) { el.style.background = el.dataset.gradient; }
                })();
            </script>
            <div class="mt-4 flex justify-center gap-6 text-xs text-slate-600">
                <span class="flex items-center gap-1"><div class="h-3 w-3 rounded-full bg-[#356AE6]"></div> Trainees ({{ $rptTraineePct }}%)</span>
                <span class="flex items-center gap-1"><div class="h-3 w-3 rounded-full bg-emerald-500"></div> Grade 3 ({{ $rptGrade3Pct }}%)</span>
                <span class="flex items-center gap-1"><div class="h-3 w-3 rounded-full bg-amber-500"></div> Interns ({{ $rptInternPct }}%)</span>
            </div>
        </div>

        <!-- Bar Chart 1 -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">Staff/Trainees by Department</h3>
            <p class="text-xs text-slate-500">Distribution across operational units</p>
            <div class="mt-6 flex h-64 flex-col justify-end gap-3 border-b border-l border-slate-200 px-4 pb-2 pt-6">
                <!-- Bar mocks -->
                <div class="flex h-full items-end justify-between gap-2">
                    <div class="w-1/5 rounded-t-sm bg-blue-100 relative group"><div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded opacity-0 transition group-hover:opacity-100">IT</div><div class="h-[60%] w-full rounded-t-sm bg-blue-500"></div></div>
                    <div class="w-1/5 rounded-t-sm bg-blue-100 relative group"><div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded opacity-0 transition group-hover:opacity-100">HR</div><div class="h-[85%] w-full rounded-t-sm bg-blue-500"></div></div>
                    <div class="w-1/5 rounded-t-sm bg-blue-100 relative group"><div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded opacity-0 transition group-hover:opacity-100">OP</div><div class="h-[40%] w-full rounded-t-sm bg-blue-500"></div></div>
                    <div class="w-1/5 rounded-t-sm bg-blue-100 relative group"><div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded opacity-0 transition group-hover:opacity-100">FIN</div><div class="h-[30%] w-full rounded-t-sm bg-blue-500"></div></div>
                    <div class="w-1/5 rounded-t-sm bg-blue-100 relative group"><div class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] bg-slate-800 text-white px-2 py-0.5 rounded opacity-0 transition group-hover:opacity-100">SEC</div><div class="h-[70%] w-full rounded-t-sm bg-blue-500"></div></div>
                </div>
            </div>
            <div class="mt-2 flex justify-between px-4 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">
                <span>IT</span><span>HR</span><span>OP</span><span>FIN</span><span>SEC</span>
            </div>
        </div>

        <!-- Line Chart -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">Registration by Month</h3>
            <p class="text-xs text-slate-500">Monthly intake volume</p>
            <div class="mt-6 h-64">
                <!-- Reusing the SVG line chart from before but mocked -->
                <svg viewBox="0 0 500 200" class="h-full w-full">
                    <path d="M0,200 L0,150 L100,120 L200,80 L300,100 L400,40 L500,20 L500,200 Z" fill="url(#reportsAreaFill)" />
                    <polyline points="0,150 100,120 200,80 300,100 400,40 500,20" fill="none" stroke="#356AE6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    <!-- Points -->
                    <circle cx="0" cy="150" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                    <circle cx="100" cy="120" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                    <circle cx="200" cy="80" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                    <circle cx="300" cy="100" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                    <circle cx="400" cy="40" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                    <circle cx="500" cy="20" r="4.5" fill="#fff" stroke="#356AE6" stroke-width="2.5"></circle>
                    <defs>
                        <linearGradient id="reportsAreaFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#356AE6" stop-opacity="0.2"></stop>
                            <stop offset="100%" stop-color="#356AE6" stop-opacity="0.0"></stop>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            <div class="mt-2 flex justify-between text-[10px] font-semibold text-slate-500 uppercase tracking-widest">
                <span>Jan</span><span>Mar</span><span>May</span><span>Jul</span><span>Sep</span><span>Nov</span>
            </div>
        </div>

        <!-- Bar Chart 2 -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="font-semibold text-slate-900">Documents Uploaded</h3>
            <p class="text-xs text-slate-500">Document throughput tracker</p>
            <div class="mt-6 flex h-64 items-end gap-2 border-b border-l border-slate-200 px-2 pb-2 pt-6" id="docs-bar-chart">
                @foreach([20, 45, 30, 60, 80, 55, 90, 40, 75, 85, 50, 100] as $h)
                    <div class="w-full rounded-t-sm bg-indigo-100 group relative">
                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 opacity-0 transition group-hover:opacity-100 bg-slate-800 text-white text-[10px] py-0.5 px-1.5 rounded">{{ $h * 105 }}</div>
                        <div class="w-full rounded-t-sm bg-indigo-500 transition-all group-hover:bg-indigo-600" data-height="{{ $h }}"></div>
                    </div>
                @endforeach
            </div>
            <script>
                document.querySelectorAll('#docs-bar-chart [data-height]').forEach(function(el) {
                    el.style.height = el.dataset.height + '%';
                });
            </script>
            <div class="mt-2 flex justify-between px-2 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">
                <span>Jan</span><span>Dec</span>
            </div>
        </div>
    </div>

    <!-- 3. Tables -->
    <div x-data="{ 
            currentTab: new URLSearchParams(window.location.search).get('tab') || 'trainee',
            setTab(tab) {
                this.currentTab = tab;
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.pushState({}, '', url);
            }
        }" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 pt-4 flex gap-6 overflow-x-auto">
            <button @click="setTab('trainee')" class="shrink-0 font-semibold pb-3 text-sm transition border-b-2" :class="currentTab === 'trainee' ? 'text-[#356AE6] border-[#356AE6]' : 'text-slate-500 border-transparent hover:text-slate-700'">Trainee List</button>
            <button @click="setTab('grade3')" class="shrink-0 font-semibold pb-3 text-sm transition border-b-2" :class="currentTab === 'grade3' ? 'text-[#356AE6] border-[#356AE6]' : 'text-slate-500 border-transparent hover:text-slate-700'">Grade 3 Staff List</button>
            <button @click="setTab('intern')" class="shrink-0 font-semibold pb-3 text-sm transition border-b-2" :class="currentTab === 'intern' ? 'text-[#356AE6] border-[#356AE6]' : 'text-slate-500 border-transparent hover:text-slate-700'">Intern Staff List</button>
            <button @click="setTab('documents')" class="shrink-0 font-semibold pb-3 text-sm transition border-b-2" :class="currentTab === 'documents' ? 'text-[#356AE6] border-[#356AE6]' : 'text-slate-500 border-transparent hover:text-slate-700'">Uploaded Documents List</button>
        </div>
        <div class="overflow-x-auto min-h-[300px]">
            <!-- Trainee Table -->
            <table x-show="currentTab === 'trainee'" class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-[11px] uppercase tracking-widest text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID Code</th>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Course / Unit</th>
                        <th class="px-6 py-4 font-semibold">Date Registered</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $app->id_number }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $app->khmer_name }}</span>
                            <span class="block text-xs text-slate-400 mt-0.5">{{ $app->latin_name }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $app->course?->name ?? $app->unit ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $app->created_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    'Approved' => 'bg-emerald-100 text-emerald-700',
                                    'Rejected' => 'bg-rose-100 text-rose-700',
                                ];
                                $class = $statusClasses[$app->status] ?? 'bg-slate-100 text-slate-700';
                            @endphp
                            <span class="inline-flex rounded-full px-3 py-1 text-[11px] font-semibold tracking-wide {{ $class }}">{{ $app->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No trainees found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Grade 3 Staff Table -->
            <table x-show="currentTab === 'grade3'" style="display: none;" class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-[11px] uppercase tracking-widest text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Rank</th>
                        <th class="px-6 py-4 font-semibold">Date Registered</th>
                        <th class="px-6 py-4 font-semibold text-center">Contact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($testTakingStaffRegistrations as $staff)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-semibold text-slate-900">#GD3-{{ $staff->id }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $staff->name_kh }}</span>
                            <span class="block text-xs text-slate-400 mt-0.5">{{ $staff->name_latin }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $staff->rank?->name_kh ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $staff->created_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">{{ $staff->phone_number ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No grade 3 staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Intern Staff Table -->
            <table x-show="currentTab === 'intern'" style="display: none;" class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-[11px] uppercase tracking-widest text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Position</th>
                        <th class="px-6 py-4 font-semibold">Date Registered</th>
                        <th class="px-6 py-4 font-semibold text-center">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teamStaffMembers as $intern)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $intern->id_number ?? ('#INT-'.$intern->id) }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $intern->name_kh }}</span>
                            <span class="block text-xs text-slate-400 mt-0.5">{{ $intern->name_latin }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $intern->position ?? '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $intern->created_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-[11px] font-semibold text-blue-700 tracking-wide">{{ $intern->role ?? 'Staff' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-slate-500">No intern staff found.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Uploaded Documents Table (Mocked data since it is aggregated across models) -->
            <table x-show="currentTab === 'documents'" style="display: none;" class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/50 text-[11px] uppercase tracking-widest text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Doc Code</th>
                        <th class="px-6 py-4 font-semibold">File Name</th>
                        <th class="px-6 py-4 font-semibold">Document Type</th>
                        <th class="px-6 py-4 font-semibold">Date Uploaded</th>
                        <th class="px-6 py-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @for($i=1; $i<=5; $i++)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 font-semibold text-slate-900">#DOC-2026-{{ str_pad($i*3, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">National ID Card v{{ $i }}</span>
                            <span class="block text-xs text-slate-400 mt-0.5">Uploaded by: Admin</span>
                        </td>
                        <td class="px-6 py-4">Identity verification</td>
                        <td class="px-6 py-4 text-slate-500">{{ 10 + $i }} Apr 2026</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-semibold text-emerald-700 tracking-wide">Valid</span>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-6 py-4 bg-white">
            <div x-show="currentTab === 'trainee'">
                {{ $applications->appends(['tab' => 'trainee'])->links('pagination::tailwind') }}
            </div>
            <div x-show="currentTab === 'grade3'" style="display: none;">
                {{ $testTakingStaffRegistrations->appends(['tab' => 'grade3'])->links('pagination::tailwind') }}
            </div>
            <div x-show="currentTab === 'intern'" style="display: none;">
                {{ $teamStaffMembers->appends(['tab' => 'intern'])->links('pagination::tailwind') }}
            </div>
            <div x-show="currentTab === 'documents'" style="display: none;" class="flex justify-between items-center text-sm">
                <span class="text-slate-500">Showing <span class="font-semibold text-slate-900">1</span> to <span class="font-semibold text-slate-900">5</span> of <span class="font-semibold text-slate-900">120</span> entries</span>
                <div class="flex gap-1.5 focus:outline-none">
                    <button class="px-3 py-1.5 border border-slate-200 rounded-lg text-slate-400 disabled:opacity-50 font-medium" disabled>Prev</button>
                    <button class="px-3 py-1.5 border border-[#356AE6] rounded-lg bg-[#356AE6] font-medium text-white shadow-sm">1</button>
                    <button class="px-3 py-1.5 border border-slate-200 rounded-lg bg-white text-slate-600 hover:bg-slate-50 font-medium transition">Next</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div x-show="isModalOpen" 
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <!-- Backdrop -->
        <div x-show="isModalOpen" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="closeModal()"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isModalOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative overflow-hidden flex flex-col rounded-[1.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-5xl border border-slate-200">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 bg-white px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold tracking-tight text-slate-900" id="modal-title">Export Report</h3>
                            <p class="text-xs text-slate-500 font-medium">Configure format and filters for your report.</p>
                        </div>
                    </div>
                    <button @click="closeModal()" type="button" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition outline-none focus:ring-2 focus:ring-slate-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <!-- Modal Body: Two Columns (Form | Preview) -->
                <div class="flex flex-col lg:flex-row bg-slate-50/50">
                    
                    <!-- Left: Filters & Form -->
                    <div class="flex-1 p-6 lg:border-r border-slate-100 space-y-7 bg-white">
                        
                        <div class="space-y-2">
                            <label class="text-[13px] font-bold text-slate-800">Report Type</label>
                            <select x-model="reportType" class="form-select w-full rounded-xl border-slate-200 bg-slate-50 hover:bg-white focus:bg-white text-sm shadow-sm transition h-11 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="Trainee Report">Trainee Report</option>
                                <option value="Grade 3 Staff Report">Grade 3 Staff Report</option>
                                <option value="Intern Staff Report">Intern Staff Report</option>
                                <option value="Uploaded Documents Report">Uploaded Documents Report</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[13px] font-bold text-slate-800">Date Range</label>
                            <div class="flex items-center gap-2">
                                <input type="date" class="form-input w-full rounded-xl border-slate-200 text-sm shadow-sm h-11 focus:border-blue-500 focus:ring-1 hover:bg-white bg-slate-50 focus:bg-white transition" value="2026-04-01">
                                <span class="text-slate-400 font-medium">-</span>
                                <input type="date" class="form-input w-full rounded-xl border-slate-200 text-sm shadow-sm h-11 focus:border-blue-500 focus:ring-1 hover:bg-white bg-slate-50 focus:bg-white transition" value="2026-04-11">
                            </div>
                        </div>

                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 border-b border-slate-100 pb-2">Filters</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Trainee Name</label>
                                    <input type="text" placeholder="Enter name to filter..." class="form-input w-full rounded-xl border-slate-200 text-sm shadow-sm placeholder:text-slate-400 h-11 hover:bg-white bg-slate-50 focus:bg-white transition focus:border-blue-500">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Grade 3 Team</label>
                                        <input type="text" placeholder="e.g. Alpha" class="form-input w-full rounded-xl border-slate-200 text-sm shadow-sm placeholder:text-slate-400 h-11 hover:bg-white bg-slate-50 focus:bg-white transition focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Department</label>
                                        <select class="form-select w-full rounded-xl border-slate-200 text-sm shadow-sm text-slate-600 h-11 hover:bg-white bg-slate-50 focus:bg-white transition focus:border-blue-500">
                                            <option value="">All Departments</option>
                                            <option value="IT">IT</option>
                                            <option value="HR">HR</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Status</label>
                                    <select class="form-select w-full rounded-xl border-slate-200 text-sm shadow-sm text-slate-600 h-11 hover:bg-white bg-slate-50 focus:bg-white transition focus:border-blue-500">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3 border-b border-slate-100 pb-2">Export Format</h4>
                            <div class="flex gap-3">
                                <!-- PDF Toggle -->
                                <label class="relative flex cursor-pointer rounded-xl border bg-white p-3 shadow-sm hover:bg-slate-50 w-full transition-all"
                                       :class="format === 'pdf' ? 'border-[#356AE6] ring-1 ring-[#356AE6]/20 bg-[#356AE6]/5' : 'border-slate-200'">
                                    <input type="radio" x-model="format" value="pdf" class="sr-only">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg shadow-sm" :class="format === 'pdf' ? 'bg-[#356AE6] text-white' : 'bg-rose-50 text-rose-500 border border-rose-100'">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-slate-900 text-[13px]">PDF</p>
                                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Visual layout</p>
                                        </div>
                                        <div class="h-5 w-5 rounded-full border flex items-center justify-center" :class="format === 'pdf' ? 'border-[#356AE6] bg-[#356AE6]' : 'border-slate-300 bg-slate-50'">
                                            <svg x-show="format === 'pdf'" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    </div>
                                </label>

                                <!-- Excel Toggle -->
                                <label class="relative flex cursor-pointer rounded-xl border bg-white p-3 shadow-sm hover:bg-slate-50 w-full transition-all"
                                       :class="format === 'excel' ? 'border-emerald-600 ring-1 ring-emerald-600/20 bg-emerald-50/30' : 'border-slate-200'">
                                    <input type="radio" x-model="format" value="excel" class="sr-only">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg shadow-sm" :class="format === 'excel' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-bold text-slate-900 text-[13px]">Excel</p>
                                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Multiple sheets</p>
                                        </div>
                                        <div class="h-5 w-5 rounded-full border flex items-center justify-center" :class="format === 'excel' ? 'border-emerald-600 bg-emerald-600' : 'border-slate-300 bg-slate-50'">
                                            <svg x-show="format === 'excel'" class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Preview Area -->
                    <div class="w-full lg:w-[480px] bg-[linear-gradient(135deg,#f8fafc,#f1f5f9)] p-6 relative border-l border-white shadow-[inset_1px_0_0_rgba(0,0,0,0.02)]">
                        <div x-show="isPreviewing" 
                             class="absolute inset-0 z-10 bg-slate-50/50 backdrop-blur-[2px] flex flex-col items-center justify-center">
                             <div class="h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-[#356AE6]"></div>
                             <p class="mt-4 text-[13px] font-bold tracking-wide text-slate-600">Generating Preview...</p>
                        </div>

                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Live Preview</h4>
                            <button @click="triggerPreview()" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 transition flex items-center gap-1.5 border border-transparent hover:border-blue-100">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                Refresh
                            </button>
                        </div>
                        
                        <!-- PDF Format Preview -->
                        <div x-show="format === 'pdf'" class="bg-white border rounded shadow-md text-left h-[520px] pb-6 flex flex-col">
                            <div class="border-b-4 border-[#356AE6] p-6 text-center space-y-1">
                                <h1 class="text-[15px] font-sans font-black tracking-tight text-slate-950 uppercase">Organization Name</h1>
                                <h2 class="text-[13px] font-bold text-slate-700" x-text="reportType"></h2>
                                <p class="text-[10px] font-medium text-slate-500">Date: 01/04/2026 - 11/04/2026</p>
                            </div>
                            
                            <div class="px-6 py-5 flex-1 overflow-y-auto">
                                <div class="mb-5">
                                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#356AE6] mb-2 border-b border-blue-100 pb-1">Summary</h3>
                                    <div class="grid grid-cols-3 gap-2 text-[11px]">
                                        <div class="bg-slate-50 rounded border border-slate-100 p-2 text-center">
                                            <span class="text-slate-500 block text-[9px] uppercase font-bold tracking-wider mb-1">Total Records</span>
                                            <span class="font-black text-slate-900 text-sm">120</span>
                                        </div>
                                        <div class="bg-emerald-50 rounded border border-emerald-100 p-2 text-center">
                                            <span class="text-emerald-700 block text-[9px] uppercase font-bold tracking-wider mb-1">Active</span>
                                            <span class="font-black text-emerald-800 text-sm">95</span>
                                        </div>
                                        <div class="bg-rose-50 rounded border border-rose-100 p-2 text-center">
                                            <span class="text-rose-700 block text-[9px] uppercase font-bold tracking-wider mb-1">Inactive</span>
                                            <span class="font-black text-rose-800 text-sm">25</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#356AE6] mb-2 border-b border-blue-100 pb-1">Charts</h3>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="h-16 bg-slate-50 border rounded flex items-center justify-center p-2 text-[8px] text-slate-400">Pie Chart visual</div>
                                        <div class="h-16 bg-slate-50 border rounded flex items-center justify-center p-2 text-[8px] text-slate-400">Bar Chart visual</div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-[#356AE6] mb-2 border-b border-blue-100 pb-1">Data Table</h3>
                                    <table class="w-full text-[9px] text-left">
                                        <thead class="bg-slate-100 text-slate-600">
                                            <tr>
                                                <th class="p-1.5 font-bold border-b border-slate-200">ID</th>
                                                <th class="p-1.5 font-bold border-b border-slate-200">Name</th>
                                                <th class="p-1.5 font-bold border-b border-slate-200">Team</th>
                                                <th class="p-1.5 font-bold border-b border-slate-200 text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-slate-600">
                                            <tr class="border-b border-slate-100">
                                                <td class="p-1.5 font-medium text-slate-900 border-r border-slate-100">#TR-001</td>
                                                <td class="p-1.5 border-r border-slate-100">Sok Sao</td>
                                                <td class="p-1.5 border-r border-slate-100">IT Dept</td>
                                                <td class="p-1.5 text-center text-emerald-600 font-bold">Active</td>
                                            </tr>
                                            <tr class="border-b border-slate-100 bg-slate-50">
                                                <td class="p-1.5 font-medium text-slate-900 border-r border-slate-100">#TR-002</td>
                                                <td class="p-1.5 border-r border-slate-100">Khmer Angkor</td>
                                                <td class="p-1.5 border-r border-slate-100">HR Dept</td>
                                                <td class="p-1.5 text-center text-rose-600 font-bold">Inactive</td>
                                            </tr>
                                            <tr>
                                                <td class="p-1.5 font-medium text-slate-900 border-r border-slate-100">#TR-003</td>
                                                <td class="p-1.5 border-r border-slate-100">Bopha Nary</td>
                                                <td class="p-1.5 border-r border-slate-100">Ops Dept</td>
                                                <td class="p-1.5 text-center text-emerald-600 font-bold">Active</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="border-t border-slate-100 px-6 pt-3 mt-auto">
                                <div class="flex justify-between items-center">
                                    <p class="text-[9px] text-slate-400 font-medium">Generated by: Admin System</p>
                                    <p class="text-[9px] text-slate-400 font-medium whitespace-nowrap" x-text="new Date().toLocaleDateString()"></p>
                                </div>
                            </div>
                        </div>

                        <!-- EXCEL Format Preview -->
                        <div x-show="format === 'excel'" style="display:none;" class="bg-white border rounded shadow-md h-[520px] flex flex-col overflow-hidden">
                            <!-- Excel Header/Toolbar -->
                            <div class="bg-green-700 p-2 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="bg-white text-green-700 h-6 w-6 rounded-sm flex items-center justify-center font-bold text-[10px]">X</div>
                                    <span class="text-white text-[11px] font-semibold" x-text="reportType + '.xlsx'"></span>
                                </div>
                            </div>
                            <div class="bg-slate-100 border-b p-1 flex gap-1">
                                <div class="h-4 w-12 bg-white rounded border"></div>
                                <div class="h-4 w-12 bg-white rounded border"></div>
                                <div class="h-4 w-12 bg-white rounded border"></div>
                            </div>
                            <!-- Data -->
                            <div class="flex-1 p-2 bg-white">
                                <table class="w-full text-[9px] border-collapse border border-slate-300">
                                    <thead class="bg-slate-100 text-slate-700 sticky top-0">
                                        <tr>
                                            <th class="border border-slate-300 w-6 bg-slate-200 text-center"></th>
                                            <th class="border border-slate-300 p-1 text-center bg-slate-200 w-10">A</th>
                                            <th class="border border-slate-300 p-1 text-center bg-slate-200 w-24">B</th>
                                            <th class="border border-slate-300 p-1 text-center bg-slate-200 w-24">C</th>
                                            <th class="border border-slate-300 p-1 text-center bg-slate-200 w-16">D</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-slate-800">
                                        <tr>
                                            <td class="border border-slate-300 bg-slate-200 text-center font-bold text-slate-500">1</td>
                                            <td class="border border-slate-300 p-1 font-bold bg-slate-50">ID</td>
                                            <td class="border border-slate-300 p-1 font-bold bg-slate-50">Name</td>
                                            <td class="border border-slate-300 p-1 font-bold bg-slate-50">Team</td>
                                            <td class="border border-slate-300 p-1 font-bold bg-slate-50 text-center">Status</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-slate-300 bg-slate-200 text-center text-slate-500">2</td>
                                            <td class="border border-slate-300 p-1">#TR-001</td>
                                            <td class="border border-slate-300 p-1">Sok Sao</td>
                                            <td class="border border-slate-300 p-1">IT Dept</td>
                                            <td class="border border-slate-300 p-1 text-center text-green-700 font-bold">Active</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-slate-300 bg-slate-200 text-center text-slate-500">3</td>
                                            <td class="border border-slate-300 p-1">#TR-002</td>
                                            <td class="border border-slate-300 p-1">Khmer Angkor</td>
                                            <td class="border border-slate-300 p-1">HR Dept</td>
                                            <td class="border border-slate-300 p-1 text-center text-red-600 font-bold">Inactive</td>
                                        </tr>
                                        <tr>
                                            <td class="border border-slate-300 bg-slate-200 text-center text-slate-500">4</td>
                                            <td class="border border-slate-300 p-1">#TR-003</td>
                                            <td class="border border-slate-300 p-1">Bopha Nary</td>
                                            <td class="border border-slate-300 p-1">Ops Dept</td>
                                            <td class="border border-slate-300 p-1 text-center text-green-700 font-bold">Active</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Sheets -->
                            <div class="bg-slate-100 border-t flex px-2 overflow-x-auto border-b border-slate-300 gap-0.5">
                                <div class="px-3 py-1 bg-white font-bold text-green-700 text-[9px] border border-b-0 rounded-t border-slate-300">Summary</div>
                                <div class="px-3 py-1 text-slate-500 font-semibold text-[9px] hover:bg-slate-200 border border-transparent cursor-pointer rounded-t">Trainees</div>
                                <div class="px-3 py-1 text-slate-500 font-semibold text-[9px] hover:bg-slate-200 border border-transparent cursor-pointer rounded-t">Grade 3</div>
                                <div class="px-3 py-1 text-slate-500 font-semibold text-[9px] hover:bg-slate-200 border border-transparent cursor-pointer rounded-t">Intern</div>
                                <div class="px-3 py-1 text-slate-500 font-semibold text-[9px] hover:bg-slate-200 border border-transparent cursor-pointer rounded-t">Docs</div>
                            </div>
                            <div class="h-4 bg-slate-200"></div>
                        </div>

                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-white px-6 py-5 flex items-center justify-between border-t border-slate-100 rounded-b-2xl relative z-20">
                    <!-- Left Side -->
                    <button @click="closeModal()" type="button" class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 focus:ring-2 focus:ring-slate-200 cursor-pointer">
                        Cancel
                    </button>
                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-3">
                        <button @click="triggerPreview()" type="button" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:ring-2 focus:ring-slate-200 flex items-center gap-2 cursor-pointer">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Preview
                        </button>
                        <button @click="exportAction('pdf')" type="button" class="rounded-xl bg-[#356AE6] hover:bg-blue-700 px-6 py-2.5 text-sm font-bold text-white shadow-[0_4px_10px_rgba(53,106,230,0.2)] transition focus:ring-2 focus:ring-offset-2 focus:ring-[#356AE6] flex items-center gap-2 cursor-pointer relative z-30" :class="{'opacity-50 pointer-events-none': isExportingPdf || isExportingExcel}">
                            <svg x-show="!isExportingPdf" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                            <svg x-show="isExportingPdf" style="display: none;" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isExportingPdf ? 'Exporting...' : 'Export PDF'"></span>
                        </button>
                        <button @click="exportAction('excel')" type="button" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 px-6 py-2.5 text-sm font-bold text-white shadow-[0_4px_10px_rgba(5,150,105,0.2)] transition focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 flex items-center gap-2 cursor-pointer relative z-30" :class="{'opacity-50 pointer-events-none': isExportingExcel || isExportingPdf}">
                            <svg x-show="!isExportingExcel" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            <svg x-show="isExportingExcel" style="display: none;" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="isExportingExcel ? 'Exporting...' : 'Export Excel'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reportExportManager', () => ({
            isModalOpen: false,
            format: 'pdf',
            reportType: 'Trainee Report',
            isPreviewing: false,
            isExportingPdf: false,
            isExportingExcel: false,

            openModal() {
                this.isModalOpen = true;
                setTimeout(() => { document.body.style.overflow = 'hidden'; }, 10);
            },

            closeModal() {
                this.isModalOpen = false;
                setTimeout(() => { document.body.style.overflow = ''; }, 300);
            },

            refreshPreview() {
                this.isPreviewing = true;
                setTimeout(() => {
                    this.isPreviewing = false;
                }, 800);
            },
            
            triggerPreview() {
                this.refreshPreview();
            },

            exportAction(type) {
                if(this.isExportingPdf || this.isExportingExcel) return;
                
                if (type === 'pdf') {
                    this.isExportingPdf = true;
                } else {
                    this.isExportingExcel = true;
                }
                
                this.format = type; // sync preview pane to the clicked format
                this.refreshPreview();
                
                // Here is where you would do: window.location.href = `/export/${type}?report=${this.reportType}...`;

                setTimeout(() => {
                    this.isExportingPdf = false;
                    this.isExportingExcel = false;
                    this.closeModal();
                    
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'success',
                            title: 'Export success',
                            text: `Your ${type.toUpperCase()} report has been generated and downloaded successfully.`,
                            confirmButtonText: 'Great!',
                            confirmButtonColor: type === 'pdf' ? '#356AE6' : '#059669',
                            timer: 3000,
                            customClass: {
                                popup: 'swal2-kh-popup',
                                title: 'swal2-kh-title',
                                htmlContainer: 'swal2-kh-content',
                                confirmButton: 'swal2-kh-confirm'
                            }
                        });
                    } else {
                        alert('Export Success');
                    }
                }, 2000);
            }
        }));
    });
</script>
