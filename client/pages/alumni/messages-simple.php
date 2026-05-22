<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Alumni System</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/components.css">
    <style>
        .messages-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .search-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .search-filters {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
        }
        
        .form-input, .form-select {
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: var(--primary-600);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-700);
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .alumni-results {
            display: grid;
            gap: 1rem;
        }
        
        .alumni-card {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            transition: all 0.2s;
        }
        
        .alumni-card:hover {
            background: var(--gray-50);
            border-color: var(--primary-300);
        }
        
        .alumni-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            background: var(--gray-200);
        }
        
        .alumni-info {
            flex: 1;
        }
        
        .alumni-info h4 {
            margin: 0 0 0.25rem 0;
            font-size: 1.125rem;
        }
        
        .alumni-info p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--gray-600);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--gray-500);
        }
        
        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }
        
        .error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="messages-container">
        <h1>Search Alumni</h1>
        
        <div class="search-section">
            <h2>Find Alumni to Message</h2>
            
            <div class="search-filters">
                <input 
                    type="text" 
                    id="searchQuery" 
                    class="form-input" 
                    placeholder="Search by name or email..."
                >
                
                <div class="filter-row">
                    <select id="filterBatch" class="form-select">
                        <option value="">All Batches</option>
                    </select>
                    
                    <select id="filterCollege" class="form-select">
                        <option value="">All Colleges</option>
                    </select>
                    
                    <select id="filterProgram" class="form-select">
                        <option value="">All Programs</option>
                    </select>
                </div>
                
                <button class="btn btn-primary" onclick="searchAlumni()">
                    Search Alumni
                </button>
            </div>
            
            <div id="errorMessage"></div>
            <div id="searchResults"></div>
        </div>
    </div>
    
    <script src="../../assets/js/api.js"></script>
    <script>
        // Initialize filters
        async function initializeFilters() {
            try {
                // Load batch years
                const currentYear = new Date().getFullYear();
                const batchSelect = document.getElementById('filterBatch');
                for (let year = currentYear; year >= currentYear - 40; year--) {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    batchSelect.appendChild(option);
                }
                
                // Load colleges
                const collegesResponse = await API.organization.getColleges();
                const colleges = collegesResponse.data || [];
                const collegeSelect = document.getElementById('filterCollege');
                colleges.forEach(college => {
                    const option = document.createElement('option');
                    option.value = college.id;
                    option.textContent = college.name;
                    collegeSelect.appendChild(option);
                });
                
                // Load programs when college changes
                document.getElementById('filterCollege').addEventListener('change', async function() {
                    const collegeId = this.value;
                    const programSelect = document.getElementById('filterProgram');
                    programSelect.innerHTML = '<option value="">All Programs</option>';
                    
                    if (!collegeId) return;
                    
                    try {
                        const programsResponse = await API.organization.getPrograms(collegeId);
                        const programs = programsResponse.data || [];
                        programs.forEach(program => {
                            const option = document.createElement('option');
                            option.value = program.id;
                            option.textContent = program.name;
                            programSelect.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading programs:', error);
                    }
                });
                
            } catch (error) {
                console.error('Error initializing filters:', error);
                showError('Failed to load filters. Please refresh the page.');
            }
        }
        
        // Search alumni
        async function searchAlumni() {
            const query = document.getElementById('searchQuery').value.trim();
            const batch = document.getElementById('filterBatch').value;
            const college = document.getElementById('filterCollege').value;
            const program = document.getElementById('filterProgram').value;
            
            const resultsDiv = document.getElementById('searchResults');
            const errorDiv = document.getElementById('errorMessage');
            
            errorDiv.innerHTML = '';
            resultsDiv.innerHTML = '<div class="loading">Searching...</div>';
            
            try {
                const params = {};
                if (query) params.q = query;
                if (batch) params.batch = batch;
                if (college) params.college = college;
                if (program) params.program = program;
                
                const response = await API.messaging.searchAlumni(params);
                const alumni = response.data || [];
                
                if (alumni.length === 0) {
                    resultsDiv.innerHTML = `
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <p>No alumni found matching your criteria</p>
                            <p style="font-size: 0.875rem;">Try adjusting your search filters</p>
                        </div>
                    `;
                    return;
                }
                
                resultsDiv.innerHTML = `
                    <h3 style="margin-bottom: 1rem;">${alumni.length} Alumni Found</h3>
                    <div class="alumni-results">
                        ${alumni.map(a => `
                            <div class="alumni-card">
                                <img 
                                    src="${a.profile_image || '../../assets/images/default-avatar.png'}" 
                                    alt="${escapeHtml(a.name)}"
                                    class="alumni-avatar"
                                    onerror="this.src='../../assets/images/default-avatar.png'"
                                >
                                <div class="alumni-info">
                                    <h4>${escapeHtml(a.name)}</h4>
                                    <p>${escapeHtml(a.email || 'No email')}</p>
                                    <p>${escapeHtml(a.college_name || 'N/A')} - ${escapeHtml(a.program_name || 'N/A')}</p>
                                    <p>Batch ${a.batch_year || 'N/A'}</p>
                                </div>
                                <button onclick="startConversation(${a.id}, '${escapeHtml(a.name)}')" class="btn btn-sm btn-primary">
                                    Message
                                </button>
                            </div>
                        `).join('')}
                    </div>
                `;
                
            } catch (error) {
                console.error('Error searching alumni:', error);
                showError('Failed to search alumni. Please try again.');
                resultsDiv.innerHTML = '';
            }
        }
        
        // Start conversation
        async function startConversation(alumniId, name) {
            try {
                const response = await API.messaging.createConversation({
                    participant_ids: [alumniId]
                });
                
                const conversationId = response.data.id || response.data.conversation_id;
                
                if (conversationId) {
                    alert(`Conversation with ${name} created! Redirecting...`);
                    window.location.hash = `#/alumni/conversation/${conversationId}`;
                } else {
                    alert('Failed to create conversation. Please try again.');
                }
            } catch (error) {
                console.error('Error creating conversation:', error);
                alert('Failed to start conversation. Please try again.');
            }
        }
        
        // Show error message
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.innerHTML = `<div class="error">${message}</div>`;
        }
        
        // Escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Allow Enter key to search
        document.getElementById('searchQuery').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchAlumni();
            }
        });
        
        // Initialize immediately (SPA navigation doesn't trigger DOMContentLoaded)
        initializeFilters();
    </script>
</body>
</html>
