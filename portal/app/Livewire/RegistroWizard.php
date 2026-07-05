<?php

namespace App\Livewire;

use App\Models\Catalogo;
use App\Services\CurpValidator;
use App\Services\RegistroAlumnoService;
use App\Support\RegistroAlumnoRules;
use Illuminate\Support\Arr;
use Livewire\Component;

class RegistroWizard extends Component
{
    public int $step = 1;

    public array $form = [
        'semestre_solicitado' => 1,
    ];

    public function mount(?string $curp = null): void
    {
        $this->form = array_replace($this->form, session('registro_borrador', []));

        if ($curp && empty($this->form['curp'])) {
            $this->form['curp'] = $curp;
        }
    }

    public function next(): void
    {
        $this->validate($this->stepRules());
        $this->saveDraft();
        $this->step = min(6, $this->step + 1);
    }

    public function previous(): void
    {
        $this->saveDraft();
        $this->step = max(1, $this->step - 1);
    }

    public function submit(CurpValidator $curpValidator, RegistroAlumnoService $service)
    {
        $data = $this->validate($this->prefixedRules())['form'];
        $data['curp'] = mb_strtoupper($data['curp']);

        if (! $curpValidator->esValida($data['curp'])) {
            $this->addError('form.curp', 'Revisa tu CURP: debe tener 18 caracteres y coincidir con el formato oficial.');

            return null;
        }

        if ($data['folio_examen'] !== $data['folio_examen_confirmacion']) {
            $this->addError('form.folio_examen_confirmacion', 'Los folios no coinciden. Escríbelo igual que aparece en tu hoja de respuestas.');

            return null;
        }

        $proceso = $service->registrar($data);
        session([
            'alumno_proceso_id' => $proceso->id,
            'alumno_ciclo_id' => $proceso->ciclo_ingreso_id,
            'alumno_nivel_sensible' => true,
        ]);
        session()->forget(['registro_curp', 'registro_borrador']);

        return redirect()->route('alumno.registro.exito')
            ->with('mensaje', 'Registro completado. Tu folio interno es '.$proceso->folio_registro.'.');
    }

    public function render()
    {
        return view('livewire.registro-wizard', [
            'catalogos' => $this->catalogos(),
            'requiredFields' => $this->requiredFields(),
        ]);
    }

    private function saveDraft(): void
    {
        session(['registro_borrador' => $this->form]);
    }

    private function stepRules(): array
    {
        $fields = match ($this->step) {
            1 => ['curp', 'folio_examen', 'folio_examen_confirmacion', 'semestre_solicitado'],
            2 => ['nombres', 'primer_apellido', 'segundo_apellido', 'estado_civil_id', 'fecha_nacimiento', 'sexo_id', 'nacionalidad_id', 'entidad_nacimiento_id', 'municipio_nacimiento_id', 'tipo_estudiante_id', 'paraescolar_id'],
            3 => ['municipio_id', 'localidad_id', 'codigo_postal', 'domicilio', 'colonia', 'telefono', 'celular', 'correo'],
            4 => ['entidad_secundaria_id', 'municipio_secundaria_id', 'secundaria_nombre', 'tipo_secundaria_id', 'turno_secundaria_id', 'promedio_secundaria'],
            5 => ['tutor_nombres', 'tutor_primer_apellido', 'tutor_segundo_apellido', 'tutor_telefono', 'tutor_celular', 'tutor_ocupacion_id', 'tutor_estudios_id', 'madre_nombres', 'madre_primer_apellido', 'madre_segundo_apellido', 'madre_telefono', 'madre_celular', 'madre_ocupacion_id', 'madre_estudios_id'],
            default => ['no_seguro_medico', 'beca_id', 'estatura', 'peso', 'tipo_sangre_id', 'acepto_privacidad'],
        };

        return Arr::only($this->prefixedRules(), array_map(fn (string $field) => 'form.'.$field, $fields));
    }

    private function prefixedRules(): array
    {
        return collect(RegistroAlumnoRules::rules())
            ->mapWithKeys(fn (array $rules, string $field) => ['form.'.$field => $rules])
            ->all();
    }

    private function requiredFields(): array
    {
        return collect(RegistroAlumnoRules::rules())
            ->filter(fn (array $rules) => collect($rules)->contains(
                fn (mixed $rule) => is_string($rule) && in_array($rule, ['required', 'accepted'], true)
            ))
            ->keys()
            ->all();
    }

    private function catalogos(): array
    {
        $tipos = [
            'sexo', 'nacionalidad', 'estado_civil', 'entidad', 'municipio', 'localidad',
            'tipo_estudiante', 'paraescolar', 'tipo_secundaria', 'turno', 'ocupacion',
            'nivel_estudios', 'beca', 'tipo_sangre',
        ];

        return collect($tipos)->mapWithKeys(fn (string $tipo) => [
            $tipo => Catalogo::deTipo($tipo)->get(),
        ])->all();
    }
}
