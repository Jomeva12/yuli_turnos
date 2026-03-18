<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos</title>
    <!-- Elegant Font: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1b998b; /* Matte Green / Teal */
            --primary-hover: #158376; /* Darker Matte Green */
            --primary-light: #f0fdf4; /* Soft Mint / Green 50 */
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #001d2d; /* Very Dark Navy for text */
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --nav-bg: #003049; /* Deep Navy */
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100vw;
            overflow: hidden; /* Prevent global scroll */
            display: flex;
            flex-direction: column;
        }

        *, *::before, *::after {
            box-sizing: inherit;
        }

        .navbar {
            background: var(--nav-bg);
            padding: 0.75rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand::before {
            content: '';
            width: 8px;
            height: 24px;
            background: var(--primary);
            border-radius: 4px;
        }

        .container {
            flex: 1;
            width: 100%;
            max-width: 100%;
            padding: 1.5rem;
            min-height: 0; /* Important for children overflow */
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .layout-wrapper {
            display: flex;
            gap: 1.5rem;
            align-items: stretch;
            flex: 1;
            min-height: 0;
        }
        
        #content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .table-section {
            flex: 1;
            min-width: 0; /* Allow shrinking */
        }

        /* Excel-like Table Styles */
        .table-container {
            background: var(--bg-card);
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: auto;
            max-height: calc(100vh - 160px);
            border: 1px solid var(--border-color);
            position: relative;
            cursor: grab;
            user-select: none; /* Prevent text selection while dragging */
        }

        .table-container:active {
            cursor: grabbing;
        }

        .current-day-column {
            background-color: #f1f5f9 !important; /* Extremely subtle tint */
            border-left: 2px solid var(--primary) !important;
            border-right: 2px solid var(--primary) !important;
        }

        .current-day-header {
            border-top: 4px solid var(--primary) !important;
            color: var(--primary) !important;
            font-weight: 700 !important;
            background-color: #fff !important;
        }

        /* Fix for disappearing text in selected header */
        .excel-table th.selected-day-header {
            background-color: #003049 !important; /* Deep Navy */
            color: white !important;
            border-top-color: #1b998b !important; /* Matte Green accent */
            box-shadow: 0 4px 12px rgba(0, 48, 73, 0.2);
            z-index: 50;
        }

        .selected-day-column-highlight {
            background-color: rgba(71, 85, 105, 0.04) !important; /* Subtle tint */
        }

        .excel-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: 0.875rem;
        }

        .editable-cell {
            color: var(--primary);
            font-weight: 500;
            position: relative;
            cursor: default !important;
        }

        .cell-options-trigger {
            position: absolute;
            top: 2px;
            right: 4px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 4px;
            opacity: 0.4;
            transition: all 0.2s;
            line-height: 1;
        }

        .editable-cell:hover .cell-options-trigger {
            opacity: 1;
            background: rgba(0,0,0,0.05);
            color: var(--primary);
        }

        .cell-options-trigger:hover {
            background: var(--primary) !important;
            color: white !important;
        }

        .excel-table th,
        .excel-table td {
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }

        .excel-table td {
            padding: 8px 4px;
            text-align: center;
            font-size: 0.85rem;
            height: 45px;
            transition: all 0.2s;
        }

        /* Excel-table header sticky */
        .excel-table th {
            background-color: white !important;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 30; /* Above regular cells */
            border-bottom: 2px solid var(--border-color) !important;
        }

        /* First column sticky cells */
        .excel-table td:first-child {
            position: sticky;
            left: 0;
            background-color: white; /* Base background */
            border-right: 2px solid var(--border-color);
            z-index: 20; /* Below headers, above other cells */
            font-weight: 500;
        }

        /* Corner header: overlaps both sticky axes */
        .excel-table th:first-child {
            z-index: 60; /* Highest in the table */
            background-color: white !important;
            left: 0;
        }

        /* Employee Cell Hover Menu */
        .employee-cell {
            /* Removed relative to preserve sticky positioning */
        }

        .skills-popover {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            top: 50%;
            left: 80%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            padding: 0.85rem 1.1rem;
            border-radius: 14px;
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            z-index: 200;
            min-width: 190px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            text-align: left;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .employee-cell:hover .skills-popover {
            visibility: visible;
            opacity: 1;
            left: 105%;
        }

        .popover-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.6rem;
            font-weight: 700;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.4rem;
        }

        .popover-skill {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            padding: 5px 0;
            color: var(--text-main);
            font-weight: 500;
        }

        .popover-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            flex-shrink: 0;
        }

        .excel-table tbody tr:hover td {
            background-color: #F0FdfA !important; /* Subtle highlight */
            transition: background-color 0.15s ease;
            cursor: pointer;
        }

        /* Striping */
        .excel-table tbody tr:nth-child(even) {
            background-color: #FAFAFA;
        }

        .editable-cell {
            color: var(--primary);
            font-weight: 500;
        }

        /* Color Coding System */
        .shift-partido {
            background-color: #FFE4E6 !important; /* Rose 100 */
            color: #9F1239 !important; /* Rose 800 */
        }
        
        .area-general {
            background-color: #D1FAE5 !important; /* Green 100 */
            color: #065F46 !important; /* Green 800 */
        }
        
        .area-electro {
            background-color: #DBEAFE !important; /* Blue 100 */
            color: #1E3A8A !important; /* Blue 800 */
        }
        
        .shift-morning {
            background-color: #FEF3C7 !important; /* Amber 100 */
            color: #92400E !important; /* Amber 800 */
        }
        
        .shift-afternoon {
            background-color: #F3E8FF !important; /* Purple 100 */
            color: #6B21A8 !important; /* Purple 800 */
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            backdrop-filter: blur(4px);
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--border-color);
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            margin-top: 1rem;
            font-weight: 600;
            color: var(--primary);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div id="loading-overlay">
        <div class="spinner"></div>
        <div class="loading-text">Generando turnos, por favor espera...</div>
    </div>

    <nav class="navbar">
        <a href="{{ route('shifts.index') }}" class="navbar-brand">GestionTurnos v1.0</a>
        <div style="display: flex; gap: 0.5rem; background: rgba(255,255,255,0.1); padding: 0.4rem; border-radius: 10px;">
            <a href="{{ route('shifts.index') }}" 
               style="text-decoration: none; font-size: 0.85rem; font-weight: 600; color: {{ request()->routeIs('shifts.index') ? 'white' : 'rgba(255,255,255,0.6)' }}; background: {{ request()->routeIs('shifts.index') ? 'var(--primary)' : 'transparent' }}; padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.2s;">
                Calendario
            </a>
            <a href="{{ route('employees.index') }}" 
               style="text-decoration: none; font-size: 0.85rem; font-weight: 600; color: {{ request()->routeIs('employees.index') ? 'white' : 'rgba(255,255,255,0.6)' }}; background: {{ request()->routeIs('employees.index') ? 'var(--primary)' : 'transparent' }}; padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.2s;">
                Habilidades
            </a>
        </div>
    </nav>

    <main class="container">
        @yield('content')
    </main>

</body>
</html>
