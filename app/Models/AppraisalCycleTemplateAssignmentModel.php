<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalCycleTemplateAssignmentModel extends Model
{
    protected $table = 'appraisal_cycle_template_assignments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields = true;

    protected $allowedFields = [
        'appraisal_cycle_id',
        'template_id',
        'review_type',
        'reviewer_role_id',
        'assignment_type',
        'department_id',
        'designation_id',
        'employee_id',
        'priority'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAssignments(int $cycleId): array
    {
        return $this->builder()
            ->select([
                'appraisal_cycle_template_assignments.*',
                'appraisal_templates.template_name',
                'departments.name AS department_name',
                'designations.title AS designation_name',
                'reviewer_role.name AS reviewer_role_name',
                'reviewer_role.display_name AS reviewer_role_display_name',
                "COALESCE(reviewer_role.display_name, reviewer_role.name) AS reviewer_role_label",
                "CONCAT(
                COALESCE(users.first_name, ''),
                CASE
                    WHEN users.last_name IS NOT NULL
                    AND users.last_name != ''
                    THEN CONCAT(' ', users.last_name)
                    ELSE ''
                END
            ) AS employee_name",
                'users.employee_code',
                "CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM appraisals ap
                    INNER JOIN users appraisal_employee
                        ON appraisal_employee.id = ap.employee_id
                    WHERE ap.appraisal_cycle_id = appraisal_cycle_template_assignments.appraisal_cycle_id
                        AND ap.status != 'pending'
                        AND (
                            (appraisal_cycle_template_assignments.assignment_type = 'employee'
                                AND ap.employee_id = appraisal_cycle_template_assignments.employee_id)

                            OR

                            (appraisal_cycle_template_assignments.assignment_type = 'designation'
                                AND appraisal_employee.designation_id = appraisal_cycle_template_assignments.designation_id)

                            OR

                            (appraisal_cycle_template_assignments.assignment_type = 'department'
                                AND appraisal_employee.department_id = appraisal_cycle_template_assignments.department_id)
                        )
                        AND ap.review_type = appraisal_cycle_template_assignments.review_type
                        AND (
                            (appraisal_cycle_template_assignments.review_type = 'self'
                                AND ap.reviewer_role_id IS NULL)

                            OR

                            (appraisal_cycle_template_assignments.review_type = 'matrix'
                                AND ap.reviewer_role_id = appraisal_cycle_template_assignments.reviewer_role_id)
                        )
                )
                THEN 0
                ELSE 1
            END AS can_edit"
            ])
            ->join(
                'appraisal_templates',
                'appraisal_templates.id = appraisal_cycle_template_assignments.template_id',
                'left'
            )
            ->join(
                'departments',
                'departments.id = appraisal_cycle_template_assignments.department_id',
                'left'
            )
            ->join(
                'designations',
                'designations.id = appraisal_cycle_template_assignments.designation_id',
                'left'
            )
            ->join(
                'users',
                'users.id = appraisal_cycle_template_assignments.employee_id',
                'left'
            )
            ->join(
                'roles reviewer_role',
                'reviewer_role.id = appraisal_cycle_template_assignments.reviewer_role_id',
                'left'
            )
            ->where(
                'appraisal_cycle_template_assignments.appraisal_cycle_id',
                $cycleId
            )
            ->orderBy(
                "CASE
                WHEN appraisal_cycle_template_assignments.review_type = 'self' THEN 1
                WHEN appraisal_cycle_template_assignments.review_type = 'matrix' THEN 2
                ELSE 3
            END",
                'ASC',
                false
            )
            ->orderBy(
                'appraisal_cycle_template_assignments.priority',
                'DESC'
            )
            ->orderBy(
                'appraisal_cycle_template_assignments.id',
                'DESC'
            )
            ->get()
            ->getResultArray();
    }

    /**
     * Check whether an assignment already exists.
     */
    public function assignmentExists(
        int $cycleId,
        string $reviewType,
        ?int $reviewerRoleId,
        string $assignmentType,
        ?int $targetId,
        ?int $excludeAssignmentId = null
    ): bool {
        $builder = $this->builder()
            ->where('appraisal_cycle_id', $cycleId)
            ->where('review_type', $reviewType)
            ->where('assignment_type', $assignmentType);

        if ($reviewType === 'matrix') {
            $builder->where('reviewer_role_id', $reviewerRoleId);
        } else {
            $builder->where('reviewer_role_id IS NULL', null, false);
        }

        switch ($assignmentType) {
            case 'department':
                $builder->where('department_id', $targetId);
                break;

            case 'designation':
                $builder->where('designation_id', $targetId);
                break;

            case 'employee':
                $builder->where('employee_id', $targetId);
                break;
        }

        if ($excludeAssignmentId !== null) {
            $builder->where('id !=', $excludeAssignmentId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Resolve template for one employee and review type.
     *
     * Priority:
     * Employee > Designation > Department
     *
     * For matrix review:
     * reviewer_role_id is required.
     *
     * For self review:
     * reviewer_role_id is NULL.
     */
    public function resolveTemplate(
        int $cycleId,
        int $employeeId,
        string $reviewType,
        ?int $reviewerRoleId = null
    ): ?array {
        $sql = "
            SELECT
                a.id AS assignment_id,
                a.appraisal_cycle_id,
                a.template_id,
                a.review_type,
                a.reviewer_role_id,
                a.assignment_type,
                a.priority,
                t.template_name,

                CASE
                    WHEN a.assignment_type = 'employee' THEN 300
                    WHEN a.assignment_type = 'designation' THEN 200
                    WHEN a.assignment_type = 'department' THEN 100
                    ELSE 0
                END AS resolution_priority

            FROM users u

            INNER JOIN appraisal_cycle_template_assignments a
                ON a.appraisal_cycle_id = ?

            INNER JOIN appraisal_templates t
                ON t.id = a.template_id

            WHERE u.id = ?
                AND a.review_type = ?
                AND (
                    (? = 'self' AND a.reviewer_role_id IS NULL)
                    OR
                    (? = 'matrix' AND a.reviewer_role_id = ?)
                )
                AND (
                    (a.assignment_type = 'employee' AND a.employee_id = u.id)
                    OR
                    (a.assignment_type = 'designation' AND a.designation_id = u.designation_id)
                    OR
                    (a.assignment_type = 'department' AND a.department_id = u.department_id)
                )

            ORDER BY
                resolution_priority DESC,
                a.priority DESC,
                a.id DESC

            LIMIT 1
        ";

        return db_connect()
            ->query(
                $sql,
                [
                    $cycleId,
                    $employeeId,
                    $reviewType,
                    $reviewType,
                    $reviewType,
                    $reviewerRoleId
                ]
            )
            ->getRowArray();
    }

    /**
     * Get resolved participant information.
     *
     * Self-review template is resolved separately.
     */
    public function getResolvedParticipants(int $cycleId): array
    {
        $participants = db_connect()
            ->table('appraisal_cycle_participants p')
            ->select([
                'p.*',
                'u.first_name',
                'u.last_name',
                'u.employee_code',
                'u.email',
                'd.name AS department_name',
                'des.title AS designation_name'
            ])
            ->join('users u', 'u.id = p.employee_id')
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->join('designations des', 'des.id = u.designation_id', 'left')
            ->where('p.appraisal_cycle_id', $cycleId)
            ->orderBy('p.id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($participants as &$participant) {
            $template = $this->resolveTemplate(
                $cycleId,
                (int)$participant['employee_id'],
                'self'
            );

            $participant['self_template_id'] = $template['template_id'] ?? null;
            $participant['self_template_name'] = $template['template_name'] ?? null;
            $participant['self_template_source'] = $template['assignment_type'] ?? null;
        }

        return $participants;
    }

    /**
     * Get self-review opportunities for an employee.
     */
    public function getEmployeeMyReviews(int $employeeId): array
    {
        $sql = "
            SELECT
                p.id AS participant_id,
                p.employee_id,
                p.status AS participant_status,

                c.id AS cycle_id,
                c.cycle_name,
                c.cycle_code,
                c.description AS cycle_description,
                c.start_date,
                c.end_date,
                c.status AS cycle_status

            FROM appraisal_cycle_participants p

            INNER JOIN appraisal_cycles c
                ON c.id = p.appraisal_cycle_id

            WHERE p.employee_id = ?
                AND p.status = 'active'
                AND c.status = 'active'

            ORDER BY c.start_date DESC, c.id DESC
        ";

        $reviews = db_connect()
            ->query($sql, [$employeeId])
            ->getResultArray();

        foreach ($reviews as &$review) {
            $template = $this->resolveTemplate(
                (int)$review['cycle_id'],
                $employeeId,
                'self'
            );

            $review['resolved_template_id'] = $template['template_id'] ?? null;
            $review['resolved_template_name'] = $template['template_name'] ?? null;
            $review['template_source'] = $template['assignment_type'] ?? null;
        }

        return array_values(array_filter(
            $reviews,
            fn($review) => $review['resolved_template_id'] !== null
        ));
    }

    /**
     * Resolve matrix template.
     */
    public function resolveMatrixTemplate(
        int $cycleId,
        int $employeeId,
        int $reviewerRoleId
    ): ?array {
        return $this->resolveTemplate(
            $cycleId,
            $employeeId,
            'matrix',
            $reviewerRoleId
        );
    }
}








// class AppraisalCycleTemplateAssignmentModel extends Model
// {
//     protected $table =
//     'appraisal_cycle_template_assignments';

//     protected $primaryKey =
//     'id';

//     protected $returnType =
//     'array';

//     protected $useAutoIncrement =
//     true;

//     protected $protectFields =
//     true;

//     protected $allowedFields = [

//         'appraisal_cycle_id',

//         'template_id',

//         'assignment_type',

//         'department_id',

//         'designation_id',

//         'employee_id',

//         'priority'
//     ];

//     protected $useTimestamps =
//     true;

//     protected $dateFormat =
//     'datetime';

//     protected $createdField =
//     'created_at';

//     protected $updatedField =
//     'updated_at';


//     /**
//      * Get all assignment rules for a cycle.
//      */
//     public function getAssignments(
//         int $cycleId
//     ): array {

//         return $this
//             ->builder()

//             ->select([

//                 'appraisal_cycle_template_assignments.*',

//                 'appraisal_templates.template_name',

//                 'departments.name AS department_name',

//                 'designations.title AS designation_name',

//                 "CONCAT(
//                     COALESCE(users.first_name, ''),
//                     CASE
//                         WHEN users.last_name IS NOT NULL
//                         AND users.last_name != ''
//                         THEN CONCAT(' ', users.last_name)
//                         ELSE ''
//                     END
//                 ) AS employee_name",

//                 'users.employee_code'

//             ])

//             ->join(
//                 'appraisal_templates',

//                 'appraisal_templates.id = '
//                     . 'appraisal_cycle_template_assignments.template_id',

//                 'left'
//             )

//             ->join(
//                 'departments',

//                 'departments.id = '
//                     . 'appraisal_cycle_template_assignments.department_id',

//                 'left'
//             )

//             ->join(
//                 'designations',

//                 'designations.id = '
//                     . 'appraisal_cycle_template_assignments.designation_id',

//                 'left'
//             )

//             ->join(
//                 'users',

//                 'users.id = '
//                     . 'appraisal_cycle_template_assignments.employee_id',

//                 'left'
//             )

//             ->where(
//                 'appraisal_cycle_template_assignments.appraisal_cycle_id',
//                 $cycleId
//             )

//             ->orderBy(
//                 'appraisal_cycle_template_assignments.priority',
//                 'DESC'
//             )

//             ->orderBy(
//                 'appraisal_cycle_template_assignments.id',
//                 'DESC'
//             )

//             ->get()

//             ->getResultArray();
//     }


//     /**
//      * Check whether assignment already exists
//      * for the same target.
//      */
//     public function assignmentExists(
//         int $cycleId,
//         string $assignmentType,
//         ?int $targetId,
//         ?int $excludeAssignmentId = null
//     ): bool {

//         $builder =
//             $this
//             ->builder()

//             ->where(
//                 'appraisal_cycle_id',
//                 $cycleId
//             )

//             ->where(
//                 'assignment_type',
//                 $assignmentType
//             );


//         switch ($assignmentType) {

//             case 'department':

//                 $builder->where(
//                     'department_id',
//                     $targetId
//                 );

//                 break;


//             case 'designation':

//                 $builder->where(
//                     'designation_id',
//                     $targetId
//                 );

//                 break;


//             case 'employee':

//                 $builder->where(
//                     'employee_id',
//                     $targetId
//                 );

//                 break;
//         }


//         if (
//             $excludeAssignmentId !== null
//         ) {

//             $builder->where(
//                 'id !=',
//                 $excludeAssignmentId
//             );
//         }


//         return
//             $builder
//             ->countAllResults()
//             > 0;
//     }


//     /**
//      * Resolve the applicable template for
//      * every participant dynamically.
//      *
//      * Priority:
//      *
//      * Employee     = 300
//      * Designation  = 200
//      * Department   = 100
//      */
//     public function getResolvedParticipants(
//         int $cycleId
//     ): array {

//         $sql = "

//             SELECT

//                 p.*,

//                 u.first_name,
//                 u.last_name,
//                 u.employee_code,
//                 u.email,

//                 d.name AS department_name,

//                 des.title AS designation_name,

//                 r.name AS role_name,

//                 r.display_name AS role_display_name,


//                 COALESCE(
//                     employee_assignment.template_id,
//                     designation_assignment.template_id,
//                     department_assignment.template_id
//                 ) AS resolved_template_id,


//                 COALESCE(
//                     employee_template.template_name,
//                     designation_template.template_name,
//                     department_template.template_name
//                 ) AS resolved_template_name,


//                 CASE

//                     WHEN employee_assignment.template_id IS NOT NULL
//                     THEN 'employee'

//                     WHEN designation_assignment.template_id IS NOT NULL
//                     THEN 'designation'

//                     WHEN department_assignment.template_id IS NOT NULL
//                     THEN 'department'

//                     ELSE NULL

//                 END AS template_source


//             FROM appraisal_cycle_participants p


//             INNER JOIN users u

//                 ON u.id = p.employee_id


//             LEFT JOIN departments d

//                 ON d.id = u.department_id


//             LEFT JOIN designations des

//                 ON des.id = u.designation_id


//             LEFT JOIN user_roles ur

//                 ON ur.user_id = u.id


//             LEFT JOIN roles r

//                 ON r.id = ur.role_id


//             LEFT JOIN appraisal_cycle_template_assignments employee_assignment

//                 ON employee_assignment.appraisal_cycle_id = p.appraisal_cycle_id

//                 AND employee_assignment.assignment_type = 'employee'

//                 AND employee_assignment.employee_id = p.employee_id


//             LEFT JOIN appraisal_templates employee_template

//                 ON employee_template.id = employee_assignment.template_id


//             LEFT JOIN appraisal_cycle_template_assignments designation_assignment

//                 ON designation_assignment.appraisal_cycle_id = p.appraisal_cycle_id

//                 AND designation_assignment.assignment_type = 'designation'

//                 AND designation_assignment.designation_id = u.designation_id


//             LEFT JOIN appraisal_templates designation_template

//                 ON designation_template.id = designation_assignment.template_id


//             LEFT JOIN appraisal_cycle_template_assignments department_assignment

//                 ON department_assignment.appraisal_cycle_id = p.appraisal_cycle_id

//                 AND department_assignment.assignment_type = 'department'

//                 AND department_assignment.department_id = u.department_id


//             LEFT JOIN appraisal_templates department_template

//                 ON department_template.id = department_assignment.template_id


//             WHERE p.appraisal_cycle_id = ?


//             ORDER BY p.id DESC

//         ";


//         return db_connect()

//             ->query(
//                 $sql,
//                 [$cycleId]
//             )

//             ->getResultArray();
//     }

//     public function getEmployeeMyReviews(int $employeeId): array
//     {
//         $sql = "
//         SELECT
//             p.id AS participant_id,
//             p.employee_id,
//             p.status AS participant_status,
//             c.id AS cycle_id,
//             c.cycle_name,
//             c.cycle_code,
//             c.description AS cycle_description,
//             c.start_date,
//             c.end_date,
//             c.status AS cycle_status,

//             COALESCE(
//                 employee_assignment.template_id,
//                 designation_assignment.template_id,
//                 department_assignment.template_id
//             ) AS resolved_template_id,

//             COALESCE(
//                 employee_template.template_name,
//                 designation_template.template_name,
//                 department_template.template_name
//             ) AS resolved_template_name,

//             CASE
//                 WHEN employee_assignment.template_id IS NOT NULL THEN 'employee'
//                 WHEN designation_assignment.template_id IS NOT NULL THEN 'designation'
//                 WHEN department_assignment.template_id IS NOT NULL THEN 'department'
//                 ELSE NULL
//             END AS template_source

//             FROM appraisal_cycle_participants p

//             INNER JOIN appraisal_cycles c ON c.id = p.appraisal_cycle_id
//             INNER JOIN users u ON u.id = p.employee_id

//             LEFT JOIN appraisal_cycle_template_assignments employee_assignment
//                 ON employee_assignment.appraisal_cycle_id = p.appraisal_cycle_id
//                 AND employee_assignment.assignment_type = 'employee'
//                 AND employee_assignment.employee_id = p.employee_id

//             LEFT JOIN appraisal_templates employee_template
//                 ON employee_template.id = employee_assignment.template_id

//             LEFT JOIN appraisal_cycle_template_assignments designation_assignment
//                 ON designation_assignment.appraisal_cycle_id = p.appraisal_cycle_id
//                 AND designation_assignment.assignment_type = 'designation'
//                 AND designation_assignment.designation_id = u.designation_id

//             LEFT JOIN appraisal_templates designation_template
//                 ON designation_template.id = designation_assignment.template_id

//             LEFT JOIN appraisal_cycle_template_assignments department_assignment
//                 ON department_assignment.appraisal_cycle_id = p.appraisal_cycle_id
//                 AND department_assignment.assignment_type = 'department'
//                 AND department_assignment.department_id = u.department_id

//             LEFT JOIN appraisal_templates department_template
//                 ON department_template.id = department_assignment.template_id

//             WHERE p.employee_id = ?
//                 AND p.status = 'active'
//                 AND c.status = 'active'

//             HAVING resolved_template_id IS NOT NULL

//             ORDER BY c.start_date DESC, c.id DESC
//         ";

//         return db_connect()->query($sql, [$employeeId])->getResultArray();
//     }
// }
