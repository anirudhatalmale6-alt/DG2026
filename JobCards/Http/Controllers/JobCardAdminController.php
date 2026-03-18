<?php

namespace Modules\JobCards\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobCardAdminController extends Controller
{
    // ─── JOB TYPES ───

    public function types()
    {
        $types = DB::table('cims_job_card_types')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        return view('jobcards::admin.types', compact('types'));
    }

    public function storeType(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'submission_to' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer',
        ]);

        $id = DB::table('cims_job_card_types')->insertGetId([
            'name'          => $request->input('name'),
            'description'   => $request->input('description'),
            'submission_to' => $request->input('submission_to'),
            'display_order' => $request->input('display_order', 0),
            'is_active'     => 1,
            'created_by'    => Auth::id(),
            'updated_by'    => Auth::id(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'id' => $id]);
        }
        return redirect()->route('jobcards.admin.types')->with('success', 'Job type created.');
    }

    public function updateType(Request $request, int $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'submission_to' => 'nullable|string|max:100',
            'display_order' => 'nullable|integer',
            'is_active'     => 'nullable|boolean',
        ]);

        DB::table('cims_job_card_types')->where('id', $id)->update([
            'name'          => $request->input('name'),
            'description'   => $request->input('description'),
            'submission_to' => $request->input('submission_to'),
            'display_order' => $request->input('display_order', 0),
            'is_active'     => $request->input('is_active', 1),
            'updated_by'    => Auth::id(),
            'updated_at'    => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('jobcards.admin.types')->with('success', 'Job type updated.');
    }

    public function deleteType(int $id)
    {
        // Check if any job cards use this type
        $count = DB::table('cims_job_cards')->where('job_type_id', $id)->whereNull('deleted_at')->count();
        if ($count > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete: {$count} job card(s) use this type. Deactivate it instead.",
            ], 422);
        }

        DB::table('cims_job_card_type_steps')->where('job_type_id', $id)->delete();
        DB::table('cims_job_card_type_fields')->where('job_type_id', $id)->delete();
        DB::table('cims_job_card_type_documents')->where('job_type_id', $id)->delete();
        DB::table('cims_job_card_types')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    // ─── STEPS ───

    public function steps(int $typeId)
    {
        $type = DB::table('cims_job_card_types')->where('id', $typeId)->first();
        if (!$type) abort(404);

        $steps = DB::table('cims_job_card_type_steps')
            ->where('job_type_id', $typeId)
            ->orderBy('display_order')
            ->get();

        return view('jobcards::admin.steps', compact('type', 'steps'));
    }

    public function storeStep(Request $request, int $typeId)
    {
        $request->validate([
            'step_name'        => 'required|string|max:255',
            'step_description' => 'nullable|string',
            'step_type'        => 'nullable|in:checkbox,document_required,info_review',
            'is_required'      => 'nullable|boolean',
            'display_order'    => 'nullable|integer',
        ]);

        $maxOrder = DB::table('cims_job_card_type_steps')
            ->where('job_type_id', $typeId)
            ->max('display_order') ?? 0;

        $id = DB::table('cims_job_card_type_steps')->insertGetId([
            'job_type_id'      => $typeId,
            'step_name'        => $request->input('step_name'),
            'step_description' => $request->input('step_description'),
            'step_type'        => $request->input('step_type', 'checkbox'),
            'is_required'      => $request->input('is_required', 1),
            'display_order'    => $request->input('display_order', $maxOrder + 1),
            'is_active'        => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'id' => $id]);
        }
        return redirect()->route('jobcards.admin.steps', $typeId)->with('success', 'Step added.');
    }

    public function updateStep(Request $request, int $id)
    {
        $request->validate([
            'step_name'        => 'required|string|max:255',
            'step_description' => 'nullable|string',
            'step_type'        => 'nullable|in:checkbox,document_required,info_review',
            'is_required'      => 'nullable|boolean',
            'display_order'    => 'nullable|integer',
            'is_active'        => 'nullable|boolean',
        ]);

        DB::table('cims_job_card_type_steps')->where('id', $id)->update([
            'step_name'        => $request->input('step_name'),
            'step_description' => $request->input('step_description'),
            'step_type'        => $request->input('step_type', 'checkbox'),
            'is_required'      => $request->input('is_required', 1),
            'display_order'    => $request->input('display_order', 0),
            'is_active'        => $request->input('is_active', 1),
            'updated_at'       => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Step updated.');
    }

    public function deleteStep(int $id)
    {
        DB::table('cims_job_card_type_steps')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    public function reorderSteps(Request $request, int $typeId)
    {
        $order = $request->input('order', []);
        foreach ($order as $index => $stepId) {
            DB::table('cims_job_card_type_steps')
                ->where('id', $stepId)
                ->where('job_type_id', $typeId)
                ->update(['display_order' => $index + 1, 'updated_at' => now()]);
        }
        return response()->json(['success' => true]);
    }

    // ─── FIELDS ───

    public function fields(int $typeId)
    {
        $type = DB::table('cims_job_card_types')->where('id', $typeId)->first();
        if (!$type) abort(404);

        $configuredFields = DB::table('cims_job_card_type_fields')
            ->where('job_type_id', $typeId)
            ->orderBy('display_order')
            ->get();

        $availableFields = config('job_cards.available_client_fields', []);

        return view('jobcards::admin.fields', compact('type', 'configuredFields', 'availableFields'));
    }

    public function saveFields(Request $request, int $typeId)
    {
        $fields = $request->input('fields', []);

        // Delete existing and re-insert
        DB::table('cims_job_card_type_fields')->where('job_type_id', $typeId)->delete();

        foreach ($fields as $index => $field) {
            if (empty($field['field_name'])) continue;

            DB::table('cims_job_card_type_fields')->insert([
                'job_type_id'   => $typeId,
                'field_name'    => $field['field_name'],
                'field_label'   => $field['field_label'] ?? config("job_cards.available_client_fields.{$field['field_name']}", $field['field_name']),
                'display_order' => $field['display_order'] ?? ($index + 1),
                'is_required'   => $field['is_required'] ?? 0,
                'is_active'     => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('jobcards.admin.fields', $typeId)->with('success', 'Fields saved.');
    }

    // ─── DOCUMENT REQUIREMENTS ───

    public function documents(int $typeId)
    {
        $type = DB::table('cims_job_card_types')->where('id', $typeId)->first();
        if (!$type) abort(404);

        $documents = DB::table('cims_job_card_type_documents')
            ->where('job_type_id', $typeId)
            ->orderBy('display_order')
            ->get();

        return view('jobcards::admin.documents', compact('type', 'documents'));
    }

    public function storeDocument(Request $request, int $typeId)
    {
        $request->validate([
            'document_label' => 'required|string|max:255',
            'is_required'    => 'nullable|boolean',
            'display_order'  => 'nullable|integer',
        ]);

        $maxOrder = DB::table('cims_job_card_type_documents')
            ->where('job_type_id', $typeId)
            ->max('display_order') ?? 0;

        $id = DB::table('cims_job_card_type_documents')->insertGetId([
            'job_type_id'     => $typeId,
            'document_label'  => $request->input('document_label'),
            'is_required'     => $request->input('is_required', 1),
            'display_order'   => $request->input('display_order', $maxOrder + 1),
            'is_active'       => 1,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'id' => $id]);
        }
        return redirect()->route('jobcards.admin.documents', $typeId)->with('success', 'Document requirement added.');
    }

    public function updateDocument(Request $request, int $id)
    {
        DB::table('cims_job_card_type_documents')->where('id', $id)->update([
            'document_label' => $request->input('document_label'),
            'is_required'    => $request->input('is_required', 1),
            'display_order'  => $request->input('display_order', 0),
            'is_active'      => $request->input('is_active', 1),
            'updated_at'     => now(),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Document requirement updated.');
    }

    public function deleteDocument(int $id)
    {
        DB::table('cims_job_card_type_documents')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
