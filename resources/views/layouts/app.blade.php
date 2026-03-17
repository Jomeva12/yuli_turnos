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
            max-width: 1400px; /* Wide container for the table */
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Excel-like Table Styles */
        .table-container {
            background: var(--bg-card);
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: auto;
            max-height: 75vh;
            border: 1px solid var(--border-color);
        }

        .excel-table {
            border-collapse: separate;
            border-spacing: 0;
            width: max-content;
            min-width: 100%;
            font-size: 0.875rem;
        }

        .excel-table th,
        .excel-table td {
            border-right: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1rem;
            white-space: nowrap;
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
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="/" class="navbar-brand">GestionTurnos v1.0</a>
    </nav>

    <main class="container">
        @yield('content')
    </main>

</body>
</html>
