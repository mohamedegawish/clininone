<?php

namespace App\Http\Controllers\Api\v1\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescriptionTemplateResource;
use App\Models\core\PrescriptionTemplate;
use App\Models\core\PrescriptionTemplateItem;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionTemplateController extends Controller
{
    use HttpResponses;

    /**
     * GET /clinic/prescription-templates
     * List this doctor's templates.
     */
    public function index(Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;
        if (! $doctor) {
            return $this->error('Doctor profile not found.', 403);
        }

        $templates = PrescriptionTemplate::where('doctor_id', $doctor->id)
            ->with('items')
            ->orderBy('name')
            ->get();

        return $this->success(PrescriptionTemplateResource::collection($templates));
    }

    /**
     * POST /clinic/prescription-templates
     * Create a new template with items.
     */
    public function store(Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;
        if (! $doctor) {
            return $this->error('Doctor profile not found.', 403);
        }

        $request->validate([
            'name'                     => 'required|string|max:255',
            'items'                    => 'required|array|min:1',
            'items.*.name'             => 'required|string|max:255',
            'items.*.medication_id'    => 'nullable|integer|exists:medications,id',
            'items.*.dosage'           => 'nullable|string|max:100',
            'items.*.frequency'        => 'nullable|string|max:100',
            'items.*.route'            => 'nullable|string|max:100',
            'items.*.duration'         => 'nullable|string|max:100',
            'items.*.instructions'     => 'nullable|string',
        ]);

        $template = \DB::transaction(function () use ($request, $doctor) {
            $template = PrescriptionTemplate::create([
                'doctor_id' => $doctor->id,
                'name'      => $request->name,
            ]);

            foreach ($request->items as $idx => $item) {
                if (empty($item['name'])) {
                    continue;
                }
                PrescriptionTemplateItem::create([
                    'template_id'   => $template->id,
                    'medication_id' => isset($item['medication_id']) ? (int) $item['medication_id'] : null,
                    'name'          => $item['name'],
                    'dosage'        => $item['dosage'] ?? null,
                    'frequency'     => $item['frequency'] ?? null,
                    'route'         => $item['route'] ?? null,
                    'duration'      => $item['duration'] ?? null,
                    'instructions'  => $item['instructions'] ?? null,
                    'sort_order'    => $idx,
                ]);
            }

            return $template->load('items');
        });

        return $this->success(new PrescriptionTemplateResource($template), 'Template created.', 201);
    }

    /**
     * GET /clinic/prescription-templates/{id}
     * Load a single template with its items.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;
        if (! $doctor) {
            return $this->error('Doctor profile not found.', 403);
        }

        $template = PrescriptionTemplate::where('doctor_id', $doctor->id)
            ->with('items')
            ->findOrFail($id);

        return $this->success(new PrescriptionTemplateResource($template));
    }

    /**
     * DELETE /clinic/prescription-templates/{id}
     */
    public function destroy(int $id, Request $request): JsonResponse
    {
        $doctor = $request->user()?->doctor;
        if (! $doctor) {
            return $this->error('Doctor profile not found.', 403);
        }

        $template = PrescriptionTemplate::where('doctor_id', $doctor->id)->findOrFail($id);
        $template->delete();

        return $this->success(null, 'Template deleted.');
    }
}
