<?php

namespace App\Http\Controllers\Web\Clinic;

use App\Http\Controllers\Controller;
use App\Models\core\PrescriptionTemplate;
use App\Models\core\PrescriptionTemplateItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index(): JsonResponse
    {
        $doctorId  = auth()->user()->doctor->id;
        $templates = PrescriptionTemplate::where('doctor_id', $doctorId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($templates);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'items'             => 'required|array|min:1',
            'items.*.name'      => 'required|string|max:255',
            'items.*.dosage'    => 'nullable|string|max:100',
            'items.*.frequency' => 'nullable|string|max:100',
            'items.*.route'     => 'nullable|string|max:100',
            'items.*.duration'  => 'nullable|string|max:100',
            'items.*.instructions' => 'nullable|string',
        ]);

        $doctorId = auth()->user()->doctor->id;

        $template = PrescriptionTemplate::create([
            'doctor_id' => $doctorId,
            'name'      => $request->name,
        ]);

        foreach ($request->items as $idx => $item) {
            if (empty($item['name'])) {
                continue;
            }

            PrescriptionTemplateItem::create([
                'template_id'  => $template->id,
                'medication_id'=> isset($item['medication_id']) && $item['medication_id'] ? (int) $item['medication_id'] : null,
                'name'         => $item['name'],
                'dosage'       => $item['dosage'] ?? null,
                'frequency'    => $item['frequency'] ?? null,
                'route'        => $item['route'] ?? null,
                'duration'     => $item['duration'] ?? null,
                'instructions' => $item['instructions'] ?? null,
                'sort_order'   => $idx,
            ]);
        }

        return response()->json(['id' => $template->id, 'name' => $template->name], 201);
    }

    public function load(PrescriptionTemplate $template): JsonResponse
    {
        $doctorId = auth()->user()->doctor->id;
        abort_if($template->doctor_id !== $doctorId, 403);

        return response()->json([
            'id'    => $template->id,
            'name'  => $template->name,
            'items' => $template->items->map(fn($item) => [
                'medication_id' => $item->medication_id,
                'name'          => $item->name,
                'dosage'        => $item->dosage,
                'frequency'     => $item->frequency,
                'route'         => $item->route,
                'duration'      => $item->duration,
                'instructions'  => $item->instructions,
            ]),
        ]);
    }
}
