@extends('layouts.app')

@section('content')
<div class="container-fluid" style="padding: 1.5rem; background: #f8fafc; min-height: 100vh;">
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
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #10b981;"></span> Automotores
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #3b82f6;"></span> Domicilio
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; font-weight: 600;">
            <span style="width: 12px; height: 12px; border-radius: 3px; background: #64748b;"></span> General
        </div>
    </div>

    <!-- Timeline Wrapper -->
    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="overflow-x: auto;">
            <div class="timeline-container" style="min-width: 1200px; padding: 1.5rem;">
                
                <!-- Time Header -->
                <div class="timeline-header">
                    <div class="header-label-col">ÁREAS / PERSONAL</div>
                    @for($h = 6; $h <= 21; $h++)
                        <div class="time-marker-group" style="grid-column: span 4;">
                            <div class="time-label">{{ sprintf('%02d:00', $h) }}</div>
                        </div>
                    @endfor
                </div>

                <!-- Areas Fijas -->
                <div class="timeline-section-title">Áreas Fijas</div>
                @foreach($areas as $area)
                    @php 
                        $areaShifts = $shiftsByArea[$area->id] ?? [];
                        $cleanAreaName = strtolower($area->name);
                        if($cleanAreaName === 'general') continue;
                    @endphp
                    <div class="timeline-row">
                        <div class="row-label">{{ $area->name }}</div>
                        <div class="grid-bg">
                            @for($i = 0; $i < 64; $i++)
                                <div class="grid-cell {{ $i % 4 == 0 ? 'cell-hour' : '' }}"></div>
                            @endfor
                        </div>
                        @foreach($areaShifts as $shift)
                            @include('shifts.partials.timeline_blocks', ['shift' => $shift, 'isIndividual' => false])
                        @endforeach
                    </div>
                @endforeach

                <!-- Area General -->
                <div class="timeline-section-title" style="margin-top: 2rem;">Área General (Detalle por Empleado)</div>
                @if(count($generalShifts) > 0)
                    @foreach($generalShifts as $shift)
                        <div class="timeline-row">
                            <div class="row-label individual">{{ $shift->employee->name }}</div>
                            <div class="grid-bg">
                                @for($i = 0; $i < 64; $i++)
                                    <div class="grid-cell {{ $i % 4 == 0 ? 'cell-hour' : '' }}"></div>
                                @endfor
                            </div>
                            @include('shifts.partials.timeline_blocks', ['shift' => $shift, 'isIndividual' => true])
                        </div>
                    @endforeach
                @else
                    <div style="padding: 2rem; text-align: center; color: #94a3b8; font-style: italic;">
                        No hay personal asignado a General para este día.
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

<style>
    .timeline-header {
        display: grid;
        grid-template-columns: 200px repeat(64, 1fr);
        border-bottom: 2px solid #f1f5f9;
        margin-bottom: 1rem;
        position: sticky;
        top: 0;
        background: white;
        z-index: 20;
    }
    .header-label-col {
        font-size: 0.75rem;
        font-weight: 800;
        color: #94a3b8;
        display: flex;
        align-items: center;
        padding-bottom: 0.5rem;
    }
    .time-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #475569;
        text-align: left;
        padding-left: 2px;
    }
    .time-marker-group {
        border-left: 2px solid #e2e8f0;
        height: 25px;
    }

    .timeline-section-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 1.5rem 0 0.75rem 0;
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
        grid-template-columns: 200px repeat(64, 1fr);
        height: 44px;
        align-items: center;
        position: relative;
        margin-bottom: 4px;
    }
    .row-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        padding-right: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .row-label.individual {
        font-weight: 500;
        color: #64748b;
        font-size: 0.8rem;
    }

    .grid-bg {
        grid-column: 2 / span 64;
        display: grid;
        grid-template-columns: repeat(64, 1fr);
        height: 100%;
        position: absolute;
        width: calc(100% - 200px);
        left: 200px;
        top: 0;
        pointer-events: none;
    }
    .grid-cell {
        border-left: 1px solid #f8fafc;
        height: 100%;
    }
    .cell-hour {
        border-left: 1px solid #f1f5f9;
    }

    /* Blocks */
    .time-block {
        height: 28px;
        border-radius: 6px;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 1px 1px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.1s, filter 0.1s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .time-block:hover {
        transform: scale(1.02);
        filter: brightness(1.1);
        z-index: 15;
    }
    
    @media print {
        .container-fluid { background: white !important; padding: 0 !important; }
        button, a, input { display: none !important; }
        .timeline-container { min-width: auto !important; }
        .timeline-row { grid-template-columns: 150px repeat(64, 1fr); }
        .grid-bg { width: calc(100% - 150px); left: 150px; }
    }
</style>
@endsection
