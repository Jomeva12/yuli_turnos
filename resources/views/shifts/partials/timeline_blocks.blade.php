@php
    $schedule = $shift->schedule;
    // Support both | and / as separators
    $schedule = str_replace('|', '/', $schedule);
    $segments = explode('/', $schedule);
    
    // Normalize area name for color matching (remove accents, to lowercase)
    $rawAreaName = $shift->area ? $shift->area->name : 'general';
    $normalizedAreaName = strtolower(strtr(utf8_decode($rawAreaName), 
        utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 
        'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'));
    
    // Define colors matching actual Areas
    $colors = [
        'cosmetico'        => '#ec4899', // pink-500
        'electrodomestico' => '#8b5cf6', // violet-500
        'buffet'           => '#f59e0b', // amber-500
        'marking'          => '#10b981', // emerald-500
        'valery camacho'   => '#6366f1', // indigo-500
        'domicilio'        => '#3b82f6', // blue-500
        'general'          => '#64748b'  // slate-500
    ];
    $color = $colors[$normalizedAreaName] ?? '#64748b';
@endphp

@foreach($segments as $segment)
    @php
        $times = explode('-', trim($segment));
        if (count($times) !== 2) continue;
        
        try {
            // Carbon handles "7:00" fine, but let's be safe
            $startTimeStr = trim($times[0]);
            $endTimeStr = trim($times[1]);
            
            // Add leading zero if missing for consistency (e.g. 7:00 -> 07:00)
            if (strlen(explode(':', $startTimeStr)[0]) === 1) $startTimeStr = '0' . $startTimeStr;
            if (strlen(explode(':', $endTimeStr)[0]) === 1) $endTimeStr = '0' . $endTimeStr;

            $start = \Carbon\Carbon::createFromFormat('H:i', $startTimeStr);
            $end = \Carbon\Carbon::createFromFormat('H:i', $endTimeStr);
            
            $startOffset = ($start->hour - 6) * 60 + $start->minute;
            $endOffset = ($end->hour - 6) * 60 + $end->minute;
            
            $colStart = (int)max(2, $startOffset + 2);
            $colEnd = (int)min(962, $endOffset + 2);
            
            if ($colStart >= $colEnd) continue;
        } catch (\Exception $e) {
            continue;
        }
    @endphp

    <div class="time-block" 
         style="grid-column: {{ $colStart }} / {{ $colEnd }}; background: {{ $color }};"
         title="{{ $shift->employee->name }} | {{ $segment }} | {{ $rawAreaName }}"
         data-area="{{ $rawAreaName }}">
        @if(!$isIndividual)
            <span style="font-size: 0.6rem; opacity: 0.9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 0 4px;">
                {{ $shift->employee->name }}
            </span>
        @endif
    </div>
@endforeach
