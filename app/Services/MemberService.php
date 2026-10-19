<?php

namespace App\Services;

use App\Enums\MemberType;
use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MemberService
{
    /**
     * Get paginated members with filters.
     */
    public function getPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = Member::query()->with('user');

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('member_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['class'])) {
            $query->where('class', $filters['class']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['year_level'])) {
            $query->where('year_level', $filters['year_level']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDir = $filters['sort_dir'] ?? 'desc';
        $query->orderBy($sortBy, $sortDir);

        // Pagination
        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Get all members for export.
     */
    public function getAllForExport(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Member::query()->with(['user', 'loans', 'fines']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Create a new member.
     */
    public function create(array $data): Member
    {
        return DB::transaction(function () use ($data) {
            // Generate member number
            $data['member_number'] = $data['member_number'] ?? Member::generateMemberNumber();

            // Set join date if not provided
            $data['join_date'] = $data['join_date'] ?? now()->toDateString();

            // Calculate expiry date
            if (!empty($data['join_date'])) {
                $setting = app('settings')->getSetting('member.card_validity_years');
                $validityYears = $setting['value'] ?? 3;
                $data['expired_date'] = \Carbon\Carbon::parse($data['join_date'])->addYears($validityYears)->toDateString();
            }

            // Create member
            $member = Member::create($data);

            // Create associated user if type is teacher or if explicitly requested
            if (($data['type'] === MemberType::Teacher->value || !empty($data['create_user'])) && !empty($data['email'])) {
                $this->createUserFromMember($member, $data);
            }

            return $member;
        });
    }

    /**
     * Update a member.
     */
    public function update(Member $member, array $data): Member
    {
        return DB::transaction(function () use ($member, $data) {
            // Remove fields that shouldn't be directly updated
            unset($data['member_number']);

            $member->update($data);

            return $member->fresh();
        });
    }

    /**
     * Delete a member.
     */
    public function delete(Member $member): bool
    {
        return DB::transaction(function () use ($member) {
            // Check if member has active loans
            if ($member->activeLoansCount() > 0) {
                throw new \Exception('Tidak dapat menghapus anggota yang masih memiliki peminjaman aktif.');
            }

            // Check if member has unpaid fines
            if ($member->unpaidFinesTotal() > 0) {
                throw new \Exception('Tidak dapat menghapus anggota yang masih memiliki denda belum lunas.');
            }

            // Delete associated user if exists
            if ($member->user_id) {
                $member->user->delete();
            }

            return $member->delete();
        });
    }

    /**
     * Create a user from member data.
     */
    public function createUserFromMember(Member $member, array $data = []): \App\Models\User
    {
        $email = $data['email'] ?? $member->email;
        $name = $data['name'] ?? $member->name;

        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'), // Default password
            'role' => $member->type === MemberType::Teacher->value ? \App\Enums\Role::Teacher : \App\Enums\Role::Student,
            'is_active' => true,
        ]);

        $member->update(['user_id' => $user->id, 'email' => $email]);

        return $user;
    }

    /**
     * Block a member.
     */
    public function block(Member $member, string $reason = null): Member
    {
        return $member->update([
            'status' => 'blocked',
            'notes' => $member->notes . "\n[DIBLOKIR] " . now()->toDateString() . ": " . ($reason ?? 'Tidak ada alasan'),
        ]);
    }

    /**
     * Unblock a member.
     */
    public function unblock(Member $member): Member
    {
        return $member->update(['status' => 'active']);
    }

    /**
     * Get member statistics.
     */
    public function getStats(): array
    {
        $total = Member::count();
        $active = Member::where('status', 'active')->count();
        $blocked = Member::where('status', 'blocked')->count();
        $inactive = Member::where('status', 'inactive')->count();

        $students = Member::where('type', MemberType::Student->value)->count();
        $teachers = Member::where('type', MemberType::Teacher->value)->count();

        return [
            'total' => $total,
            'active' => $active,
            'blocked' => $blocked,
            'inactive' => $inactive,
            'students' => $students,
            'teachers' => $teachers,
        ];
    }

    /**
     * Get unique classes for filter dropdown.
     */
    public function getUniqueClasses(): array
    {
        return Member::whereNotNull('class')
            ->where('class', '!=', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class')
            ->toArray();
    }

    /**
     * Get unique year levels for filter dropdown.
     */
    public function getUniqueYearLevels(): array
    {
        return Member::whereNotNull('year_level')
            ->where('year_level', '!=', '')
            ->distinct()
            ->orderBy('year_level')
            ->pluck('year_level')
            ->toArray();
    }
}