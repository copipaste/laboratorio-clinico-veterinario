<div>
    {{-- Mensajes toast --}}
    <x-toast type="success" :message="session('mensaje')" />
    <x-toast type="error" :message="session('error')" />

    {{-- Header --}}
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">Gestión de Tipos de Análisis</flux:heading>
        <flux:subheading>Administra los tipos de análisis, parámetros e insumos requeridos</flux:subheading>
    </div>

    {{-- Barra de acciones --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="w-full sm:w-96">
            <flux:input 
                wire:model.live.debounce.300ms="buscar"
                icon="magnifying-glass"
                placeholder="Buscar tipos de análisis..."
                class="w-full"
            />
        </div>

        <flux:button 
            wire:click="crear"
            icon="plus"
            variant="primary"
        >
            Nuevo Tipo de Análisis
        </flux:button>
    </div>

    {{-- Tabla --}}
    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow dark:border-neutral-700 dark:bg-neutral-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-700 dark:text-neutral-300">
                            <button wire:click="ordenarPor('nombre')" class="flex items-center gap-1">
                                <span>Nombre</span>
                                @if($sortBy === 'nombre')
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="{{ $sortDirection === 'asc' ? 'M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z' : 'M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z' }}" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-neutral-700 dark:text-neutral-300">
                            Descripción
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-neutral-700 dark:text-neutral-300">
                            Parámetros
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-neutral-700 dark:text-neutral-300">
                            Insumos
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-neutral-700 dark:text-neutral-300">
                            Estado
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-neutral-700 dark:text-neutral-300">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                    @forelse ($tiposAnalisis as $tipo)
                        <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50" wire:key="tipo-{{ $tipo->id }}">
                            <td class="px-6 py-4 text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $tipo->nombre }}
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-700 dark:text-neutral-300">
                                {{ Str::limit($tipo->descripcion ?? '-', 50) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:button
                                    wire:click="gestionarParametros({{ $tipo->id }})"
                                    variant="ghost"
                                    size="sm"
                                    color="blue"
                                >
                                    <flux:badge color="blue" size="sm">{{ $tipo->parametros_count }}</flux:badge>
                                    Parámetros
                                </flux:button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:button
                                    wire:click="gestionarInsumos({{ $tipo->id }})"
                                    variant="ghost"
                                    size="sm"
                                    color="purple"
                                >
                                    <flux:badge color="purple" size="sm">{{ $tipo->insumos_count }}</flux:badge>
                                    Gestionar
                                </flux:button>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <button type="button" wire:click="confirmarCambiarEstado({{ $tipo->id }})" class="cursor-pointer">
                                    <div class="pointer-events-none inline-flex">
                                        <flux:switch 
                                            :checked="$tipo->estado"
                                            wire:key="switch-{{ $tipo->id }}-{{ $tipo->estado ? 'on' : 'off' }}"
                                        />
                                    </div>
                                </button>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button
                                        wire:click="ver({{ $tipo->id }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="eye"
                                        color="neutral"
                                        title="Ver detalles"
                                    />
                                    <flux:button
                                        wire:click="editar({{ $tipo->id }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="pencil"
                                        color="cyan"
                                        title="Editar"
                                    />
                                    <flux:button
                                        wire:click="confirmarEliminar({{ $tipo->id }})"
                                        variant="ghost"
                                        size="sm"
                                        icon="trash"
                                        color="red"
                                        title="Eliminar"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="mb-3 h-12 w-12 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <flux:heading size="lg" class="mb-1">No hay tipos de análisis</flux:heading>
                                    <flux:subheading>
                                        @if ($buscar)
                                            No se encontraron resultados con "{{ $buscar }}"
                                        @else
                                            Comienza creando tu primer tipo de análisis
                                        @endif
                                    </flux:subheading>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tiposAnalisis->hasPages())
            <div class="border-t border-neutral-200 bg-neutral-50 px-6 py-4 dark:border-neutral-700 dark:bg-neutral-900">
                {{ $tiposAnalisis->links() }}
            </div>
        @endif
    </div>

    {{-- Modal crear/editar tipo de análisis --}}
    <flux:modal wire:model="modalAbierto" class="w-full max-w-3xl">
        <form wire:submit.prevent="guardar">
            <flux:heading size="lg" class="mb-2">
                {{ $modoEdicion ? 'Editar Tipo de Análisis' : 'Nuevo Tipo de Análisis' }}
            </flux:heading>
            <flux:subheading class="mb-6">
                Define el tipo de análisis y sus campos de resultados
            </flux:subheading>

            {{-- Información Básica --}}
            <div class="mb-6">
                <flux:heading size="sm" class="mb-4">Información Básica</flux:heading>
                
                <div class="space-y-6">
                    <flux:input 
                        wire:model="nombre"
                        label="Nombre del Análisis"
                        placeholder="Ej: Hemograma Completo"
                        required
                        :error="$errors->first('nombre')"
                    />

                    <flux:textarea 
                        wire:model="descripcion"
                        label="Descripción"
                        placeholder="Describe el análisis..."
                        rows="3"
                        :error="$errors->first('descripcion')"
                    />
                </div>
            </div>

            {{-- Campos del Análisis --}}
            <div class="mb-6">
                <flux:heading size="sm" class="mb-4">Campos del Análisis</flux:heading>
                
                {{-- Formulario para agregar campo --}}
                <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-900 mb-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-3">
                        <flux:input 
                            wire:model="campo_nombre_temp"
                            label="Nombre del Campo"
                            placeholder="Ej: Glóbulos Rojos"
                            :error="$errors->first('campo_nombre_temp')"
                        />

                        <flux:select wire:model="campo_unidad_temp" label="Unidad" :error="$errors->first('campo_unidad_temp')">
                            <option value="">Selecciona una unidad</option>
                            <option value="mg/dL">mg/dL</option>
                            <option value="g/dL">g/dL</option>
                            <option value="mmol/L">mmol/L</option>
                            <option value="UI/L">UI/L</option>
                            <option value="%">%</option>
                            <option value="células/µL">células/µL</option>
                            <option value="texto">texto</option>
                        </flux:select>
                    </div>

                    <div class="flex items-center justify-end">
                        <flux:button 
                            type="button"
                            wire:click="agregarCampo"
                            icon="plus"
                            variant="primary"
                            size="sm"
                        >
                            Agregar Campo
                        </flux:button>
                    </div>
                </div>

                {{-- Lista de campos agregados --}}
                @if(count($campos_temporales) > 0)
                    <div class="space-y-3">
                        @foreach($campos_temporales as $index => $campo)
                            <div wire:key="campo-{{ $index }}" class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700 bg-white dark:bg-neutral-800">
                                <div class="space-y-3">
                                    {{-- Campos editables --}}
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <flux:input 
                                            wire:model="campos_temporales.{{ $index }}.nombre"
                                            label="Nombre del Campo"
                                            placeholder="Ej: Glóbulos Rojos"
                                        />
                                            <div>
                                                <flux:select wire:model="campos_temporales.{{ $index }}.unidad" label="Unidad">
                                                    <option value="">Selecciona una unidad</option>
                                                    <option value="mg/dL">mg/dL</option>
                                                    <option value="g/dL">g/dL</option>
                                                    <option value="mmol/L">mmol/L</option>
                                                    <option value="UI/L">UI/L</option>
                                                    <option value="%">%</option>
                                                    <option value="células/µL">células/µL</option>
                                                    <option value="texto">texto</option>
                                                </flux:select>
                                            </div>
                                        </div>


                                    {{-- Fila inferior con badges y botón eliminar --}}
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            @if(isset($campo['id']))
                                                <flux:badge color="zinc" size="sm">Ref: {{ isset($campo['orden']) ? $campo['orden'] : '-' }}</flux:badge>
                                            @endif
                                        </div>
                                        
                                        <flux:button
                                            type="button"
                                            wire:click="eliminarCampoTemporal({{ $index }})"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            color="red"
                                        />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-neutral-500 text-sm">
                        No hay campos agregados. Agrega el primer campo del análisis.
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <flux:button type="button" wire:click="cerrarModal" variant="ghost">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" color="cyan" icon="check">
                    {{ $modoEdicion ? 'Actualizar' : 'Crear Tipo de Análisis' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal gestionar parámetros --}}
    <flux:modal wire:model="modalParametros" class="w-full max-w-4xl">
        <flux:heading size="lg" class="mb-2">Gestión de Parámetros</flux:heading>
        <flux:subheading class="mb-6">Administra los parámetros clínicos del análisis</flux:subheading>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300">Unidad</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-neutral-700 dark:text-neutral-300">Orden</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($parametros as $parametro)
                        <tr wire:key="param-{{ $parametro->id }}">
                            <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">{{ $parametro->nombre }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-700 dark:text-neutral-300">{{ $parametro->unidad }}</td>
                            <td class="px-4 py-3 text-center text-sm text-neutral-700 dark:text-neutral-300">{{ $parametro->orden }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-neutral-500">
                                No hay parámetros asociados. Crea el primero.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <flux:button wire:click="cerrarModalParametros" variant="primary" color="cyan">
                Cerrar
            </flux:button>
        </div>
    </flux:modal>

    {{-- Modal formulario parámetro --}}
    <flux:modal wire:model="modalParametroForm" class="w-full max-w-lg">
        <form wire:submit.prevent="guardarParametro">
            <flux:heading size="lg" class="mb-2">
                {{ $modoEdicionParametro ? 'Editar Parámetro' : 'Nuevo Parámetro' }}
            </flux:heading>
            <flux:subheading class="mb-6">Define el parámetro clínico a medir</flux:subheading>

            <div class="space-y-6">
                <flux:input 
                    wire:model="parametro_nombre"
                    label="Nombre del parámetro"
                    placeholder="Ej: Glucosa, Urea, Creatinina"
                    required
                    :error="$errors->first('parametro_nombre')"
                />

                <flux:input 
                    wire:model="parametro_unidad"
                    label="Unidad de medida"
                    placeholder="Ej: mg/dL, mmol/L, g/dL"
                    required
                    :error="$errors->first('parametro_unidad')"
                />

                <flux:input 
                    wire:model="parametro_orden"
                    label="Orden de visualización"
                    type="number"
                    placeholder="Ej: 1, 2, 3..."
                    :error="$errors->first('parametro_orden')"
                />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <flux:button type="button" wire:click="cerrarModalParametroForm" variant="ghost">
                    Cancelar
                </flux:button>
                <flux:button type="submit" variant="primary" color="cyan" icon="check">
                    {{ $modoEdicionParametro ? 'Actualizar' : 'Guardar' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    {{-- Modal gestionar insumos --}}
    <flux:modal wire:model="modalInsumos" class="w-full max-w-4xl">
        <flux:heading size="lg" class="mb-2">Gestión de Insumos Requeridos</flux:heading>
        <flux:subheading class="mb-6">Define los insumos que consume este análisis</flux:subheading>

        {{-- Formulario asociar insumo --}}
        <div class="mb-6 rounded-lg bg-neutral-50 p-4 dark:bg-neutral-900">
            <flux:heading size="sm" class="mb-3">Asociar Nuevo Insumo</flux:heading>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                <flux:select wire:model="insumo_id" label="Insumo" placeholder="Selecciona un insumo">
                    @foreach($insumos as $insumo)
                        <option value="{{ $insumo->id }}">{{ $insumo->nombre }} ({{ $insumo->unidad }})</option>
                    @endforeach
                </flux:select>

                <flux:input 
                    wire:model="cantidad_requerida"
                    label="Cantidad estándar"
                    type="number"
                    step="0.01"
                    placeholder="Ej: 5, 10, 0.5"
                />

                <flux:select wire:model="unidad_insumo" label="Unidad">
                    <option value="">Selecciona una unidad</option>
                    <option value="mg/dL">mg/dL</option>
                    <option value="g/dL">g/dL</option>
                    <option value="mmol/L">mmol/L</option>
                    <option value="UI/L">UI/L</option>
                    <option value="%">%</option>
                    <option value="células/µL">células/µL</option>
                </flux:select>

                <div class="flex items-end">
                    <flux:button wire:click="asociarInsumo" icon="plus" variant="primary" class="w-full">
                        Asociar
                    </flux:button>
                </div>
            </div>
        </div>

        {{-- Tabla de insumos asociados --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 dark:divide-neutral-700">
                <thead class="bg-neutral-50 dark:bg-neutral-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300">Insumo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-neutral-700 dark:text-neutral-300">Unidad</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-neutral-700 dark:text-neutral-300">Cantidad Requerida</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-neutral-700 dark:text-neutral-300">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($insumosAsociados as $insumo)
                        <tr wire:key="insumo-{{ $insumo['id'] }}">
                            <td class="px-4 py-3 text-sm text-neutral-900 dark:text-neutral-100">{{ $insumo['nombre'] }}</td>
                            <td class="px-4 py-3 text-sm text-neutral-700 dark:text-neutral-300">{{ $insumo['unidad'] }}</td>
                            <td class="px-4 py-3 text-right text-sm text-neutral-900 dark:text-neutral-100 font-medium">
                                {{ $insumo['pivot']['cantidad_requerida'] }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <flux:button
                                    wire:click="desasociarInsumo({{ $insumo['id'] }})"
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    color="red"
                                    title="Desasociar"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-neutral-500">
                                No hay insumos asociados. Agrega el primero.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <flux:button wire:click="cerrarModalInsumos" variant="primary" color="cyan">
                Cerrar
            </flux:button>
        </div>
    </flux:modal>

    {{-- Modal ver detalles --}}
    <flux:modal wire:model="modalVer" class="w-full max-w-3xl">
        @if($tipoAnalisisAVer)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg" class="mb-1">Detalles del Tipo de Análisis</flux:heading>
                    <flux:subheading>Información completa</flux:subheading>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Nombre</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->nombre }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Estado</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->estado ? 'Activo' : 'Inactivo' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Descripción</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->descripcion ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Parámetros</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->parametros_count }} parámetros</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Insumos</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->insumos_count }} insumos</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Análisis realizados</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->analisis_count }} análisis</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-1">Fecha de creación</label>
                        <p class="text-base text-neutral-900 dark:text-neutral-100">{{ $tipoAnalisisAVer->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="cerrarModalVer" variant="primary" color="cyan">Cerrar</flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- Modal eliminar --}}
    <flux:modal wire:model="modalEliminar" class="w-full max-w-md">
        <div class="space-y-6">
            <div class="flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <flux:heading size="lg" class="mb-2">Eliminar Tipo de Análisis</flux:heading>
                <flux:subheading>¿Estás seguro? Esta acción no se puede deshacer. Si tiene análisis históricos, se recomienda desactivarlo.</flux:subheading>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="cancelarEliminar" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="eliminar" variant="danger" icon="trash">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal eliminar parámetro --}}
    <flux:modal wire:model="modalEliminarParametro" class="w-full max-w-md">
        <div class="space-y-6">
            <div class="flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <flux:heading size="lg" class="mb-2">Eliminar Parámetro</flux:heading>
                <flux:subheading>¿Estás seguro? No se puede eliminar si tiene resultados históricos.</flux:subheading>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="modalEliminarParametro = false" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="eliminarParametro" variant="danger" icon="trash">Eliminar</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal cambiar estado --}}
    <flux:modal wire:model="modalCambiarEstado" class="w-full max-w-md">
        <div class="space-y-6">
            <div class="flex justify-center">
                @if($tipoAnalisisACambiar && $estadoActual === true)
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-100 dark:bg-orange-900/20">
                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </div>
                @else
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/20">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        </svg>
                    </div>
                @endif
            </div>
            <div class="text-center">
                <flux:heading size="lg" class="mb-2">{{ $estadoActual ? 'Desactivar' : 'Activar' }} Tipo de Análisis</flux:heading>
                <flux:subheading>¿Estás seguro de cambiar el estado? {{ $estadoActual ? 'Al desactivarlo, no estará disponible para nuevos análisis.' : 'Al activarlo, estará disponible nuevamente.' }}</flux:subheading>
            </div>
            <div class="flex justify-end gap-3">
                <flux:button wire:click="modalCambiarEstado = false" variant="ghost">Cancelar</flux:button>
                <flux:button wire:click="cambiarEstado" variant="{{ $estadoActual ? 'danger' : 'primary' }}" color="{{ $estadoActual ? 'red' : 'cyan' }}">
                    {{ $estadoActual ? 'Desactivar' : 'Activar' }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>


