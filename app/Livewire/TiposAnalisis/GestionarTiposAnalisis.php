<?php

namespace App\Livewire\TiposAnalisis;

use App\Models\TipoAnalisis;
use App\Models\ParametroAnalisis;
use App\Models\Insumo;
use Livewire\Component;
use Livewire\WithPagination;

class GestionarTiposAnalisis extends Component
{
    use WithPagination;

    // Propiedades del formulario de análisis
    public $tipo_analisis_id;
    public $nombre;
    public $descripcion;
    public $estado = true;

    // Propiedades del formulario de parámetros
    public $parametro_id;
    public $parametro_nombre;
    public $parametro_unidad;
    public $parametro_orden;
    public $tipo_analisis_seleccionado_id;

    // Propiedades para campos dinámicos en el formulario
    public $campo_nombre_temp;
    public $campo_unidad_temp;
    public $campos_temporales = [];
    public $parametros_a_eliminar = [];

    // Propiedades para insumos
    public $insumo_id;
    public $cantidad_requerida;
    public $insumosAsociados = [];

    // Propiedades de control
    public $modalAbierto = false;
    public $modalEliminar = false;
    public $modalCambiarEstado = false;
    public $modalVer = false;
    public $modalParametros = false;
    public $modalParametroForm = false;
    public $modalInsumos = false;
    public $modalEliminarParametro = false;
    
    public $tipoAnalisisAEliminar = null;
    public $tipoAnalisisACambiar = null;
    public $tipoAnalisisAVer = null;
    public $estadoActual = null;
    public $parametroAEliminar = null;
    
    public $buscar = '';
    public $modoEdicion = false;
    public $modoEdicionParametro = false;

    // Propiedades de ordenamiento
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Reglas de validación para análisis
    protected function rulesAnalisis()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'boolean',
        ];
    }

    // Reglas de validación para parámetros
    protected function rulesParametro()
    {
        return [
            'parametro_nombre' => 'required|string|max:255',
            'parametro_unidad' => 'required|string|max:50',
            'parametro_orden' => 'nullable|integer|min:1',
        ];
    }

    // Mensajes de validación personalizados
    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'parametro_nombre.required' => 'El nombre del parámetro es obligatorio.',
        'parametro_unidad.required' => 'La unidad es obligatoria.',
    ];

    /**
     * Abrir modal para crear nuevo tipo de análisis
     */
    public function crear()
    {
        $this->resetearFormulario();
        $this->campos_temporales = [];
        $this->parametros_a_eliminar = [];
        $this->modoEdicion = false;
        $this->modalAbierto = true;
    }

    /**
     * Abrir modal para ver detalles
     */
    public function ver($id)
    {
        $this->tipoAnalisisAVer = TipoAnalisis::with(['parametros', 'analisis', 'insumos'])
            ->withCount(['parametros', 'analisis', 'insumos'])
            ->findOrFail($id);
        $this->modalVer = true;
    }

    /**
     * Cerrar modal de ver
     */
    public function cerrarModalVer()
    {
        $this->modalVer = false;
        $this->tipoAnalisisAVer = null;
    }

    /**
     * Abrir modal para editar
     */
    public function editar($id)
    {
        $tipoAnalisis = TipoAnalisis::with('parametros')->findOrFail($id);
        
        $this->tipo_analisis_id = $tipoAnalisis->id;
        $this->nombre = $tipoAnalisis->nombre;
        $this->descripcion = $tipoAnalisis->descripcion;
        $this->estado = $tipoAnalisis->estado;
        
        // Cargar parámetros existentes
        $this->campos_temporales = $tipoAnalisis->parametros->sortBy('orden')->map(function($param) {
            return [
                'id' => $param->id,
                'nombre' => $param->nombre,
                'unidad' => $param->unidad,
                'orden' => $param->orden,
            ];
        })->values()->toArray();
        
        $this->parametros_a_eliminar = [];
        $this->modoEdicion = true;
        $this->modalAbierto = true;
    }

    /**
     * Guardar tipo de análisis
     */
    public function guardar()
    {
        $this->validate($this->rulesAnalisis());

        try {
            if ($this->modoEdicion) {
                $tipoAnalisis = TipoAnalisis::findOrFail($this->tipo_analisis_id);
                $tipoAnalisis->update([
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                    'estado' => $this->estado,
                ]);

                // Eliminar parámetros marcados
                if (!empty($this->parametros_a_eliminar)) {
                    ParametroAnalisis::whereIn('id', $this->parametros_a_eliminar)->delete();
                }

                // Actualizar y crear parámetros
                foreach ($this->campos_temporales as $index => $campo) {
                    $unidad = $campo['unidad'] ?? 'texto';
                    
                    if (isset($campo['id'])) {
                        // Actualizar existente
                        ParametroAnalisis::where('id', $campo['id'])->update([
                            'nombre' => $campo['nombre'],
                            'unidad' => $unidad,
                            'orden' => $index + 1,
                        ]);
                    } else {
                        // Crear nuevo
                        ParametroAnalisis::create([
                            'tipo_analisis_id' => $tipoAnalisis->id,
                            'nombre' => $campo['nombre'],
                            'unidad' => $unidad,
                            'orden' => $index + 1,
                        ]);
                    }
                }

                session()->flash('mensaje', 'Tipo de análisis actualizado exitosamente.');
            } else {
                $tipoAnalisis = TipoAnalisis::create([
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                    'estado' => $this->estado,
                ]);

                // Crear parámetros
                foreach ($this->campos_temporales as $index => $campo) {
                    $unidad = $campo['unidad'] ?? 'texto';
                    
                    ParametroAnalisis::create([
                        'tipo_analisis_id' => $tipoAnalisis->id,
                        'nombre' => $campo['nombre'],
                        'unidad' => $unidad,
                        'orden' => $index + 1,
                    ]);
                }

                session()->flash('mensaje', 'Tipo de análisis creado exitosamente.');
            }

            $this->cerrarModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el tipo de análisis: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar eliminación
     */
    public function confirmarEliminar($id)
    {
        $this->tipoAnalisisAEliminar = $id;
        $this->modalEliminar = true;
    }

    /**
     * Cancelar eliminación
     */
    public function cancelarEliminar()
    {
        $this->modalEliminar = false;
        $this->tipoAnalisisAEliminar = null;
    }

    /**
     * Eliminar tipo de análisis
     */
    public function eliminar()
    {
        try {
            if (!$this->tipoAnalisisAEliminar) {
                return;
            }

            $tipoAnalisis = TipoAnalisis::findOrFail($this->tipoAnalisisAEliminar);
            
            // Verificar si tiene análisis asociados
            if ($tipoAnalisis->analisis()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el tipo de análisis porque tiene análisis asociados. Se recomienda desactivarlo.');
                $this->modalEliminar = false;
                $this->tipoAnalisisAEliminar = null;
                return;
            }

            $tipoAnalisis->delete();
            session()->flash('mensaje', 'Tipo de análisis eliminado exitosamente.');
            
            $this->modalEliminar = false;
            $this->tipoAnalisisAEliminar = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar: ' . $e->getMessage());
            $this->modalEliminar = false;
            $this->tipoAnalisisAEliminar = null;
        }
    }

    /**
     * Confirmar cambiar estado
     */
    public function confirmarCambiarEstado($id)
    {
        $tipoAnalisis = TipoAnalisis::findOrFail($id);
        $this->tipoAnalisisACambiar = $id;
        $this->estadoActual = $tipoAnalisis->estado;
        $this->modalCambiarEstado = true;
    }

    /**
     * Cambiar estado
     */
    public function cambiarEstado()
    {
        try {
            if (!$this->tipoAnalisisACambiar) {
                return;
            }

            $tipoAnalisis = TipoAnalisis::findOrFail($this->tipoAnalisisACambiar);
            $tipoAnalisis->update(['estado' => !$tipoAnalisis->estado]);
            
            $mensaje = $tipoAnalisis->estado ? 'Tipo de análisis activado exitosamente.' : 'Tipo de análisis desactivado exitosamente.';
            session()->flash('mensaje', $mensaje);

            $this->modalCambiarEstado = false;
            $this->tipoAnalisisACambiar = null;
            $this->estadoActual = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado: ' . $e->getMessage());
            $this->modalCambiarEstado = false;
        }
    }

    /**
     * Gestionar parámetros de un tipo de análisis
     */
    public function gestionarParametros($id)
    {
        $this->tipo_analisis_seleccionado_id = $id;
        $this->modalParametros = true;
    }

    /**
     * Abrir formulario para crear parámetro
     */
    public function crearParametro()
    {
        $this->resetearFormularioParametro();
        $this->modoEdicionParametro = false;
        $this->modalParametroForm = true;
    }

    /**
     * Editar parámetro
     */
    public function editarParametro($id)
    {
        $parametro = ParametroAnalisis::findOrFail($id);
        
        $this->parametro_id = $parametro->id;
        $this->parametro_nombre = $parametro->nombre;
        $this->parametro_unidad = $parametro->unidad;
        $this->parametro_orden = $parametro->orden;
        
        $this->modoEdicionParametro = true;
        $this->modalParametroForm = true;
    }

    /**
     * Guardar parámetro
     */
    public function guardarParametro()
    {
        $this->validate($this->rulesParametro());

        try {
            if ($this->modoEdicionParametro) {
                $parametro = ParametroAnalisis::findOrFail($this->parametro_id);
                $parametro->update([
                    'nombre' => $this->parametro_nombre,
                    'unidad' => $this->parametro_unidad,
                    'orden' => $this->parametro_orden ?? 999,
                ]);

                session()->flash('mensaje', 'Parámetro actualizado exitosamente.');
            } else {
                ParametroAnalisis::create([
                    'tipo_analisis_id' => $this->tipo_analisis_seleccionado_id,
                    'nombre' => $this->parametro_nombre,
                    'unidad' => $this->parametro_unidad,
                    'orden' => $this->parametro_orden ?? 999,
                ]);

                session()->flash('mensaje', 'Parámetro creado exitosamente.');
            }

            $this->cerrarModalParametroForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al guardar el parámetro: ' . $e->getMessage());
        }
    }

    /**
     * Confirmar eliminar parámetro
     */
    public function confirmarEliminarParametro($id)
    {
        $this->parametroAEliminar = $id;
        $this->modalEliminarParametro = true;
    }

    /**
     * Eliminar parámetro
     */
    public function eliminarParametro()
    {
        try {
            if (!$this->parametroAEliminar) {
                return;
            }

            $parametro = ParametroAnalisis::findOrFail($this->parametroAEliminar);
            
            // Verificar si tiene resultados asociados
            if ($parametro->resultados()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el parámetro porque tiene resultados históricos asociados.');
                $this->modalEliminarParametro = false;
                $this->parametroAEliminar = null;
                return;
            }

            $parametro->delete();
            session()->flash('mensaje', 'Parámetro eliminado exitosamente.');
            
            $this->modalEliminarParametro = false;
            $this->parametroAEliminar = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el parámetro: ' . $e->getMessage());
            $this->modalEliminarParametro = false;
        }
    }

    /**
     * Gestionar insumos de un tipo de análisis
     */
    public function gestionarInsumos($id)
    {
        $this->tipo_analisis_seleccionado_id = $id;
        $this->cargarInsumosAsociados();
        $this->modalInsumos = true;
    }

    /**
     * Cargar insumos asociados
     */
    public function cargarInsumosAsociados()
    {
        $tipoAnalisis = TipoAnalisis::with('insumos')->find($this->tipo_analisis_seleccionado_id);
        $this->insumosAsociados = $tipoAnalisis ? $tipoAnalisis->insumos->toArray() : [];
    }

    /**
     * Asociar insumo al tipo de análisis
     */
    public function asociarInsumo()
    {
        $this->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad_requerida' => 'required|numeric|min:0.01',
        ]);

        try {
            $tipoAnalisis = TipoAnalisis::findOrFail($this->tipo_analisis_seleccionado_id);
            
            // Verificar si ya está asociado
            if ($tipoAnalisis->insumos()->where('insumo_id', $this->insumo_id)->exists()) {
                session()->flash('error', 'Este insumo ya está asociado a este análisis.');
                return;
            }

            $tipoAnalisis->insumos()->attach($this->insumo_id, [
                'cantidad_requerida' => $this->cantidad_requerida
            ]);

            session()->flash('mensaje', 'Insumo asociado exitosamente.');
            
            $this->insumo_id = null;
            $this->cantidad_requerida = null;
            $this->cargarInsumosAsociados();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al asociar insumo: ' . $e->getMessage());
        }
    }

    /**
     * Desasociar insumo
     */
    public function desasociarInsumo($insumoId)
    {
        try {
            $tipoAnalisis = TipoAnalisis::findOrFail($this->tipo_analisis_seleccionado_id);
            $tipoAnalisis->insumos()->detach($insumoId);

            session()->flash('mensaje', 'Insumo desasociado exitosamente.');
            $this->cargarInsumosAsociados();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desasociar insumo: ' . $e->getMessage());
        }
    }

    /**
     * Cerrar modal
     */
    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->resetearFormulario();
        $this->resetValidation();
    }

    /**
     * Cerrar modal de parámetros
     */
    public function cerrarModalParametros()
    {
        $this->modalParametros = false;
        $this->tipo_analisis_seleccionado_id = null;
    }

    /**
     * Cerrar modal de formulario de parámetro
     */
    public function cerrarModalParametroForm()
    {
        $this->modalParametroForm = false;
        $this->resetearFormularioParametro();
        $this->resetValidation();
    }

    /**
     * Cerrar modal de insumos
     */
    public function cerrarModalInsumos()
    {
        $this->modalInsumos = false;
        $this->tipo_analisis_seleccionado_id = null;
        $this->insumosAsociados = [];
        $this->insumo_id = null;
        $this->cantidad_requerida = null;
    }

    /**
     * Resetear formulario principal
     */
    private function resetearFormulario()
    {
        $this->tipo_analisis_id = null;
        $this->nombre = '';
        $this->descripcion = '';
        $this->estado = true;
        $this->campo_nombre_temp = '';
        $this->campo_unidad_temp = '';
        $this->campos_temporales = [];
        $this->parametros_a_eliminar = [];
    }

    /**
     * Agregar campo temporal
     */
    public function agregarCampo()
    {
        $this->validate([
            'campo_nombre_temp' => 'required|string|max:255',
            'campo_unidad_temp' => 'required|string|max:50',
        ], [
            'campo_nombre_temp.required' => 'El nombre del campo es obligatorio.',
            'campo_unidad_temp.required' => 'La unidad es obligatoria.',
        ]);

        $this->campos_temporales[] = [
            'nombre' => $this->campo_nombre_temp,
            'unidad' => $this->campo_unidad_temp,
        ];

        // Resetear campos temporales
        $this->campo_nombre_temp = '';
        $this->campo_unidad_temp = '';
        $this->resetValidation(['campo_nombre_temp', 'campo_unidad_temp']);
    }

    /**
     * Eliminar campo temporal
     */
    public function eliminarCampoTemporal($index)
    {
        if (isset($this->campos_temporales[$index])) {
            // Si tiene ID, agregar a la lista de eliminación
            if (isset($this->campos_temporales[$index]['id'])) {
                $this->parametros_a_eliminar[] = $this->campos_temporales[$index]['id'];
            }
            unset($this->campos_temporales[$index]);
            $this->campos_temporales = array_values($this->campos_temporales);
        }
    }

    /**
     * Resetear formulario de parámetro
     */
    private function resetearFormularioParametro()
    {
        $this->parametro_id = null;
        $this->parametro_nombre = '';
        $this->parametro_unidad = '';
        $this->parametro_orden = null;
    }

    /**
     * Resetear búsqueda
     */
    public function updatingBuscar()
    {
        $this->resetPage();
    }

    /**
     * Cambiar ordenamiento
     */
    public function ordenarPor($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        $tiposAnalisis = TipoAnalisis::query()
            ->withCount(['parametros', 'analisis', 'insumos'])
            ->when($this->buscar, function ($query) {
                $query->where('nombre', 'like', '%' . $this->buscar . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->buscar . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        $parametros = $this->tipo_analisis_seleccionado_id 
            ? ParametroAnalisis::where('tipo_analisis_id', $this->tipo_analisis_seleccionado_id)
                ->orderBy('orden')
                ->get()
            : collect();

        $insumos = Insumo::where('estado', true)->orderBy('nombre')->get();

        return view('livewire.tipos-analisis.gestionar-tipos-analisis', [
            'tiposAnalisis' => $tiposAnalisis,
            'parametros' => $parametros,
            'insumos' => $insumos,
        ]);
    }
}
