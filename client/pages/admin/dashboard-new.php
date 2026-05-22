<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Alumni System</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .dashboard-container {
            padding: 2rem;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .dashboard-grid {
            display: grid;
            gap: 1.5rem;
        }
        
        /* Metrics Row */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            gap: 1rem;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .metric-icon {
            font-size: 2.5rem;
            flex-shrink: 0;
        }
        
        .metric-content h3 {
            margin: 0 0 0.25rem 0;
            font-size: 0.875rem;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-700);
            margin: 0;
        }
        
        .metric-change {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
        }
        
        .metric-change.positive {
            color: #10b981;
        }
        
        .metric-change.negative {
            color: #ef4444;
        }
        
        /* Charts Row */
        .charts-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1rem;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .chart-header h3 {
            margin: 0;
            font-size: 1.125rem;
            color: var(--gray-800);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        /* Tables Row */
        .tables-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1rem;
        }
        
        .table-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .table-header h3 {
            margin: 0;
            font-size: 1.125rem;
            color: var(--gray-800);
        }
        
        .table-header a {
            font-size: 0.875rem;
            color: var(--primary-600);
            text-decoration: none;
        }
        
        .table-header a:hover {
            text-decoration: underline;
        }
        
        .compact-table {
            width: 100%;
            font-size: 0.875rem;
        }
        
        .compact-table th {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 2px solid var(--gray-200);
            color: var(--gray-700);
            font-weight: 600;
        }
        
        .compact-table td {
            padding: 0.5rem;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .compact-table tbody tr:hover {
            background: var(--gray-50);
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        @media (max-width: 1200px) {
            .charts-row,
            .tables-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .metrics-row {
                grid-template-columns: 1fr;
            }
            
            .metric-card {
                padding: 1rem;
            }
            
            .metric-icon {
                font-size: 2rem;
            }
            
            .metric-value {
                font-size: 1.5rem;
            }
            
            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        
        <!-- Metrics Row -->
        <div class="metrics-row">
            <div class="metric-card">
                <div class="metric-icon">👥</div>
                <div class="metric-content">
                    <h3>Total Alumni</h3>
                    <div class="metric-value" id="totalAlumni">0</div>
                    <div class="metric-change" id="alumniChange">Loading...</div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">✅</div>
                <div class="metric-content">
                    <h3>Verified</h3>
                    <div class="metric-value" id="verifiedCount">0</div>
                    <div class="metric-change" id="pendingCount">0 pending</div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">📅</div>
                <div class="metric-content">
                    <h3>Events</h3>
                    <div class="metric-value" id="eventsCount">0</div>
                    <div class="metric-change" id="upcomingEvents">0 upcoming</div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">🎯</div>
                <div class="metric-content">
                    <h3>Active Users</h3>
                    <div class="metric-value" id="activeUsers">0</div>
                    <div class="metric-change">Last 7 days</div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="charts-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Alumni by College</h3>
                </div>
                <div class="chart-container">
                    <canvas id="collegeChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3>Registration Trend</h3>
                </div>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tables Row -->
        <div class="tables-row">
            <div class="table-card">
                <div class="table-header">
                    <h3>Recent Registrations</h3>
                    <a href="#/admin/alumni-verification" id="recentRegistrationsViewAll">View All</a>
                </div>
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>College</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recentRegistrations">
                        <tr>
                            <td colspan="3" style="text-align: center;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="table-card">
                <div class="table-header">
                    <h3>Top Programs</h3>
                </div>
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Alumni</th>
                            <th>New</th>
                        </tr>
                    </thead>
                    <tbody id="topPrograms">
                        <tr>
                            <td colspan="3" style="text-align: center;">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="../../assets/js/api.js"></script>
    <script>
        let collegeChart = null;
        let trendChart = null;
        
        // Load dashboard data
        async function loadDashboard() {
            try {
                const response = await API.analytics.getDashboard();
                const data = response.data;
                
                // Update metrics
                document.getElementById('totalAlumni').textContent = data.stats.total_alumni || 0;
                document.getElementById('verifiedCount').textContent = data.stats.verified_count || 0;
                document.getElementById('pendingCount').textContent = `${data.stats.pending_count || 0} pending`;
                document.getElementById('activeUsers').textContent = data.stats.active_last_week || 0;
                
                // Event stats
                if (data.event_stats) {
                    document.getElementById('eventsCount').textContent = data.event_stats.total_events || 0;
                    document.getElementById('upcomingEvents').textContent = `${data.event_stats.upcoming_events || 0} upcoming`;
                }
                
                // Growth percentage
                const growthPercent = data.growth_percentage || 0;
                const changeEl = document.getElementById('alumniChange');
                if (growthPercent > 0) {
                    changeEl.textContent = `+${growthPercent}% from last month`;
                    changeEl.className = 'metric-change positive';
                } else if (growthPercent < 0) {
                    changeEl.textContent = `${growthPercent}% from last month`;
                    changeEl.className = 'metric-change negative';
                } else {
                    changeEl.textContent = 'No change from last month';
                    changeEl.className = 'metric-change';
                }
                
                // Render charts
                renderCollegeChart(data.by_college || []);
                renderTrendChart(data.registration_trend || []);
                
                // Populate tables
                populateRecentTable(data.recent_registrations || []);
                populateTopPrograms(data.top_programs || []);
                
            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        }
        
        // Render college distribution chart
        function renderCollegeChart(data) {
            const ctx = document.getElementById('collegeChart');
            
            if (collegeChart) {
                collegeChart.destroy();
            }
            
            const labels = data.map(d => d.code || d.name);
            const values = data.map(d => d.alumni_count || 0);
            
            collegeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444',
                            '#8b5cf6',
                            '#ec4899',
                            '#14b8a6',
                            '#f97316'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
        
        // Render registration trend chart
        function renderTrendChart(data) {
            const ctx = document.getElementById('trendChart');
            
            if (trendChart) {
                trendChart.destroy();
            }
            
            const labels = data.map(d => d.month);
            const registrations = data.map(d => d.registrations || 0);
            const verified = data.map(d => d.verified || 0);
            
            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Registrations',
                            data: registrations,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Verified',
                            data: verified,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Populate recent registrations table
        function populateRecentTable(data) {
            const tbody = document.getElementById('recentRegistrations');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center;">No recent registrations</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.slice(0, 5).map(a => {
                let statusBadge = '';
                if (a.verification_status === 'verified') {
                    statusBadge = '<span class="badge badge-success">Verified</span>';
                } else if (a.verification_status === 'pending') {
                    statusBadge = '<span class="badge badge-warning">Pending</span>';
                } else if (a.verification_status === 'rejected') {
                    statusBadge = '<span class="badge badge-danger">Rejected</span>';
                }
                
                return `
                    <tr>
                        <td>${a.name || 'N/A'}</td>
                        <td>${a.college_name || 'N/A'}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
            }).join('');
        }
        
        // Populate top programs table
        function populateTopPrograms(data) {
            const tbody = document.getElementById('topPrograms');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align: center;">No data available</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.slice(0, 5).map(p => `
                <tr>
                    <td>${p.code || p.name || 'N/A'}</td>
                    <td>${p.alumni_count || 0}</td>
                    <td>${p.new_this_month || 0}</td>
                </tr>
            `).join('');
        }
        
        // Initialize immediately (SPA navigation doesn't trigger DOMContentLoaded)
        (function() {
            function updateVerificationShortcutVisibility() {
                var link = document.getElementById('recentRegistrationsViewAll');
                if (!link || typeof API === 'undefined' || typeof API.getUser !== 'function') {
                    return;
                }

                var user = API.getUser() || {};
                if (!['admin', 'system_admin'].includes(user.role)) {
                    link.style.display = 'none';
                }
            }

            updateVerificationShortcutVisibility();
            loadDashboard();
            
            // Refresh every 5 minutes
            setInterval(loadDashboard, 300000);
        })();
    </script>
</body>
</html>
