<?php

namespace Modules\JobCards\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JobCardService
{
    /**
     * Generate next job code: JC-2026-0001
     */
    public function generateJobCode(): string
    {
        $prefix = config('job_cards.code_prefix', 'JC');
        $year = Carbon::now()->format('Y');

        $lastCode = DB::table('cims_job_cards')
            ->where('job_code', 'LIKE', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->value('job_code');

        if ($lastCode) {
            $lastNum = (int) substr($lastCode, strrpos($lastCode, '-') + 1);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $year, $nextNum);
    }

    /**
     * Get all active job types for dropdowns.
     */
    public function getJobTypes()
    {
        return DB::table('cims_job_card_types')
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get full job type config (steps, fields, required docs).
     */
    public function getJobTypeConfig(int $typeId): array
    {
        $type = DB::table('cims_job_card_types')->where('id', $typeId)->first();

        $steps = DB::table('cims_job_card_type_steps')
            ->where('job_type_id', $typeId)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        $fields = DB::table('cims_job_card_type_fields')
            ->where('job_type_id', $typeId)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        $documents = DB::table('cims_job_card_type_documents')
            ->where('job_type_id', $typeId)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        return [
            'type'      => $type,
            'steps'     => $steps,
            'fields'    => $fields,
            'documents' => $documents,
        ];
    }

    /**
     * Search clients for autocomplete/dropdown.
     */
    public function searchClients(string $query = '', int $limit = 20)
    {
        $q = DB::table('client_master')
            ->select([
                'client_id',
                'client_code',
                'company_name',
                'trading_name',
                'email',
                'phone_mobile',
            ])
            ->where('is_active', 1)
            ->whereNull('deleted_at');

        if ($query) {
            $q->where(function ($w) use ($query) {
                $w->where('company_name', 'LIKE', "%{$query}%")
                  ->orWhere('client_code', 'LIKE', "%{$query}%")
                  ->orWhere('trading_name', 'LIKE', "%{$query}%");
            });
        }

        return $q->orderBy('company_name')->limit($limit)->get();
    }

    /**
     * Get client info — only the fields configured for the job type.
     */
    public function getClientInfo(int $clientId, ?int $jobTypeId = null): array
    {
        $client = DB::table('client_master')
            ->where('client_id', $clientId)
            ->first();

        if (!$client) {
            return ['client' => null, 'fields' => [], 'existingDocs' => []];
        }

        // Build fields data based on job type config
        $fieldValues = [];
        if ($jobTypeId) {
            $configuredFields = DB::table('cims_job_card_type_fields')
                ->where('job_type_id', $jobTypeId)
                ->where('is_active', 1)
                ->orderBy('display_order')
                ->get();

            foreach ($configuredFields as $f) {
                $fieldValues[] = [
                    'field_name'  => $f->field_name,
                    'field_label' => $f->field_label,
                    'value'       => $client->{$f->field_name} ?? '',
                    'is_required' => (bool) $f->is_required,
                ];
            }
        }

        // Get existing documents on file for this client
        $existingDocs = DB::table('cims_documents')
            ->where('client_id', $clientId)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->where('is_trashed', 0)->orWhereNull('is_trashed');
            })
            ->select(['id', 'title', 'document_code', 'file_original_name', 'file_path', 'category_id', 'type_id', 'status', 'created_at'])
            ->orderByDesc('created_at')
            ->get();

        // Also get client_master_documents
        $clientDocs = DB::table('client_master_documents')
            ->where('client_id', $clientId)
            ->select(['id', 'document_type', 'original_filename', 'file_path', 'uploaded_at'])
            ->orderByDesc('uploaded_at')
            ->get();

        return [
            'client'       => $client,
            'fields'       => $fieldValues,
            'existingDocs' => $existingDocs,
            'clientDocs'   => $clientDocs,
        ];
    }

    /**
     * Create a new job card with progress rows.
     */
    public function createJobCard(array $data): int
    {
        $jobCode = $this->generateJobCode();

        $jobCardId = DB::table('cims_job_cards')->insertGetId([
            'job_code'    => $jobCode,
            'client_id'   => $data['client_id'],
            'job_type_id' => $data['job_type_id'],
            'assigned_to' => $data['assigned_to'] ?? Auth::id(),
            'followed_by' => $data['followed_by'] ?? null,
            'status'      => $data['status'] ?? 'draft',
            'priority'    => $data['priority'] ?? 'normal',
            'due_date'    => $data['due_date'] ?? null,
            'notes'       => $data['notes'] ?? null,
            'completion_percentage' => 0,
            'created_by'  => Auth::id(),
            'updated_by'  => Auth::id(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Create progress rows for each step
        $steps = DB::table('cims_job_card_type_steps')
            ->where('job_type_id', $data['job_type_id'])
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        foreach ($steps as $step) {
            DB::table('cims_job_card_progress')->insert([
                'job_card_id' => $jobCardId,
                'step_id'     => $step->id,
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return $jobCardId;
    }

    /**
     * Get full job card detail for the show page.
     */
    public function getJobCard(int $id): ?array
    {
        $jobCard = DB::table('cims_job_cards')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$jobCard) return null;

        // Client info
        $client = DB::table('client_master')
            ->where('client_id', $jobCard->client_id)
            ->first();

        // Job type
        $jobType = DB::table('cims_job_card_types')
            ->where('id', $jobCard->job_type_id)
            ->first();

        // Progress with step details
        $progress = DB::table('cims_job_card_progress as p')
            ->join('cims_job_card_type_steps as s', 'p.step_id', '=', 's.id')
            ->where('p.job_card_id', $id)
            ->leftJoin('users as u', 'p.completed_by', '=', 'u.id')
            ->select([
                'p.*',
                's.step_name',
                's.step_description',
                's.step_type',
                's.is_required',
                's.display_order',
                'u.first_name as completed_by_name',
            ])
            ->orderBy('s.display_order')
            ->get();

        // Configured fields with values
        $fields = DB::table('cims_job_card_type_fields')
            ->where('job_type_id', $jobCard->job_type_id)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get()
            ->map(function ($f) use ($client) {
                $f->value = $client ? ($client->{$f->field_name} ?? '') : '';
                return $f;
            });

        // Required documents with status
        $requiredDocs = DB::table('cims_job_card_type_documents')
            ->where('job_type_id', $jobCard->job_type_id)
            ->where('is_active', 1)
            ->orderBy('display_order')
            ->get();

        // Attached documents
        $attachments = DB::table('cims_job_card_attachments')
            ->where('job_card_id', $id)
            ->orderBy('created_at')
            ->get();

        // Assigned user
        $assignedUser = null;
        if ($jobCard->assigned_to) {
            $assignedUser = DB::table('users')
                ->where('id', $jobCard->assigned_to)
                ->select(['id', 'first_name', 'last_name', 'email'])
                ->first();
        }

        return [
            'jobCard'      => $jobCard,
            'client'       => $client,
            'jobType'      => $jobType,
            'progress'     => $progress,
            'fields'       => $fields,
            'requiredDocs' => $requiredDocs,
            'attachments'  => $attachments,
            'assignedUser' => $assignedUser,
        ];
    }

    /**
     * Update a step's status and recalculate completion.
     */
    public function updateStepStatus(int $jobCardId, int $stepId, string $status, ?string $notes = null): array
    {
        $updateData = [
            'status'     => $status,
            'notes'      => $notes,
            'updated_at' => now(),
        ];

        if ($status === 'completed') {
            $updateData['completed_by'] = Auth::id();
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_by'] = null;
            $updateData['completed_at'] = null;
        }

        DB::table('cims_job_card_progress')
            ->where('job_card_id', $jobCardId)
            ->where('step_id', $stepId)
            ->update($updateData);

        // Recalculate completion percentage
        $percentage = $this->recalculateCompletion($jobCardId);

        // Auto-update job card status
        $jobCard = DB::table('cims_job_cards')->where('id', $jobCardId)->first();
        if ($jobCard->status === 'draft' && $percentage > 0) {
            DB::table('cims_job_cards')->where('id', $jobCardId)->update([
                'status'     => 'in_progress',
                'started_at' => $jobCard->started_at ?? now(),
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
        }

        return [
            'completion_percentage' => $percentage,
            'status' => $jobCard->status === 'draft' && $percentage > 0 ? 'in_progress' : $jobCard->status,
        ];
    }

    /**
     * Recalculate completion percentage for a job card.
     */
    public function recalculateCompletion(int $jobCardId): float
    {
        $total = DB::table('cims_job_card_progress')
            ->where('job_card_id', $jobCardId)
            ->whereIn('status', ['pending', 'in_progress', 'completed', 'skipped'])
            ->count();

        $completed = DB::table('cims_job_card_progress')
            ->where('job_card_id', $jobCardId)
            ->whereIn('status', ['completed', 'skipped'])
            ->count();

        $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        DB::table('cims_job_cards')
            ->where('id', $jobCardId)
            ->update([
                'completion_percentage' => $percentage,
                'updated_at' => now(),
            ]);

        return $percentage;
    }

    /**
     * Get job cards list with filters.
     */
    public function getJobCards(array $filters = []): array
    {
        $q = DB::table('cims_job_cards as jc')
            ->join('client_master as c', 'jc.client_id', '=', 'c.client_id')
            ->join('cims_job_card_types as jt', 'jc.job_type_id', '=', 'jt.id')
            ->leftJoin('users as u', 'jc.assigned_to', '=', 'u.id')
            ->whereNull('jc.deleted_at')
            ->select([
                'jc.*',
                'c.company_name',
                'c.client_code',
                'jt.name as job_type_name',
                'u.first_name as assigned_first_name',
                'u.last_name as assigned_last_name',
            ]);

        if (!empty($filters['status'])) {
            $q->where('jc.status', $filters['status']);
        }
        if (!empty($filters['job_type_id'])) {
            $q->where('jc.job_type_id', $filters['job_type_id']);
        }
        if (!empty($filters['assigned_to'])) {
            $q->where('jc.assigned_to', $filters['assigned_to']);
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $q->where(function ($w) use ($search) {
                $w->where('jc.job_code', 'LIKE', "%{$search}%")
                  ->orWhere('c.company_name', 'LIKE', "%{$search}%")
                  ->orWhere('c.client_code', 'LIKE', "%{$search}%");
            });
        }

        $total = $q->count();
        $jobCards = $q->orderByDesc('jc.created_at')->get();

        return [
            'jobCards' => $jobCards,
            'total'    => $total,
        ];
    }

    /**
     * Get dashboard statistics.
     */
    public function getDashboardStats(): array
    {
        $now = Carbon::now();

        // Status counts
        $statusCounts = DB::table('cims_job_cards')
            ->whereNull('deleted_at')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($statusCounts);
        $completed = ($statusCounts['completed'] ?? 0) + ($statusCounts['submitted'] ?? 0);
        $inProgress = $statusCounts['in_progress'] ?? 0;
        $overdue = DB::table('cims_job_cards')
            ->whereNull('deleted_at')
            ->whereNotIn('status', ['completed', 'submitted', 'cancelled'])
            ->where('due_date', '<', $now->format('Y-m-d'))
            ->count();

        // Jobs by type
        $byType = DB::table('cims_job_cards as jc')
            ->join('cims_job_card_types as jt', 'jc.job_type_id', '=', 'jt.id')
            ->whereNull('jc.deleted_at')
            ->select('jt.name', DB::raw('COUNT(*) as count'))
            ->groupBy('jt.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $label = $month->format('M Y');
            $created = DB::table('cims_job_cards')
                ->whereNull('deleted_at')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $completedMonth = DB::table('cims_job_cards')
                ->whereNull('deleted_at')
                ->whereIn('status', ['completed', 'submitted'])
                ->whereYear('completed_at', $month->year)
                ->whereMonth('completed_at', $month->month)
                ->count();
            $monthlyTrend[] = [
                'label'     => $label,
                'created'   => $created,
                'completed' => $completedMonth,
            ];
        }

        // Average completion time (days) for completed jobs
        $avgTime = DB::table('cims_job_cards')
            ->whereNull('deleted_at')
            ->whereIn('status', ['completed', 'submitted'])
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(DATEDIFF(completed_at, started_at)) as avg_days')
            ->value('avg_days');

        // Recent activity (last 10 job cards updated)
        $recentActivity = DB::table('cims_job_cards as jc')
            ->join('client_master as c', 'jc.client_id', '=', 'c.client_id')
            ->join('cims_job_card_types as jt', 'jc.job_type_id', '=', 'jt.id')
            ->whereNull('jc.deleted_at')
            ->select(['jc.id', 'jc.job_code', 'jc.status', 'jc.completion_percentage', 'jc.updated_at',
                       'c.company_name', 'c.client_code', 'jt.name as job_type_name'])
            ->orderByDesc('jc.updated_at')
            ->limit(10)
            ->get();

        // Jobs by assigned user
        $byAssignee = DB::table('cims_job_cards as jc')
            ->leftJoin('users as u', 'jc.assigned_to', '=', 'u.id')
            ->whereNull('jc.deleted_at')
            ->whereNotIn('jc.status', ['completed', 'submitted', 'cancelled'])
            ->select(
                DB::raw("COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unassigned') as assignee"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('assignee')
            ->orderByDesc('count')
            ->get();

        return [
            'total'          => $total,
            'inProgress'     => $inProgress,
            'completed'      => $completed,
            'overdue'        => $overdue,
            'draft'          => $statusCounts['draft'] ?? 0,
            'review'         => $statusCounts['review'] ?? 0,
            'submitted'      => $statusCounts['submitted'] ?? 0,
            'cancelled'      => $statusCounts['cancelled'] ?? 0,
            'statusCounts'   => $statusCounts,
            'byType'         => $byType,
            'monthlyTrend'   => $monthlyTrend,
            'avgCompletionDays' => round($avgTime ?? 0, 1),
            'recentActivity' => $recentActivity,
            'byAssignee'     => $byAssignee,
        ];
    }

    /**
     * Get users for assignment dropdown.
     */
    public function getUsers()
    {
        return DB::table('users')
            ->where('status', 'active')
            ->where('type', 'team')
            ->select(['id', 'first_name', 'last_name', 'email'])
            ->orderBy('first_name')
            ->get();
    }

    /**
     * Get directors/shareholders for a client (Beneficial Ownership).
     */
    public function getDirectorsForClient(int $clientId): array
    {
        $client = DB::table('client_master')
            ->where('client_id', $clientId)
            ->select(['client_id', 'company_name', 'client_code', 'number_of_shares', 'share_type_name', 'company_reg_number'])
            ->first();

        if (!$client) return ['client' => null, 'directors' => []];

        $totalShares = (int) ($client->number_of_shares ?? 0);

        $directors = DB::table('client_master_directors as d')
            ->leftJoin('cims_persons as p', 'd.person_id', '=', 'p.id')
            ->where('d.client_id', $clientId)
            ->select([
                'd.id as director_id',
                'd.person_id',
                'd.firstname',
                'd.surname',
                'd.identity_number',
                'd.number_of_director_shares',
                'd.share_percentage',
                'd.director_type_name',
                'd.director_status_name',
                'd.is_active',
                'd.id_front_image as dir_id_front',
                'd.id_back_image as dir_id_back',
                'p.id as person_id_check',
                'p.id_front_image as person_id_front',
                'p.id_back_image as person_id_back',
                'p.passport_image',
                'p.green_book_image',
                'p.signature_image',
                'p.poa_image',
                'p.poa_uploaded_at',
                'p.tax_number',
            ])
            ->orderBy('d.firstname')
            ->get()
            ->map(function ($d) use ($totalShares) {
                // Calculate share percentage if not stored
                $shares = (int) ($d->number_of_director_shares ?? 0);
                $d->calculated_percentage = $totalShares > 0
                    ? round(($shares / $totalShares) * 100, 2)
                    : 0;

                // Determine if ID document exists (check person first, then director copy)
                $d->has_id_front = !empty($d->person_id_front) || !empty($d->dir_id_front);
                $d->has_id_back = !empty($d->person_id_back) || !empty($d->dir_id_back);
                $d->has_passport = !empty($d->passport_image);
                $d->has_id_document = $d->has_id_front || $d->has_passport;

                // POA status
                $d->has_poa = !empty($d->poa_image);
                $d->poa_is_fresh = false;
                if ($d->has_poa && $d->poa_uploaded_at) {
                    $d->poa_is_fresh = Carbon::parse($d->poa_uploaded_at)->diffInDays(Carbon::now()) <= 60;
                }

                return $d;
            });

        return [
            'client' => $client,
            'totalShares' => $totalShares,
            'shareType' => $client->share_type_name ?: 'Ordinary Shares',
            'directors' => $directors,
        ];
    }

    /**
     * Fetch ID document from person record to job card attachment.
     */
    public function fetchIdDocument(int $directorId, int $jobCardId): array
    {
        $director = DB::table('client_master_directors as d')
            ->leftJoin('cims_persons as p', 'd.person_id', '=', 'p.id')
            ->where('d.id', $directorId)
            ->select([
                'd.id', 'd.firstname', 'd.surname', 'd.person_id',
                'd.id_front_image as dir_id_front',
                'd.id_back_image as dir_id_back',
                'p.id_front_image as person_id_front',
                'p.id_back_image as person_id_back',
                'p.passport_image',
            ])
            ->first();

        if (!$director) {
            return ['success' => false, 'message' => 'Director not found.'];
        }

        // Check which images exist — prefer person record
        $idFront = $director->person_id_front ?: $director->dir_id_front;
        $idBack = $director->person_id_back ?: $director->dir_id_back;
        $passport = $director->passport_image;

        if (!$idFront && !$passport) {
            return ['success' => false, 'message' => 'No ID document found on record for ' . $director->firstname . ' ' . $director->surname . '.'];
        }

        $fetched = [];

        // Save references as job card attachments
        $storagePath = config('job_cards.documents_path', 'job_cards/documents');
        $now = now();

        if ($idFront) {
            DB::table('cims_job_card_attachments')->insert([
                'job_card_id' => $jobCardId,
                'file_name' => basename($idFront),
                'file_original_name' => $director->firstname . '_' . $director->surname . '_ID_Front.' . pathinfo($idFront, PATHINFO_EXTENSION),
                'file_path' => $idFront,
                'file_type' => 'id_document',
                'file_mime_type' => 'image/' . pathinfo($idFront, PATHINFO_EXTENSION),
                'uploaded_by' => Auth::id(),
                'director_id' => $directorId,
                'document_category' => 'id_front',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $fetched[] = 'ID Front';
        }
        if ($idBack) {
            DB::table('cims_job_card_attachments')->insert([
                'job_card_id' => $jobCardId,
                'file_name' => basename($idBack),
                'file_original_name' => $director->firstname . '_' . $director->surname . '_ID_Back.' . pathinfo($idBack, PATHINFO_EXTENSION),
                'file_path' => $idBack,
                'file_type' => 'id_document',
                'file_mime_type' => 'image/' . pathinfo($idBack, PATHINFO_EXTENSION),
                'uploaded_by' => Auth::id(),
                'director_id' => $directorId,
                'document_category' => 'id_back',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $fetched[] = 'ID Back';
        }
        if ($passport && !$idFront) {
            DB::table('cims_job_card_attachments')->insert([
                'job_card_id' => $jobCardId,
                'file_name' => basename($passport),
                'file_original_name' => $director->firstname . '_' . $director->surname . '_Passport.' . pathinfo($passport, PATHINFO_EXTENSION),
                'file_path' => $passport,
                'file_type' => 'id_document',
                'file_mime_type' => 'image/' . pathinfo($passport, PATHINFO_EXTENSION),
                'uploaded_by' => Auth::id(),
                'director_id' => $directorId,
                'document_category' => 'passport',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $fetched[] = 'Passport';
        }

        return [
            'success' => true,
            'message' => 'Fetched: ' . implode(', ', $fetched) . ' for ' . $director->firstname . ' ' . $director->surname,
            'fetched' => $fetched,
        ];
    }

    /**
     * Get ID document attachments for a job card grouped by director.
     */
    public function getDirectorDocuments(int $jobCardId): array
    {
        $docs = DB::table('cims_job_card_attachments')
            ->where('job_card_id', $jobCardId)
            ->whereIn('file_type', ['id_document', 'poa_document'])
            ->orderBy('created_at')
            ->get();

        $byDirector = [];
        foreach ($docs as $doc) {
            $dirId = $doc->director_id ?? 0;
            $byDirector[$dirId][] = $doc;
        }

        return $byDirector;
    }

    /**
     * Update job card details.
     */
    public function updateJobCard(int $id, array $data): bool
    {
        $update = array_filter([
            'assigned_to' => $data['assigned_to'] ?? null,
            'priority'    => $data['priority'] ?? null,
            'due_date'    => $data['due_date'] ?? null,
            'notes'       => $data['notes'] ?? null,
            'status'      => $data['status'] ?? null,
        ], fn($v) => $v !== null);

        $update['updated_by'] = Auth::id();
        $update['updated_at'] = now();

        if (isset($update['status'])) {
            if ($update['status'] === 'completed') {
                $update['completed_at'] = now();
            }
            if ($update['status'] === 'submitted') {
                $update['submitted_at'] = now();
            }
        }

        return DB::table('cims_job_cards')->where('id', $id)->update($update) > 0;
    }

    /**
     * Soft-delete a job card.
     */
    public function deleteJobCard(int $id): bool
    {
        return DB::table('cims_job_cards')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_by' => Auth::id(),
        ]) > 0;
    }
}
