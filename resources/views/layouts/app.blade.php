<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Turnos</title>
    <!-- Modern Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-hover: #4338CA;
            --bg-body: #F9FAFB;
            --bg-card: #FFFFFF;
            --text-main: #111827;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;
            --sticky-bg: #F3F4F6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        *, *::before, *::after {
            box-sizing: inherit;
        }

        .navbar {
            background-color: var(--bg-card);
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-main);
            text-decoration: none;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 1rem auto;
            padding: 0 1rem;
        }

        .layout-wrapper {
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .table-section {
            flex: 1;
            min-width: 0; /* Allow shrinking */
        }

        .summary-panel {
            width: 300px;
            background: var(--bg-card);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }

        .summary-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
            color: var(--primary);
        }

        .stat-group {
            margin-bottom: 1.5rem;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            display: block;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0;
            border-bottom: 1px dashed var(--border-color);
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-value {
            font-weight: 600;
            background: var(--bg-body);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
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
            background-color: #FEF3C7 !important; /* Light amber */
            border-left: 2px solid #F59E0B !important;
            border-right: 2px solid #F59E0B !important;
        }

        .current-day-header {
            background-color: #F59E0B !important;
            color: white !important;
        }

        .selected-day-header {
            background-color: var(--primary) !important;
            color: white !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            z-index: 20;
        }

        .selected-day-column-highlight {
            outline: 2px solid var(--primary);
            outline-offset: -2px;
            background-color: rgba(59, 130, 246, 0.08);
            z-index: 10;
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

        .excel-table th {
            background-color: var(--sticky-bg);
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* First column sticky */
        .excel-table th:first-child,
        .excel-table td:first-child {
            position: sticky;
            left: 0;
            background-color: var(--bg-card);
            border-right: 2px solid var(--border-color);
            z-index: 5;
            font-weight: 500;
        }
        
        .excel-table tr:nth-child(even) td:first-child {
            background-color: #FAFAFA;
        }

        /* Corner header overlaps both sticky axes */
        .excel-table th:first-child {
            z-index: 15;
            background-color: var(--sticky-bg);
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
        <a href="/" class="navbar-brand">GestionTurnos v1.0</a>
    </nav>

    <main class="container">
        @yield('content')
    </main>

</body>
</html>
