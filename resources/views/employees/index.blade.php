@extends('layouts.app')

@section('content')
<div id="content-area">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <h1 style="margin: 0; font-size: 1.75rem; font-weight: 700; color: var(--text-main);">Matriz de Habilidades</h1>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.95rem;">Define las capacidades técnicas de cada colaborador</p>
        </div>
        
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button id="clearFilters" style="padding: 0.6rem 1rem; background: white; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; color: var(--text-muted); font-size: 0.85rem; font-weight: 600; display: none; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                Limpiar Filtros
            </button>
            <div style="position: relative;">
                <input type="text" id="employeeSearch" placeholder="Buscar colaborador..." 
                       style="padding: 0.6rem 1rem 0.6rem 2.5rem; background: white; border: 1px solid var(--border-color); border-radius: 8px; width: 260px; outline: none; transition: all 0.2s; color: var(--text-main); font-size: 0.9rem;">
                <svg style="position: absolute; left: 0.8rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    @php
        $skillColors = [
            ['bg' => '#eef2ff', 'text' => '#4f46e5', 'border' => '#818cf8', 'dot' => '#4f46e5'], // Indigo
            ['bg' => '#ecfdf5', 'text' => '#059669', 'border' => '#34d399', 'dot' => '#059669'], // Emerald
            ['bg' => '#fff7ed', 'text' => '#d97706', 'border' => '#fbbf24', 'dot' => '#d97706'], // Amber
            ['bg' => '#fdf2f8', 'text' => '#db2777', 'border' => '#f472b6', 'dot' => '#db2777'], // Pink
            ['bg' => '#f0f9ff', 'text' => '#0284c7', 'border' => '#38bdf8', 'dot' => '#0284c7'], // Sky
            ['bg' => '#faf5ff', 'text' => '#9333ea', 'border' => '#c084fc', 'dot' => '#9333ea'], // Purple
            ['bg' => '#fff1f2', 'text' => '#e11d48', 'border' => '#fb7185', 'dot' => '#e11d48'], // Rose
            ['bg' => '#f0fdf4', 'text' => '#16a34a', 'border' => '#4ade80', 'dot' => '#16a34a'], // Green
            ['bg' => '#fefce8', 'text' => '#ca8a04', 'border' => '#facc15', 'dot' => '#ca8a04'], // Yellow
            ['bg' => '#f5f3ff', 'text' => '#7c3aed', 'border' => '#a78bfa', 'dot' => '#7c3aed'], // Violet
        ];
    @endphp

    <div class="card" style="background: white; border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); flex: 1; display: flex; flex-direction: column; min-height: 0;">
        <div class="table-responsive" style="overflow: auto; flex: 1; min-height: 0;">
            <table id="skillsTable" style="width: 100%; border-collapse: separate; border-spacing: 0; min-width: 900px;">
                <thead>
                    <tr>
                        <th class="sticky-col-header">
                            Colaborador
                        </th>
                        @foreach($areas as $index => $area)
                            @php
                                $color = $skillColors[$index % count($skillColors)];
                            @endphp
                            <th class="skill-header" 
                                data-area-id="{{ $area->id }}"
                                data-column-index="{{ $index + 1 }}"
                                data-active-color="{{ $color['dot'] }}"
                                style="padding: 1rem; background: #f8fafc; border-bottom: 2px solid var(--border-color); border-right: 1px solid var(--border-color); border-top: 4px solid {{ $color['border'] }}; min-width: 130px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; transition: all 0.2s; color: {{ $color['text'] }}; text-align: center; position: relative;">
                                {{ $area->name }}
                                <div class="filter-dot" style="position: absolute; bottom: 0; left: 0; width: 100%; height: 4px; background: {{ $color['dot'] }}; opacity: 0; transition: opacity 0.2s;"></div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr class="employee-row" data-name="{{ strtolower($employee->name) }}">
                            <td class="sticky-col-cell">
                                {{ $employee->name }}
                            </td>
                            @foreach($areas as $area)
                                @php 
                                    $hasArea = $employee->areas->contains($area->id);
                                @endphp
                                <td style="padding: 0; text-align: center; border-bottom: 1px solid var(--border-color); border-right: 1px solid var(--border-color);">
                                    <label style="display: flex; align-items: center; justify-content: center; padding: 0.85rem; cursor: pointer; width: 100%; height: 100%;">
                                        <input type="checkbox" 
                                               class="skill-checkbox" 
                                               data-employee-id="{{ $employee->id }}" 
                                               data-area-id="{{ $area->id }}"
                                               {{ $hasArea ? 'checked' : '' }}
                                               style="width: 18px; height: 18px; cursor: pointer; accent-color: var(--primary);">
                                    </label>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" style="position: fixed; bottom: 2rem; right: 2rem; padding: 0.75rem 1.25rem; background: #0f172a; color: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: none; z-index: 9999; font-size: 0.85rem; font-weight: 500; border-left: 4px solid var(--primary); animation: slideIn 0.3s ease-out; max-width: 320px; display: flex; align-items: center; gap: 0.5rem;">
        <span id="toast-icon">&#10003;</span>
        <span id="toast-msg">Cambio guardado</span>
    </div>

    <style>
        @keyframes slideIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        #employeeSearch:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .employee-row:hover td {
            background-color: #f8fafc !important; /* Extremely subtle hover */
        }

        .sticky-col-header {
            text-align: left;
            padding: 1rem;
            position: sticky !important;
            left: 0;
            background: #f1f5f9 !important;
            z-index: 40;
            border-bottom: 2px solid var(--border-color) !important;
            border-right: 2px solid var(--border-color);
            width: 260px;
            font-weight: 700;
            color: #475569;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sticky-col-cell {
            padding: 0.85rem 1rem;
            position: sticky !important;
            left: 0;
            background: white !important;
            z-index: 20;
            border-bottom: 1px solid var(--border-color);
            border-right: 2px solid var(--border-color);
            font-weight: 600;
            color: #003049;
            font-size: 0.9rem;
            box-shadow: 2px 0 5px rgba(0,0,0,0.02);
        }

        .employee-row:hover .sticky-col-cell {
            background-color: #f8fafc !important;
        }

        .skill-header:hover {
            background-color: #f1f5f9 !important;
            color: var(--primary) !important;
        }

        .skill-header {
            position: relative;
        }

        .filter-dot {
            transition: opacity 0.2s ease-in-out;
            opacity: 0;
        }

        .skill-header.active-filter .filter-dot {
            opacity: 1;
        }

        .table-responsive::-webkit-scrollbar { width: 6px; height: 6px; }
        .table-responsive::-webkit-scrollbar-track { background: #f1f5f9; }
        .table-responsive::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <script>
        let currentFilterColumn = null;
        let currentSearchQuery = '';

        const table = document.getElementById('skillsTable');
        const searchInput = document.getElementById('employeeSearch');
        const clearBtn = document.getElementById('clearFilters');
        const rows = document.querySelectorAll('.employee-row');
        const headers = document.querySelectorAll('.skill-header');

        function applyFilters() {
            const query = (currentSearchQuery || '').toLowerCase().trim();
            
            rows.forEach(row => {
                const name = (row.dataset.name || row.getAttribute('data-name') || '').toLowerCase();
                const nameMatch = !query || name.includes(query);
                
                let skillMatch = true;
                if (currentFilterColumn !== null) {
                    // Search for the checkbox within this row that matches the selected area ID
                    const checkbox = row.querySelector(`.skill-checkbox[data-area-id="${currentFilterColumn}"]`);
                    skillMatch = !!(checkbox && checkbox.checked);
                }

                row.style.display = (nameMatch && skillMatch) ? '' : 'none';
            });

            clearBtn.style.display = (currentFilterColumn !== null || currentSearchQuery !== '') ? 'inline-block' : 'none';
        }

        searchInput.addEventListener('input', (e) => {
            currentSearchQuery = e.target.value.toLowerCase();
            applyFilters();
        });

        headers.forEach(header => {
            header.addEventListener('click', function(e) {
                const target = e.currentTarget;
                const areaId = target.dataset.areaId;
                const dot = target.querySelector('.filter-dot');
                
                if (currentFilterColumn == areaId) {
                    currentFilterColumn = null;
                    target.classList.remove('active-filter');
                    target.style.background = '#f8fafc';
                    if (dot) dot.style.opacity = '0';
                } else {
                    headers.forEach(h => {
                        h.classList.remove('active-filter');
                        h.style.background = '#f8fafc';
                        const hDot = h.querySelector('.filter-dot');
                        if (hDot) hDot.style.opacity = '0';
                    });
                    
                    const activeColor = target.dataset.activeColor;
                    target.classList.add('active-filter');
                    target.style.background = target.style.borderTopColor.replace('rgb', 'rgba').replace(')', ', 0.1)'); // Very light tint
                    if (dot) {
                        dot.style.background = activeColor;
                        dot.style.opacity = '1';
                    }
                    currentFilterColumn = areaId;
                }
                applyFilters();
            });
        });

        clearBtn.addEventListener('click', () => {
            currentFilterColumn = null;
            currentSearchQuery = '';
            searchInput.value = '';
            headers.forEach(h => {
                h.classList.remove('active-filter');
                h.style.background = '#f8fafc';
                const dot = h.querySelector('.filter-dot');
                if (dot) dot.style.opacity = '0';
            });
            applyFilters();
        });

        document.querySelectorAll('.skill-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', async function() {
                const employeeId = this.dataset.employeeId;
                const areaId = this.dataset.areaId;
                const active = this.checked;
                const originalState = !active; // save to revert on error

                try {
                    const response = await fetch('{{ route("employees.toggle_area") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ employee_id: employeeId, area_id: areaId, active: active })
                    });

                    const data = await response.json();
                    console.log('[ToggleArea] Response:', data);

                    if (response.ok && data.success) {
                        showToast(data.message || 'Cambio guardado', 'success');
                        if (currentFilterColumn !== null && !active) applyFilters();
                    } else {
                        console.error('[ToggleArea] Server error:', data);
                        showToast(data.message || 'Error al guardar el cambio', 'error');
                        this.checked = originalState; // revert
                    }
                } catch (error) {
                    console.error('[ToggleArea] Fetch error:', error);
                    showToast('Error de conexión. Intenta de nuevo.', 'error');
                    this.checked = originalState; // revert
                }
            });
        });

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const msg = document.getElementById('toast-msg');
            const icon = document.getElementById('toast-icon');

            msg.textContent = message;

            if (type === 'error') {
                toast.style.borderLeftColor = '#ef4444';
                toast.style.background = '#1c1917';
                icon.textContent = '✗';
                icon.style.color = '#ef4444';
            } else {
                toast.style.borderLeftColor = 'var(--primary)';
                toast.style.background = '#0f172a';
                icon.textContent = '✓';
                icon.style.color = 'var(--primary)';
            }

            toast.style.display = 'flex';
            setTimeout(() => toast.style.display = 'none', 3000);
        }
    </script>

</div> <!-- End content-area -->
@endsection
