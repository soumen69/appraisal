<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AppraisalTemplateService;

use InvalidArgumentException;
use Throwable;

class AppraisalTemplateController extends BaseController
{
    protected AppraisalTemplateService
        $appraisalTemplateService;


    public function __construct()
    {
        $this->appraisalTemplateService =
            new AppraisalTemplateService();
    }

    public function index()
    {
        return view(
            'appraisal/templates/index',
            [

                'title' =>
                'Appraisal Templates',

                'page_title' =>
                'Appraisal Templates',

                'page_subtitle' =>
                'Create and manage appraisal templates.'

            ]
        );
    }


    public function list()
    {
        try {

            $data =
                $this
                ->appraisalTemplateService
                ->getTemplates(
                    $this->request->getGet()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'data' => $data

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template list error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to load appraisal templates.'

                ]);
        }
    }


    public function edit(int $id)
    {
        try {

            $template =
                $this
                ->appraisalTemplateService
                ->getTemplate($id);


            if (!$template) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([

                        'success' => false,

                        'message' =>
                        'Appraisal template not found.'

                    ]);
            }


            return $this->response
                ->setJSON([

                    'success' => true,

                    'data' => $template

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template edit error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to load appraisal template.'

                ]);
        }
    }


    public function store()
    {
        try {

            $id =
                $this
                ->appraisalTemplateService
                ->createTemplate(
                    $this->request->getPost()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Appraisal template created successfully.',

                    'data' => [

                        'id' => $id

                    ]

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->validationErrorResponse(
                $e
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template create error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to create appraisal template.'

                ]);
        }
    }


    public function update(int $id)
    {
        try {

            $this
                ->appraisalTemplateService
                ->updateTemplate(
                    $id,
                    $this->request->getPost()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Appraisal template updated successfully.'

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->validationErrorResponse(
                $e
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template update error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to update appraisal template.'

                ]);
        }
    }


    public function delete(int $id)
    {
        try {

            $this
                ->appraisalTemplateService
                ->deleteTemplate($id);


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Appraisal template deleted successfully.'

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template delete error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function details(int $id)
    {
        try {

            $template =
                $this
                ->appraisalTemplateService
                ->getTemplate($id);


            if (!$template) {

                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([

                        'success' => false,

                        'message' =>
                        'Appraisal template not found.'

                    ]);
            }


            return $this->response
                ->setJSON([

                    'success' => true,

                    'data' =>
                    $template

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template details error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to load appraisal template details.'

                ]);
        }
    }

    public function builder(int $id)
    {
        try {

            $template =
                $this
                ->appraisalTemplateService
                ->getTemplate($id);


            if (!$template) {

                return redirect()
                    ->to(
                        base_url(
                            'templates'
                        )
                    )
                    ->with(
                        'error',
                        'Appraisal template not found.'
                    );
            }


            return view(
                'appraisal/templates/builder',
                [

                    'title' =>
                    'Template Builder',

                    'page_title' =>
                    'Template Builder',

                    'page_subtitle' =>
                    'Configure sections and questions.',

                    'template' =>
                    $template

                ]
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template builder page error: ' .
                    $e->getMessage()
            );


            return redirect()
                ->to(
                    base_url(
                        'templates'
                    )
                )
                ->with(
                    'error',
                    'Unable to load template builder.'
                );
        }
    }


    public function builderData(int $id)
    {
        try {

            $data =
                $this
                ->appraisalTemplateService
                ->getBuilderData($id);


            return $this->response
                ->setJSON([

                    'success' => true,

                    'data' => $data

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Appraisal template builder data error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }

    public function storeSection(int $templateId)
    {
        try {

            $id =
                $this
                ->appraisalTemplateService
                ->createSection(
                    $templateId,
                    $this->request->getPost()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Section created successfully.',

                    'data' => [

                        'id' => $id

                    ]

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->validationErrorResponse(
                $e
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Section create error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function updateSection(int $id)
    {
        try {

            $this
                ->appraisalTemplateService
                ->updateSection(
                    $id,
                    $this->request->getPost()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Section updated successfully.'

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->validationErrorResponse(
                $e
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Section update error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function deleteSection(int $id)
    {
        try {

            $this
                ->appraisalTemplateService
                ->deleteSection($id);


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Section deleted successfully.'

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Section delete error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function reorderSections(int $templateId)
    {
        try {

            $sectionIds =
                $this->request->getPost(
                    'section_ids'
                )
                ?? [];


            $this
                ->appraisalTemplateService
                ->reorderSections(
                    $templateId,
                    $sectionIds
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Sections reordered successfully.'

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Section reorder error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to reorder sections.'

                ]);
        }
    }

    public function storeQuestion(int $sectionId)
    {
        try {

            $id =
                $this
                ->appraisalTemplateService
                ->createQuestion(
                    $sectionId,
                    $this->request->getPost()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Question created successfully.',

                    'data' => [

                        'id' => $id

                    ]

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->validationErrorResponse(
                $e
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Question create error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function updateQuestion(int $id)
    {
        try {

            $this
                ->appraisalTemplateService
                ->updateQuestion(
                    $id,
                    $this->request->getPost()
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Question updated successfully.'

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->validationErrorResponse(
                $e
            );
        } catch (Throwable $e) {

            log_message(
                'error',
                'Question update error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function deleteQuestion(int $id)
    {
        try {

            $this
                ->appraisalTemplateService
                ->deleteQuestion($id);


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Question deleted successfully.'

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Question delete error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        }
    }


    public function reorderQuestions(int $sectionId)
    {
        try {

            $questionIds =
                $this->request->getPost(
                    'question_ids'
                )
                ?? [];


            $this
                ->appraisalTemplateService
                ->reorderQuestions(
                    $sectionId,
                    $questionIds
                );


            return $this->response
                ->setJSON([

                    'success' => true,

                    'message' =>
                    'Questions reordered successfully.'

                ]);
        } catch (InvalidArgumentException $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    $e->getMessage()

                ]);
        } catch (Throwable $e) {

            log_message(
                'error',
                'Question reorder error: ' .
                    $e->getMessage()
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([

                    'success' => false,

                    'message' =>
                    'Unable to reorder questions.'

                ]);
        }
    }

    protected function validationErrorResponse(InvalidArgumentException $e)
    {
        $errors =
            json_decode(
                $e->getMessage(),
                true
            );


        return $this->response
            ->setStatusCode(422)
            ->setJSON([

                'success' => false,

                'message' =>
                is_array($errors)
                    ? 'Please correct the highlighted fields.'
                    : $e->getMessage(),

                'errors' =>
                is_array($errors)
                    ? $errors
                    : []

            ]);
    }
}
