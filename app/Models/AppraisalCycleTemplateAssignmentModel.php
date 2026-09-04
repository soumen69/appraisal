<?php

namespace App\Models;

use CodeIgniter\Model;

class AppraisalCycleTemplateAssignmentModel extends Model
{
    protected $table =
    'appraisal_cycle_template_assignments';

    protected $primaryKey =
    'id';

    protected $returnType =
    'array';

    protected $useAutoIncrement =
    true;

    protected $protectFields =
    true;

    protected $allowedFields = [

        'appraisal_cycle_id',

        'template_id',

        'assignment_type',

        'department_id',

        'designation_id',

        'employee_id',

        'priority'
    ];

    protected $useTimestamps =
    true;

    protected $dateFormat =
    'datetime';

    protected $createdField =
    'created_at';

    protected $updatedField =
    'updated_at';


    /**
     * Get all assignment rules for a cycle.
     */
    public function getAssignments(
        int $cycleId
    ): array {

        return $this
            ->builder()

            ->select([

                'appraisal_cycle_template_assignments.*',

                'appraisal_templates.template_name',

                'departments.name AS department_name',

                'designations.title AS designation_name',

                "CONCAT(
                    COALESCE(users.first_name, ''),
                    CASE
                        WHEN users.last_name IS NOT NULL
                        AND users.last_name != ''
                        THEN CONCAT(' ', users.last_name)
                        ELSE ''
                    END
                ) AS employee_name",

                'users.employee_code'

            ])

            ->join(
                'appraisal_templates',

                'appraisal_templates.id = '
                    . 'appraisal_cycle_template_assignments.template_id',

                'left'
            )

            ->join(
                'departments',

                'departments.id = '
                    . 'appraisal_cycle_template_assignments.department_id',

                'left'
            )

            ->join(
                'designations',

                'designations.id = '
                    . 'appraisal_cycle_template_assignments.designation_id',

                'left'
            )

            ->join(
                'users',

                'users.id = '
                    . 'appraisal_cycle_template_assignments.employee_id',

                'left'
            )

            ->where(
                'appraisal_cycle_template_assignments.appraisal_cycle_id',
                $cycleId
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
     * Check whether assignment already exists
     * for the same target.
     */
    public function assignmentExists(
        int $cycleId,
        string $assignmentType,
        ?int $targetId,
        ?int $excludeAssignmentId = null
    ): bool {

        $builder =
            $this
            ->builder()

            ->where(
                'appraisal_cycle_id',
                $cycleId
            )

            ->where(
                'assignment_type',
                $assignmentType
            );


        switch ($assignmentType) {

            case 'department':

                $builder->where(
                    'department_id',
                    $targetId
                );

                break;


            case 'designation':

                $builder->where(
                    'designation_id',
                    $targetId
                );

                break;


            case 'employee':

                $builder->where(
                    'employee_id',
                    $targetId
                );

                break;
        }


        if (
            $excludeAssignmentId !== null
        ) {

            $builder->where(
                'id !=',
                $excludeAssignmentId
            );
        }


        return
            $builder
            ->countAllResults()
            > 0;
    }


    /**
     * Resolve the applicable template for
     * every participant dynamically.
     *
     * Priority:
     *
     * Employee     = 300
     * Designation  = 200
     * Department   = 100
     */
    public function getResolvedParticipants(
        int $cycleId
    ): array {

        $sql = "

            SELECT

                p.*,

                u.first_name,
                u.last_name,
                u.employee_code,
                u.email,

                d.name AS department_name,

                des.title AS designation_name,

                r.name AS role_name,

                r.display_name AS role_display_name,


                COALESCE(
                    employee_assignment.template_id,
                    designation_assignment.template_id,
                    department_assignment.template_id
                ) AS resolved_template_id,


                COALESCE(
                    employee_template.template_name,
                    designation_template.template_name,
                    department_template.template_name
                ) AS resolved_template_name,


                CASE

                    WHEN employee_assignment.template_id IS NOT NULL
                    THEN 'employee'

                    WHEN designation_assignment.template_id IS NOT NULL
                    THEN 'designation'

                    WHEN department_assignment.template_id IS NOT NULL
                    THEN 'department'

                    ELSE NULL

                END AS template_source


            FROM appraisal_cycle_participants p


            INNER JOIN users u

                ON u.id = p.employee_id


            LEFT JOIN departments d

                ON d.id = u.department_id


            LEFT JOIN designations des

                ON des.id = u.designation_id


            LEFT JOIN user_roles ur

                ON ur.user_id = u.id


            LEFT JOIN roles r

                ON r.id = ur.role_id


            LEFT JOIN appraisal_cycle_template_assignments employee_assignment

                ON employee_assignment.appraisal_cycle_id = p.appraisal_cycle_id

                AND employee_assignment.assignment_type = 'employee'

                AND employee_assignment.employee_id = p.employee_id


            LEFT JOIN appraisal_templates employee_template

                ON employee_template.id = employee_assignment.template_id


            LEFT JOIN appraisal_cycle_template_assignments designation_assignment

                ON designation_assignment.appraisal_cycle_id = p.appraisal_cycle_id

                AND designation_assignment.assignment_type = 'designation'

                AND designation_assignment.designation_id = u.designation_id


            LEFT JOIN appraisal_templates designation_template

                ON designation_template.id = designation_assignment.template_id


            LEFT JOIN appraisal_cycle_template_assignments department_assignment

                ON department_assignment.appraisal_cycle_id = p.appraisal_cycle_id

                AND department_assignment.assignment_type = 'department'

                AND department_assignment.department_id = u.department_id


            LEFT JOIN appraisal_templates department_template

                ON department_template.id = department_assignment.template_id


            WHERE p.appraisal_cycle_id = ?


            ORDER BY p.id DESC

        ";


        return db_connect()

            ->query(
                $sql,
                [$cycleId]
            )

            ->getResultArray();
    }

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
            c.status AS cycle_status,

            COALESCE(
                employee_assignment.template_id,
                designation_assignment.template_id,
                department_assignment.template_id
            ) AS resolved_template_id,

            COALESCE(
                employee_template.template_name,
                designation_template.template_name,
                department_template.template_name
            ) AS resolved_template_name,

            CASE
                WHEN employee_assignment.template_id IS NOT NULL THEN 'employee'
                WHEN designation_assignment.template_id IS NOT NULL THEN 'designation'
                WHEN department_assignment.template_id IS NOT NULL THEN 'department'
                ELSE NULL
            END AS template_source

            FROM appraisal_cycle_participants p

            INNER JOIN appraisal_cycles c ON c.id = p.appraisal_cycle_id
            INNER JOIN users u ON u.id = p.employee_id

            LEFT JOIN appraisal_cycle_template_assignments employee_assignment
                ON employee_assignment.appraisal_cycle_id = p.appraisal_cycle_id
                AND employee_assignment.assignment_type = 'employee'
                AND employee_assignment.employee_id = p.employee_id

            LEFT JOIN appraisal_templates employee_template
                ON employee_template.id = employee_assignment.template_id

            LEFT JOIN appraisal_cycle_template_assignments designation_assignment
                ON designation_assignment.appraisal_cycle_id = p.appraisal_cycle_id
                AND designation_assignment.assignment_type = 'designation'
                AND designation_assignment.designation_id = u.designation_id

            LEFT JOIN appraisal_templates designation_template
                ON designation_template.id = designation_assignment.template_id

            LEFT JOIN appraisal_cycle_template_assignments department_assignment
                ON department_assignment.appraisal_cycle_id = p.appraisal_cycle_id
                AND department_assignment.assignment_type = 'department'
                AND department_assignment.department_id = u.department_id

            LEFT JOIN appraisal_templates department_template
                ON department_template.id = department_assignment.template_id

            WHERE p.employee_id = ?
                AND p.status = 'active'
                AND c.status = 'active'

            HAVING resolved_template_id IS NOT NULL

            ORDER BY c.start_date DESC, c.id DESC
        ";

        return db_connect()->query($sql, [$employeeId])->getResultArray();
    }
}
