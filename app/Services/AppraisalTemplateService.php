<?php

namespace App\Services;

use App\Models\AppraisalTemplateModel;
use App\Models\AppraisalTemplateSectionModel;
use App\Models\AppraisalQuestionModel;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AppraisalTemplateService
{
    protected AppraisalTemplateModel $templates;

    protected AppraisalTemplateSectionModel $sections;

    protected AppraisalQuestionModel $questions;

    public function __construct()
    {
        $this->templates =
            new AppraisalTemplateModel();

        $this->sections =
            new AppraisalTemplateSectionModel();

        $this->questions =
            new AppraisalQuestionModel();
    }


    public function getTemplates(
        array $filters = []
    ): array {

        $page =
            max(
                1,
                (int) (
                    $filters['page']
                    ?? 1
                )
            );


        $pageSize =
            (int) (
                $filters['pageSize']
                ?? 10
            );


        if (
            !in_array(
                $pageSize,
                [10, 25, 50, 100],
                true
            )
        ) {

            $pageSize = 10;
        }


        return
            $this->templates->getTemplates(

                $page,

                $pageSize,

                trim(
                    $filters['search']
                        ?? ''
                ),

                trim(
                    $filters['status']
                        ?? ''
                ),

                trim(
                    $filters['orderBy']
                        ?? 'id'
                ),

                trim(
                    $filters['direction']
                        ?? 'desc'
                )
            );
    }


    public function getTemplate(
        int $id
    ): ?array {

        return
            $this->templates->getTemplate(
                $id
            );
    }


    public function createTemplate(
        array $data
    ): int {

        $errors =
            $this->validateTemplateData(
                $data
            );


        if (!empty($errors)) {

            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }


        $organizationId =
            (int) $data['organization_id'];


        $templateName =
            trim(
                $data['template_name']
            );


        $this->validateOrganization(
            $organizationId
        );


        if (
            $this->templates->templateNameExists(
                $organizationId,
                $templateName
            )
        ) {

            throw new InvalidArgumentException(
                json_encode([

                    'template_name' =>
                    'A template with this name already exists for the selected organization.'

                ])
            );
        }


        $isDefault =
            !empty($data['is_default'])
            ? 1
            : 0;


        $db = db_connect();

        $db->transStart();


        /*
         * Only one default template
         * per organization.
         */
        if ($isDefault === 1) {

            $this->templates
                ->clearDefaultTemplate(
                    $organizationId
                );
        }


        $templateId =
            $this->templates->insert(
                $this->buildTemplateData(
                    $data
                ),
                true
            );


        $db->transComplete();


        if (
            $db->transStatus() === false ||
            !$templateId
        ) {

            throw new RuntimeException(
                'Unable to create appraisal template.'
            );
        }


        return (int) $templateId;
    }


    public function updateTemplate(
        int $id,
        array $data
    ): void {

        $template =
            $this->templates->find($id);


        if (!$template) {

            throw new RuntimeException(
                'Appraisal template not found.'
            );
        }


        $errors =
            $this->validateTemplateData(
                $data
            );


        if (!empty($errors)) {

            throw new InvalidArgumentException(
                json_encode($errors)
            );
        }


        $organizationId =
            (int) $data['organization_id'];


        $templateName =
            trim(
                $data['template_name']
            );


        $this->validateOrganization(
            $organizationId
        );


        if (
            $this->templates->templateNameExists(
                $organizationId,
                $templateName,
                $id
            )
        ) {

            throw new InvalidArgumentException(
                json_encode([

                    'template_name' =>
                    'A template with this name already exists for the selected organization.'

                ])
            );
        }


        $isDefault =
            !empty($data['is_default'])
            ? 1
            : 0;


        $db = db_connect();

        $db->transStart();


        if ($isDefault === 1) {

            $this->templates
                ->clearDefaultTemplate(
                    $organizationId,
                    $id
                );
        }


        $updated =
            $this->templates->update(
                $id,
                $this->buildTemplateData(
                    $data
                )
            );


        $db->transComplete();


        if (
            $db->transStatus() === false ||
            !$updated
        ) {

            throw new RuntimeException(
                'Unable to update appraisal template.'
            );
        }
    }


    public function deleteTemplate(
        int $id
    ): void {

        $template =
            $this->templates->find($id);


        if (!$template) {

            throw new RuntimeException(
                'Appraisal template not found.'
            );
        }


        /*
         * A template cannot be deleted
         * once it has been used by an appraisal.
         */
        $appraisalExists =
            db_connect()

            ->table('appraisals')

            ->where(
                'template_id',
                $id
            )

            ->countAllResults() > 0;


        if ($appraisalExists) {

            throw new RuntimeException(
                'This template cannot be deleted because it is already used in an appraisal.'
            );
        }


        if (
            !$this->templates->delete($id)
        ) {

            throw new RuntimeException(
                'Unable to delete appraisal template.'
            );
        }
    }


    protected function buildTemplateData(
        array $data
    ): array {

        return [

            'organization_id' =>
            (int) $data['organization_id'],

            'template_name' =>
            trim(
                $data['template_name']
            ),

            'description' =>
            !empty($data['description'])
                ? trim(
                    $data['description']
                )
                : null,

            'is_default' =>
            !empty($data['is_default'])
                ? 1
                : 0,

            'status' =>
            $data['status']
                ?? 'active',
        ];
    }


    protected function validateTemplateData(
        array $data
    ): array {

        $errors = [];


        if (
            empty($data['organization_id']) ||
            !is_numeric(
                $data['organization_id']
            )
        ) {

            $errors['organization_id'] =
                'Organization is required.';
        }


        if (
            empty($data['template_name']) ||
            trim(
                $data['template_name']
            ) === ''
        ) {

            $errors['template_name'] =
                'Template name is required.';
        }


        if (
            !empty($data['template_name']) &&
            strlen(
                trim(
                    $data['template_name']
                )
            ) > 150
        ) {

            $errors['template_name'] =
                'Template name cannot exceed 150 characters.';
        }


        $allowedStatuses = [
            'active',
            'inactive'
        ];


        if (
            !empty($data['status']) &&
            !in_array(
                $data['status'],
                $allowedStatuses,
                true
            )
        ) {

            $errors['status'] =
                'Invalid template status.';
        }


        return $errors;
    }


    protected function validateOrganization(int $organizationId): void
    {
        if ($organizationId <= 0) {
            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Organization is required.'
                ])
            );
        }

        $organization =
            db_connect()
            ->table('organizations')
            ->select(['id', 'status'])
            ->where('id', $organizationId)
            ->get()
            ->getRowArray();

        if (!$organization) {
            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Selected organization is invalid.'
                ])
            );
        }

        if (($organization['status'] ?? null) !== 'active') {
            throw new InvalidArgumentException(
                json_encode([
                    'organization_id' =>
                    'Selected organization is inactive.'

                ])
            );
        }
    }

    public function getBuilderData(
        int $templateId
    ): array {

        $template =
            $this->templates->getTemplate(
                $templateId
            );

        if (!$template) {

            throw new RuntimeException(
                'Appraisal template not found.'
            );
        }


        $sections =
            $this->sections
            ->getSectionsByTemplate(
                $templateId
            );


        foreach ($sections as &$section) {

            $section['questions'] =
                $this->questions
                ->getQuestionsBySection(
                    (int) $section['id']
                );
        }

        unset($section);


        return [

            'template' =>
            $template,

            'sections' =>
            $sections,
        ];
    }

    public function createSection(
        int $templateId,
        array $data
    ): int {

        $template =
            $this->templates->find(
                $templateId
            );

        if (!$template) {

            throw new RuntimeException(
                'Appraisal template not found.'
            );
        }


        $sectionName =
            trim(
                $data['section_name']
                    ?? ''
            );


        if ($sectionName === '') {

            throw new InvalidArgumentException(
                json_encode([

                    'section_name' =>
                    'Section name is required.'

                ])
            );
        }


        if (
            mb_strlen(
                $sectionName
            ) > 150
        ) {

            throw new InvalidArgumentException(
                json_encode([

                    'section_name' =>
                    'Section name cannot exceed 150 characters.'

                ])
            );
        }


        $sectionId =
            $this->sections->insert(
                [

                    'template_id' =>
                    $templateId,

                    'section_name' =>
                    $sectionName,

                    'sort_order' =>
                    $this->sections
                        ->getNextSortOrder(
                            $templateId
                        ),

                ],
                true
            );


        if (!$sectionId) {

            throw new RuntimeException(
                'Unable to create section.'
            );
        }


        return (int) $sectionId;
    }

    public function updateSection(
        int $sectionId,
        array $data
    ): void {

        $section =
            $this->sections->find(
                $sectionId
            );

        if (!$section) {

            throw new RuntimeException(
                'Section not found.'
            );
        }


        $sectionName =
            trim(
                $data['section_name']
                    ?? ''
            );


        if ($sectionName === '') {

            throw new InvalidArgumentException(
                json_encode([

                    'section_name' =>
                    'Section name is required.'

                ])
            );
        }


        if (
            mb_strlen(
                $sectionName
            ) > 150
        ) {

            throw new InvalidArgumentException(
                json_encode([

                    'section_name' =>
                    'Section name cannot exceed 150 characters.'

                ])
            );
        }


        if (
            !$this->sections->update(
                $sectionId,
                [

                    'section_name' =>
                    $sectionName

                ]
            )
        ) {

            throw new RuntimeException(
                'Unable to update section.'
            );
        }
    }

    public function deleteSection(
        int $sectionId
    ): void {

        $section =
            $this->sections->find(
                $sectionId
            );

        if (!$section) {

            throw new RuntimeException(
                'Section not found.'
            );
        }


        $db =
            db_connect();

        $db->transStart();


        $deleted =
            $this->sections->delete(
                $sectionId
            );


        if (!$deleted) {

            $db->transRollback();

            throw new RuntimeException(
                'Unable to delete section.'
            );
        }


        $this->normalizeSectionOrder(
            (int) $section['template_id']
        );


        $db->transComplete();


        if (
            !$db->transStatus()
        ) {

            throw new RuntimeException(
                'Unable to delete section.'
            );
        }
    }

    public function reorderSections(
        int $templateId,
        array $sectionIds
    ): void {

        $template =
            $this->templates->find(
                $templateId
            );

        if (!$template) {

            throw new RuntimeException(
                'Appraisal template not found.'
            );
        }


        $orderedIds =
            array_values(
                array_map(
                    'intval',
                    $sectionIds
                )
            );


        if (empty($orderedIds)) {

            throw new InvalidArgumentException(
                'Section ordering data is required.'
            );
        }


        if (
            count($orderedIds)
            !==
            count(
                array_unique(
                    $orderedIds
                )
            )
        ) {

            throw new InvalidArgumentException(
                'Duplicate sections found in ordering data.'
            );
        }


        $existingSections =
            $this->sections
            ->where(
                'template_id',
                $templateId
            )
            ->findAll();


        $existingIds =
            array_map(
                static fn($section) =>
                (int) $section['id'],

                $existingSections
            );


        $checkSubmitted =
            $orderedIds;

        $checkExisting =
            $existingIds;


        sort($checkSubmitted);

        sort($checkExisting);


        if (
            $checkSubmitted
            !==
            $checkExisting
        ) {

            throw new InvalidArgumentException(
                'Invalid section ordering data.'
            );
        }


        $db =
            db_connect();

        $db->transStart();


        foreach (
            $orderedIds
            as $index => $sectionId
        ) {

            $this->sections->update(
                $sectionId,
                [

                    'sort_order' =>
                    $index + 1

                ]
            );
        }


        $db->transComplete();


        if (
            !$db->transStatus()
        ) {

            throw new RuntimeException(
                'Unable to reorder sections.'
            );
        }
    }

    public function createQuestion(
        int $sectionId,
        array $data
    ): int {

        $section =
            $this->sections->find(
                $sectionId
            );

        if (!$section) {

            throw new RuntimeException(
                'Section not found.'
            );
        }


        $question =
            trim(
                $data['question']
                    ?? ''
            );


        if ($question === '') {

            throw new InvalidArgumentException(
                json_encode([

                    'question' =>
                    'Question is required.'

                ])
            );
        }


        $allowedAnswerTypes = [

            'rating',

            'text',

            'number',

            'yes_no',
        ];


        $answerType =
            trim(
                $data['answer_type']
                    ?? 'rating'
            );


        if (
            !in_array(
                $answerType,
                $allowedAnswerTypes,
                true
            )
        ) {

            throw new InvalidArgumentException(
                json_encode([

                    'answer_type' =>
                    'Invalid answer type.'

                ])
            );
        }


        $isRequired =
            !empty($data['is_required'])
            ? 1
            : 0;


        $questionId =
            $this->questions->insert(
                [

                    'section_id' =>
                    $sectionId,

                    'question' =>
                    $question,

                    'answer_type' =>
                    $answerType,

                    'is_required' =>
                    $isRequired,

                    'sort_order' =>
                    $this->questions
                        ->getNextSortOrder(
                            $sectionId
                        ),

                ],
                true
            );


        if (!$questionId) {

            throw new RuntimeException(
                'Unable to create question.'
            );
        }


        return (int) $questionId;
    }

    public function updateQuestion(
        int $questionId,
        array $data
    ): void {

        $questionRecord =
            $this->questions->find(
                $questionId
            );

        if (!$questionRecord) {

            throw new RuntimeException(
                'Question not found.'
            );
        }


        $question =
            trim(
                $data['question']
                    ?? ''
            );


        if ($question === '') {

            throw new InvalidArgumentException(
                json_encode([

                    'question' =>
                    'Question is required.'

                ])
            );
        }


        $allowedAnswerTypes = [

            'rating',

            'text',

            'number',

            'yes_no',
        ];


        $answerType =
            trim(
                $data['answer_type']
                    ?? 'rating'
            );


        if (
            !in_array(
                $answerType,
                $allowedAnswerTypes,
                true
            )
        ) {

            throw new InvalidArgumentException(
                json_encode([

                    'answer_type' =>
                    'Invalid answer type.'

                ])
            );
        }


        $isRequired =
            !empty($data['is_required'])
            ? 1
            : 0;


        if (
            !$this->questions->update(
                $questionId,
                [

                    'question' =>
                    $question,

                    'answer_type' =>
                    $answerType,

                    'is_required' =>
                    $isRequired,

                ]
            )
        ) {

            throw new RuntimeException(
                'Unable to update question.'
            );
        }
    }

    public function deleteQuestion(
        int $questionId
    ): void {

        $question =
            $this->questions->find(
                $questionId
            );

        if (!$question) {

            throw new RuntimeException(
                'Question not found.'
            );
        }


        $sectionId =
            (int) $question['section_id'];


        $db =
            db_connect();

        $db->transStart();


        $deleted =
            $this->questions->delete(
                $questionId
            );


        if (!$deleted) {

            $db->transRollback();

            throw new RuntimeException(
                'Unable to delete question.'
            );
        }


        $this->normalizeQuestionOrder(
            $sectionId
        );


        $db->transComplete();


        if (
            !$db->transStatus()
        ) {

            throw new RuntimeException(
                'Unable to delete question.'
            );
        }
    }

    public function reorderQuestions(
        int $sectionId,
        array $questionIds
    ): void {

        $section =
            $this->sections->find(
                $sectionId
            );

        if (!$section) {

            throw new RuntimeException(
                'Section not found.'
            );
        }


        $orderedIds =
            array_values(
                array_map(
                    'intval',
                    $questionIds
                )
            );


        $existingQuestions =
            $this->questions
            ->where(
                'section_id',
                $sectionId
            )
            ->findAll();


        $existingIds =
            array_map(
                static fn($question) =>
                (int) $question['id'],

                $existingQuestions
            );


        if (
            count($orderedIds)
            !==
            count($existingIds)
        ) {

            throw new InvalidArgumentException(
                'Invalid question ordering data.'
            );
        }


        if (
            count($orderedIds)
            !==
            count(
                array_unique(
                    $orderedIds
                )
            )
        ) {

            throw new InvalidArgumentException(
                'Duplicate questions found in ordering data.'
            );
        }


        $checkSubmitted =
            $orderedIds;

        $checkExisting =
            $existingIds;


        sort($checkSubmitted);

        sort($checkExisting);


        if (
            $checkSubmitted
            !==
            $checkExisting
        ) {

            throw new InvalidArgumentException(
                'Invalid question ordering data.'
            );
        }


        $db =
            db_connect();

        $db->transStart();


        foreach (
            $orderedIds
            as $index => $questionId
        ) {

            $this->questions->update(
                $questionId,
                [

                    'sort_order' =>
                    $index + 1

                ]
            );
        }


        $db->transComplete();


        if (
            !$db->transStatus()
        ) {

            throw new RuntimeException(
                'Unable to reorder questions.'
            );
        }
    }

    protected function normalizeSectionOrder(
        int $templateId
    ): void {

        $sections =
            $this->sections
            ->where(
                'template_id',
                $templateId
            )
            ->orderBy(
                'sort_order',
                'ASC'
            )
            ->orderBy(
                'id',
                'ASC'
            )
            ->findAll();


        foreach (
            $sections
            as $index => $section
        ) {

            $this->sections->update(
                $section['id'],
                [

                    'sort_order' =>
                    $index + 1

                ]
            );
        }
    }

    protected function normalizeQuestionOrder(
        int $sectionId
    ): void {

        $questions =
            $this->questions
            ->where(
                'section_id',
                $sectionId
            )
            ->orderBy(
                'sort_order',
                'ASC'
            )
            ->orderBy(
                'id',
                'ASC'
            )
            ->findAll();


        foreach (
            $questions
            as $index => $question
        ) {

            $this->questions->update(
                $question['id'],
                [

                    'sort_order' =>
                    $index + 1

                ]
            );
        }
    }
}
