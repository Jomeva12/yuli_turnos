@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem; background: #f8fafc; height: 100vh; display: flex; flex-direction: column; overflow: hidden;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <a href="{{ route('shifts.index') }}" style="text-decoration: none; color: #64748b; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                ← Volver a la Planilla
            </a>
            <h1 style="margin: 0; color: #0f172a; font-size: 1.75rem; font-weight: 800;">
                Cronograma de Cobertura: {{ $carbonDate->translatedFormat('l d \d\e F Y') }}
            </h1>
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center;">
            <input type="date" value="{{ $date }}" onchange="window.location.href='{{ route('shifts.timeline') }}/' + this.value" 
                   style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #e2e8f0; font-family: inherit;">
            <button onclick="window.print()" style="padding: 0.5rem 1.25rem; background: white; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; font-weight: 600;">
                🖨️ Imprimir
            </button>
        </div>
    </div>

    <!-- Legend -->
    <div style="display: flex; gap: 1.5rem; margin-bottom: 1.5rem; background: white; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #ec4899;"></span> Cosmético
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #8b5cf6;"></span> Electrodoméstico
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #f59e0b;"></span> Buffet
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #10b981;"></span> Marking
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #6366f1;"></span> Valery Camacho
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #3b82f6;"></span> Domicilio
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #64748b;"></span> General
        </div>
    </div>

    <!-- Timeline Wrapper -->
    <div class="timeline-outer-wrapper" style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); flex: 1; display: flex; flex-direction: column; min-height: 0;">
        <div class="timeline-inner-wrapper" style="overflow-x: auto; flex: 1; display: flex; flex-direction: column; min-height: 0;">
            <div class="timeline-container" style="min-width: 1200px; padding: 1.5rem; position: relative; flex: 1; display: flex; flex-direction: column; min-height: 0;">
                
                <!-- Progress lines Indicator -->
                <div id="timeIndicatorWrapper" style="display: grid; grid-template-columns: 200px repeat(960, 1fr); position: absolute; top: 1.5rem; bottom: 1.5rem; left: 1.5rem; right: 1.5rem; pointer-events: none; z-index: 40;">
                    <div id="indicatorsGridArea" style="grid-column: 2 / span 960; position: relative; height: 100%;">
                        <!-- Current Time Indicator (Red - Auto) -->
                        <div id="currentTimeIndicator" style="position: absolute; top: 0; bottom: 0; width: 2px; background: #ef4444; display: none; transition: left 0.5s linear;">
                            <div style="position: absolute; top: -2px; left: -4px; border-left: 5px solid transparent; border-right: 5px solid transparent; border-top: 8px solid #ef4444;"></div>
                        </div>
                        
                        <!-- Manual Time Indicator (Blue - Draggable) -->
                        <div id="manualTimeIndicator" style="position: absolute; top: 0; bottom: 0; width: 3px; background: #3b82f6; cursor: grab; pointer-events: auto; z-index: 45;">
                            <!-- Drag Handle -->
                            <div id="manualHandle" style="position: absolute; top: -6px; left: -10px; width: 22px; height: 22px; background: #2563eb; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: grab; display: flex; align-items: center; justify-content: center;">
                                <div style="width: 2px; height: 10px; background: white; margin: 0 1px;"></div>
                                <div style="width: 2px; height: 10px; background: white; margin: 0 1px;"></div>
                            </div>
                            <!-- Tooltip -->
                            <div id="manualTooltip" style="position: absolute; top: 25px; left: 50%; transform: translateX(-50%); background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(8px); color: white; padding: 12px 16px; border-radius: 10px; font-size: 13px; white-space: nowrap; pointer-events: none; box-shadow: 0 15px 25px -5px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.1); z-index: 60; display: none; min-width: 180px;">
                                <div style="font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.2); margin-bottom: 8px; padding-bottom: 5px; display: flex; justify-content: space-between;">
                                    <span>Personal a las:</span>
                                    <span id="tooltipTime" style="color: #60a5fa;">00:00</span>
                                </div>
                                <div id="tooltipContent" style="display: flex; flex-direction: column; gap: 4px;">
                                    <!-- Dynamic Content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-header">
                    <div class="header-label-col">ÁREAS / PERSONAL</div>
                    @for($i = 0; $i < 64; $i++)
                        @php
                            $isHour = ($i % 4 == 0);
                            $hourValue = 6 + ($i / 4);
                        @endphp
                        <div class="time-tick {{ $isHour ? 'tick-hour' : 'tick-sub' }}" style="grid-column: span 15; border-left: 1px solid {{ $isHour ? '#cbd5e1' : '#f1f5f9' }};">
                            @if($isHour)
                                <span class="tick-label">{{ sprintf('%02d:00', $hourValue) }}</span>
                            @endif
                        </div>
                    @endfor
                </div>

                <!-- Scrollable Content Wrapper -->
                <div class="timeline-body-scroll" style="flex: 1; overflow-y: auto; padding-right: 5px;">
                    <!-- Areas Fijas -->
                    <div class="timeline-section-title">Áreas Fijas</div>
                    @foreach($areas as $area)
                        @php 
                            $areaShifts = $shiftsByArea[$area->id] ?? [];
                            $cleanAreaName = strtolower($area->name);
                            if($cleanAreaName === 'general') continue;
                            
                            $shiftsByEmployee = collect($areaShifts)->groupBy('employee_id');
                        @endphp
                        
                        @if(count($shiftsByEmployee) > 0)
                            @foreach($shiftsByEmployee as $employeeId => $employeeShifts)
                                <div class="timeline-row">
                                    <div class="row-label">
                                        @if($loop->first)
                                            <div style="font-weight: 800; color: #0f172a; font-size: 0.8rem;">{{ $area->name }}</div>
                                        @endif
                                        <div style="font-size: 0.65rem; color: #64748b; font-weight: 500;">
                                            {{ $employeeShifts->first()->employee->name }}
                                        </div>
                                    </div>
                                    <div class="grid-bg" style="grid-template-columns: repeat(960, 1fr);">
                                        @for($i = 0; $i < 64; $i++)
                                            @php
                                                $type = '';
                                                if ($i % 4 == 0) $type = 'cell-hour';
                                                elseif ($i % 4 == 2) $type = 'cell-30m';
                                                else $type = 'cell-15m';
                                            @endphp
                                            <div class="grid-cell {{ $type }}" style="grid-column: span 15;"></div>
                                        @endfor
                                    </div>
                                    @foreach($employeeShifts as $shift)
                                        @include('shifts.partials.timeline_blocks', ['shift' => $shift, 'isIndividual' => false])
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <div class="timeline-row opacity-50">
                                <div class="row-label" style="font-size: 0.8rem;">{{ $area->name }}</div>
                                <div class="grid-bg" style="grid-template-columns: repeat(960, 1fr);">
                                    @for($i = 0; $i < 64; $i++)
                                        <div class="grid-cell {{ $i % 4 == 0 ? 'cell-hour' : ($i % 4 == 2 ? 'cell-30m' : 'cell-15m') }}" style="grid-column: span 15;"></div>
                                    @endfor
                                </div>
                                <div style="grid-column: 2 / span 64; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #94a3b8; font-style: italic;">
                                    Sin asignaciones
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Area General -->
                    <div class="timeline-section-title" style="margin-top: 1.5rem;">Área General (Detalle por Empleado)</div>
                    @if(count($generalShifts) > 0)
                        @foreach($generalShifts as $shift)
                            <div class="timeline-row">
                                <div class="row-label individual">{{ $shift->employee->name }}</div>
                                <div class="grid-bg" style="grid-template-columns: repeat(960, 1fr);">
                                    @for($i = 0; $i < 64; $i++)
                                        @php
                                            $type = '';
                                            if ($i % 4 == 0) $type = 'cell-hour';
                                            elseif ($i % 4 == 2) $type = 'cell-30m';
                                            else $type = 'cell-15m';
                                        @endphp
                                        <div class="grid-cell {{ $type }}" style="grid-column: span 15;"></div>
                                    @endfor
                                </div>
                                @include('shifts.partials.timeline_blocks', ['shift' => $shift, 'isIndividual' => true])
                            </div>
                        @endforeach
                    @else
                        <div style="padding: 1rem; text-align: center; color: #94a3b8; font-style: italic; font-size: 0.75rem;">
                            No hay personal asignado a General.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .timeline-header {
        display: grid;
        grid-template-columns: 200px repeat(960, 1fr);
        margin-bottom: 2rem;
        position: sticky;
        top: 0;
        background: white;
        z-index: 30;
        height: 60px;
        align-items: flex-start;
        border-top: 2px solid #334155;
    }
    .header-label-col {
        font-size: 0.7rem;
        font-weight: 800;
        color: #94a3b8;
        padding-top: 10px;
    }
    
    /* Ruler ticks */
    .time-tick {
        position: relative;
        height: 12px;
        border-left: 1px solid #cbd5e1;
    }
    .tick-hour {
        height: 25px;
        border-left: 2px solid #334155;
    }
    .tick-half {
        height: 18px;
        border-left: 1px solid #64748b;
    }
    .tick-label {
        position: absolute;
        top: 30px;
        left: -18px;
        width: 40px;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 800;
        color: #1e293b;
    }

    .timeline-section-title {
        font-size: 0.75rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 1rem 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .timeline-section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    .timeline-row {
        display: grid;
        grid-template-columns: 200px repeat(960, 1fr);
        min-height: 38px;
        align-items: center;
        position: relative;
        margin-bottom: 2px;
        border-bottom: 1px solid #f8fafc;
    }
    .row-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e293b;
        padding-right: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.1;
    }
    .row-label.individual {
        font-weight: 600;
        color: #475569;
        font-size: 0.75rem;
    }

    .grid-bg {
        grid-column: 2 / span 960;
        display: grid;
        /* grid-template-columns is now set inline to match the 960-column parent perfectly */
        height: 100%;
        position: absolute;
        width: calc(100% - 200px);
        left: 200px;
        top: 0;
        pointer-events: none;
    }
    .grid-cell {
        border-left: 1px solid transparent;
        height: 100%;
    }
    .cell-hour {
        border-left: 1px solid #cbd5e1; /* Replaced e2e8f0 */
    }
    .cell-30m {
        border-left: 1px dashed #e2e8f0; /* Replaced f1f5f9 */
    }
    .cell-15m {
        border-left: 1px dotted #f1f5f9; /* Replaced f8fafc */
    }

    /* Blocks */
    .time-block {
        height: 26px; /* Reduced height for compactness */
        border-radius: 6px;
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 1px 1px rgba(0,0,0,0.2);
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        background-image: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .time-block:hover {
        transform: translateY(-2px) scale(1.01);
        filter: brightness(1.1);
        z-index: 15;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    @media print {
        @page { size: landscape; margin: 1cm; }
        #timeIndicatorWrapper { display: none !important; }
        
        /* Override global layout constraints from app.blade.php and common patterns */
        html, body, .main-content, .layout-wrapper, #content-area {
            height: auto !important;
            overflow: visible !important;
            width: auto !important;
            display: block !important;
            flex: none !important;
        }

        .container, .container-fluid {
            height: auto !important;
            overflow: visible !important;
            display: block !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: none !important;
            flex: none !important;
        }

        /* Reset containers for multipage printing */
        .timeline-outer-wrapper, 
        .timeline-inner-wrapper, 
        .timeline-container { 
            height: auto !important; 
            min-height: auto !important;
            overflow: visible !important; 
            padding: 0 !important; 
            display: block !important;
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            flex: none !important;
        }

        .timeline-body-scroll { 
            max-height: none !important; 
            overflow: visible !important; 
            height: auto !important;
            padding-right: 0 !important;
            display: block !important;
        }

        /* Border for blocks on paper */
        .time-block {
            border: 1px solid rgba(0,0,0,0.4) !important;
            box-shadow: none !important;
        }

        button, a, input, .legend-bar { display: none !important; }
        .timeline-header, .timeline-row { grid-template-columns: 150px repeat(960, 1fr); }
        .grid-bg { grid-column: 2 / span 960; width: calc(100% - 150px); left: 150px; }
    }
</style>

<script>
    function updateTimeIndicator() {
        const indicator = document.getElementById('currentTimeIndicator');
        if (!indicator) return;

        const now = new Date();
        const hours = now.getHours();
        const minutes = now.getMinutes();

        // Timeline window: 06:00 to 22:00
        if (hours < 6 || hours >= 22) {
            indicator.style.display = 'none';
            return;
        }

        indicator.style.display = 'block';
        const totalMinutesSinceStart = (hours - 6) * 60 + minutes;
        const totalTimelineMinutes = 16 * 60; // 06:00 to 22:00
        const percentage = (totalMinutesSinceStart / totalTimelineMinutes) * 100;

        indicator.style.left = percentage + '%';
    }

    // Update every minute for the red line
    setInterval(updateTimeIndicator, 60000);
    
    // Initial call for both
    updateTimeIndicator();
    initManualIndicator();

    function initManualIndicator() {
        const manual = document.getElementById('manualTimeIndicator');
        const handle = document.getElementById('manualHandle');
        const gridArea = document.getElementById('indicatorsGridArea');
        if (!manual || !gridArea) return;

        // Set initial position: 1 hour before current time indicator, snapped to 15m
        const now = new Date();
        const startH = now.getHours();
        const startM = now.getMinutes();
        
        // Target time: current - 60 minutes
        let targetTotalMinutes = (startH - 6) * 60 + startM - 60;
        // Snap to nearest 15 minutes
        targetTotalMinutes = Math.round(targetTotalMinutes / 15) * 15;
        // Boundary check
        targetTotalMinutes = Math.max(0, Math.min(960, targetTotalMinutes));
        
        const initialPerc = (targetTotalMinutes / 960) * 100;
        manual.style.left = initialPerc + '%';
        updateTooltip(initialPerc);

        // Drag functionality
        let isDragging = false;

        const onStart = (e) => {
            isDragging = true;
            manual.style.cursor = 'grabbing';
            handle.style.cursor = 'grabbing';
            document.getElementById('manualTooltip').style.display = 'block';
            updateTooltip(parseFloat(manual.style.left));
            e.preventDefault();
        };

        const onMove = (e) => {
            if (!isDragging) return;

            const rect = gridArea.getBoundingClientRect();
            const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            
            let offsetX = clientX - rect.left;
            let percentage = (offsetX / rect.width) * 100;
            
            // Snap to 1 minute increments (960 segments across 16 hours)
            const segmentSize = 100 / 960;
            percentage = Math.round(percentage / segmentSize) * segmentSize;
            
            // Boundary checks
            percentage = Math.max(0, Math.min(100, percentage));
            
            manual.style.left = percentage + '%';
            updateTooltip(percentage);
        };

        const onEnd = () => {
            isDragging = false;
            manual.style.cursor = 'grab';
            handle.style.cursor = 'grab';
            document.getElementById('manualTooltip').style.display = 'none';
        };

        function updateTooltip(percentage) {
            const tooltipContent = document.getElementById('tooltipContent');
            const tooltipTime = document.getElementById('tooltipTime');
            if (!tooltipContent || !tooltipTime) return;

            // Mapping for colors and display names (must match DatabaseSeeder.php)
            const areaConfig = {
                'general':          { label: 'General', color: '#64748b' },
                'valery camacho':   { label: 'Valery Camacho', color: '#6366f1' },
                'buffet':           { label: 'Buffet', color: '#f59e0b' },
                'domicilio':        { label: 'Domicilio', color: '#3b82f6' },
                'electrodomestico': { label: 'Electrodoméstico', color: '#8b5cf6' },
                'cosmetico':        { label: 'Cosmético', color: '#ec4899' },
                'marking':          { label: 'Marking', color: '#10b981' }
            };

            // Calculate Time
            const totalMinutesArr = Math.round((percentage / 100) * 960);
            const hours = Math.floor(totalMinutesArr / 60) + 6;
            const minutes = totalMinutesArr % 60;
            tooltipTime.innerText = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

            // Initialize counts for all areas at 0
            const counts = {};
            for (const key in areaConfig) {
                counts[key] = 0;
            }
            
            let total = 0;
            // currentColumn in 960-column grid
            const currentColumn = Math.round(percentage / (100 / 960)) + 2;
            const blocks = document.querySelectorAll('.time-block');

            blocks.forEach(block => {
                const style = block.getAttribute('style');
                const match = style.match(/grid-column:\s*(\d+)\s*\/\s*(\d+)/);
                if (match) {
                    const start = parseInt(match[1]);
                    const end = parseInt(match[2]);
                    if (currentColumn >= start && currentColumn < end) {
                        let area = block.getAttribute('data-area') || 'general';
                        area = area.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        
                        // Safety: if new area added but not in config, treat as general
                        const configKey = areaConfig[area] ? area : 'general';
                        counts[configKey]++;
                        total++;
                    }
                }
            });

            // Build UI breakdown
            let html = `
                <div style="display: flex; justify-content: space-between; font-weight: bold; color: #fff; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 6px;">
                    <span>Total Personal:</span>
                    <span>${total}</span>
                </div>
            `;
            
            // Loop through all configured areas to show them even if 0
            for (const key in areaConfig) {
                const config = areaConfig[key];
                const count = counts[key];
                const opacity = count > 0 ? '1' : '0.4';
                
                html += `
                    <div style="display: flex; align-items: center; justify-content: space-between; font-size: 11px; color: rgba(255,255,255,${opacity}); margin-bottom: 2px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: ${config.color}; border: 1px solid rgba(255,255,255,0.2);"></span>
                            <span>${config.label}:</span>
                        </div>
                        <span style="font-weight: ${count > 0 ? 'bold' : 'normal'}">${count}</span>
                    </div>
                `;
            }

            tooltipContent.innerHTML = html;
        }

        handle.addEventListener('mousedown', onStart);
        window.addEventListener('mousemove', onMove);
        window.addEventListener('mouseup', onEnd);
        
        // Discoverability: show on hover
        handle.addEventListener('mouseenter', () => {
            if (!isDragging) document.getElementById('manualTooltip').style.display = 'block';
        });
        handle.addEventListener('mouseleave', () => {
            if (!isDragging) document.getElementById('manualTooltip').style.display = 'none';
        });
        
        // Touch support
        handle.addEventListener('touchstart', onStart);
        window.addEventListener('touchmove', onMove);
        window.addEventListener('touchend', onEnd);
    }
</script>
@endsection
