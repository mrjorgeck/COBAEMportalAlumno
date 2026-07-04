@extends('layouts.admin')

@section('titulo', 'Dashboard academico')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Dashboard academico</h1>

    <form method="GET" class="mt-4 flex flex-wrap gap-3 rounded bg-white p-4 shadow-sm">
        <select name="ciclo" class="rounded border-gray-300">
            @foreach ($ciclos as $item)
                <option value="{{ $item->id }}" @selected($ciclo?->id === $item->id)>{{ $item->generacion }}</option>
            @endforeach
        </select>
        <select name="examen" class="rounded border-gray-300">
            @foreach ($examenes as $item)
                <option value="{{ $item->id }}" @selected($examen?->id === $item->id)>{{ $item->nombre }}</option>
            @endforeach
        </select>
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white">Filtrar</button>
    </form>

    <section class="mt-4 grid gap-3 md:grid-cols-4">
        @foreach ([
            'Registrados' => $datos['registrados'],
            'Evaluados' => $datos['evaluados'],
            'Promedio general' => $datos['promedio_general'].'%',
            'Sin resultado' => $datos['sin_resultado'],
            'Riesgo alto' => $datos['riesgo_alto'],
            'Riesgo critico' => $datos['riesgo_critico'],
            'Sin grupo' => $datos['sin_grupo'],
        ] as $etiqueta => $valor)
            <div class="rounded bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-600">{{ $etiqueta }}</p>
                <p class="text-2xl font-bold text-cobaem-900">{{ $valor }}</p>
            </div>
        @endforeach
    </section>

    <section class="mt-4 grid gap-4 lg:grid-cols-3">
        <div class="rounded bg-white p-4 shadow-sm"><canvas id="areas"></canvas></div>
        <div class="rounded bg-white p-4 shadow-sm"><canvas id="riesgos"></canvas></div>
        <div class="rounded bg-white p-4 shadow-sm"><canvas id="grupos"></canvas></div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const chartData = {
            areas: @json($datos['promedio_areas']),
            riesgos: @json($datos['riesgos']),
            grupos: @json($datos['por_grupo']),
        };
        const barras = (id, data, label) => new Chart(document.getElementById(id), {
            type: 'bar',
            data: { labels: Object.keys(data), datasets: [{ label, data: Object.values(data), backgroundColor: '#0f766e' }] },
            options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
        });
        barras('areas', chartData.areas, 'Promedio por area');
        new Chart(document.getElementById('riesgos'), {
            type: 'doughnut',
            data: { labels: Object.keys(chartData.riesgos), datasets: [{ data: Object.values(chartData.riesgos), backgroundColor: ['#16a34a', '#eab308', '#f97316', '#dc2626'] }] },
            options: { responsive: true }
        });
        barras('grupos', chartData.grupos, 'Promedio por grupo propedeutico');
    </script>
@endsection
