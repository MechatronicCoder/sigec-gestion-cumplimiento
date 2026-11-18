<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Checklist de Ciberseguridad — {{ $evaluacion->nombre ?? 'Sin evaluación' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-800 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
            @endif

            {{-- Barra de control --}}
            <div class="bg-white shadow rounded-lg p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <label class="text-sm text-gray-600">Evaluación:</label>
                        <select onchange="window.location.href = '{{ route('checklist.index') }}?evaluacion_id=' + this.value"
                                class="border-gray-300 rounded-md text-sm">
                            @foreach ($evaluaciones as $ev)
                                <option value="{{ $ev->id }}" @selected($evaluacion && $ev->id === $evaluacion->id)>
                                    {{ $ev->nombre }} ({{ ucfirst($ev->estado) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <form action="{{ route('checklist.evaluacion.nueva') }}" method="POST"
                            onsubmit="return confirm('¿Crear una nueva evaluación en blanco?');">
                            @csrf
                            <button type="submit"
                                    class="text-sm px-3 py-1.5 rounded-md font-medium {{ !$existeAbierta ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}"
                                    @disabled($existeAbierta)>
                                + Nueva evaluación
                            </button>
                        </form>

                        <button type="submit" form="form-checklist"
                                class="text-sm px-3 py-1.5 rounded-md font-medium {{ ($evaluacion && $esEditable) ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}"
                                @disabled(!$evaluacion || !$esEditable)>
                            Guardar evaluación
                        </button>

                        @if ($evaluacion && $esEditable)
                            <form action="{{ route('checklist.evaluacion.finalizar', $evaluacion) }}" method="POST"
                                onsubmit="return confirm('¿Finalizar esta evaluación? Ya no podrás modificar sus calificaciones.');">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-sm px-3 py-1.5 rounded-md font-medium bg-red-600 text-white hover:bg-red-700">
                                    Finalizar evaluación
                                </button>
                            </form>
                        @else
                            <button type="button" class="text-sm px-3 py-1.5 rounded-md font-medium bg-gray-200 text-gray-400 cursor-not-allowed" disabled>
                                Finalizar evaluación
                            </button>
                        @endif
                    </div>
                </div>

                @if ($evaluacion)
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <span class="font-medium px-2 py-1 rounded-full {{ $evaluacion->estado === 'abierta' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($evaluacion->estado) }}
                        </span>
                        <span class="text-gray-400">
                            Última modificación: {{ $evaluacion->updated_at->locale('es')->isoFormat('DD/MM/YYYY hh:mm A') }}
                        </span>
                        @if (!$esEditable)
                            <span class="text-gray-400 italic">Solo lectura</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

            @if (!$evaluacion)
                <div class="bg-white shadow rounded-lg p-8 text-center text-gray-500">
                    No hay ninguna evaluación creada todavía. Usa el botón "+ Nueva evaluación" para comenzar.
                </div>
            @else
                <form method="POST" action="{{ route('checklist.guardar') }}">
                    @csrf
                    <input type="hidden" name="evaluacion_id" value="{{ $evaluacion->id }}">

                    <div class="space-y-6">
                        @foreach ($dimensiones as $dimension)
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <div class="bg-gray-800 text-white px-6 py-3">
                                    <h3 class="text-lg font-semibold">{{ $dimension->nombre }}</h3>
                                    <p class="text-sm text-gray-300">Peso: {{ $dimension->peso }}%</p>
                                </div>

                                @foreach ($dimension->indicadores as $indicador)
                                    <div class="border-t border-gray-200">
                                        <div class="bg-gray-50 px-6 py-2">
                                            <h4 class="font-medium text-gray-700">{{ $indicador->nombre }}</h4>
                                        </div>
                                        <table class="w-full table-fixed divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 45%">Ítem</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 15%">Calificación</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="width: 40%">Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach ($indicador->items as $item)
                                                    @php $calif = $calificaciones->get($item->id); @endphp
                                                    <tr>
                                                        <td class="px-4 py-2 align-top">
                                                            <p class="text-sm text-gray-800">{{ $item->nombre }}</p>
                                                            @if ($item->descripcion)
                                                                <p class="text-xs text-gray-400 mt-1">{{ $item->descripcion }}</p>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 align-top">
                                                            <select name="calificaciones[{{ $item->id }}]"
                                                                    class="border-gray-300 rounded text-sm w-full disabled:bg-gray-100 disabled:text-gray-400"
                                                                    @disabled(!$esEditable)>
                                                                <option value="">--</option>
                                                                @for ($n = 0; $n <= 5; $n++)
                                                                    <option value="{{ $n }}" @selected($calif && (int)$calif->calificacion === $n)>{{ $n }}</option>
                                                                @endfor
                                                            </select>
                                                        </td>
                                                        <td class="px-4 py-2 align-top">
                                                            <textarea name="observaciones[{{ $item->id }}]" rows="1"
                                                                      class="border-gray-300 rounded text-sm w-full disabled:bg-gray-100 disabled:text-gray-400"
                                                                      @disabled(!$esEditable)>{{ $calif->observaciones ?? '' }}</textarea>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </form>
            @endif

        </div>
    </div>
</x-app-layout>
