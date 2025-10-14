<?php

namespace App\Controllers\Admin;

use App\Requests\EmailHistoryRequest;
use App\Models\EmailHistoryModel;
use App\Resources\EmailHistoryResource;
use App\Services\JwtAuthService;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class EmailHistoryController extends ResourceController {
  // use ResponseTrait; // Already in ResourceController
  use ResponseTrait;

  protected $model;
  protected $jwtService;
  protected $controllerName = 'Email History';

  public function __construct() {
    $this->model = new EmailHistoryModel();
    $this->jwtService = new JwtAuthService();
  }

  /**
   * Return an array of resource objects, themselves in array format
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function index() {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      // Validation
      $rules = [
        'page'   => 'permit_empty|integer|is_natural_no_zero',
        'limit'  => 'permit_empty|integer|is_natural_no_zero',
        'search' => 'permit_empty|max_length[100]',
      ];
      $messages = [
        'page.is_natural_no_zero'  => 'Page must be a positive number.',
        'limit.is_natural_no_zero' => 'Limit must be a positive number.',
        'search.max_length'        => 'Search term must not exceed 100 characters.',
      ];

      $validation = \Config\Services::validation();
      $validation->setRules($rules, $messages);
      $request = $this->request->getGet();
      if (!$validation->run($request)) {
        return $this->respond([
          'status' => false,
          'message' => 'Validation failed',
          'errors' => $validation->getErrors()
        ], 422);
      }

      $page  = isset($request['page']) ? (int) $request['page'] : 1;
      $limit = isset($request['limit']) ? (int) $request['limit'] : 10;
      $search = $request['search'] ?? "";
      $status = $request['status'] ?? "";

      $offset = ($page - 1) * $limit;

      $builder = $this->model;
      if (!empty($search)) {
        $builder->where('name', $search);
      }

      if ($status != '' && !is_null($status)) {
        $builder->where('status', $status);
      }

      $total = $builder->countAllResults(false);
      $email_histories = $builder
        ->orderBy('created_at', 'DESC')
        ->findAll($limit, $offset);

      $resource = EmailHistoryResource::collection($email_histories);
      return $this->respond([
        'status' => true,
        'data' => $resource,
        'pagination' => [
          'total' => $total,
          'limit' => $limit,
          'page' => $page,
          'pages' => ceil($total / $limit)
        ]
      ]);
    } catch (\Throwable $th) {
      $message = "EmailHistoryController.index: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'server error',
        // 'message' => $message,
      ]);
    }
  }

  /**
   * Return the properties of a resource object
   *
   * @param int|string|null $id
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function show($id = null) {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      $data = $this->model->find($id);
      if (!$data) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.not_found', ['name' => $this->controllerName])
        ], 404);
      }

      $resource = new EmailHistoryResource($data);
      return $this->respond($resource);
      return $this->respond([
        'status' => true,
        'data' => $resource,
      ]);
    } catch (\Throwable $th) {
      $message = "EmailHisotryController.show: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ]);
    }
  }

  /**
   * Create a new resource object, from "posted" parameters
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function create() {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      //Initial request
      $request = $this->request->getJSON(true) ?? [];
      //Validation
      $rules    = EmailHistoryRequest::rules();
      $messages = EmailHistoryRequest::messages();
      if (!$this->validateData($request, $rules, $messages)) {
        return $this->respond([
          'status' => false,
          'errors' => $this->validator->getErrors(),
        ], 422);
      }
      $request['status'] = 1;
      $request['resent_times'] = 1;
      $insertedId = $this->model->insert($request);
      if (!$insertedId) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.model_create', ['name' => $this->controllerName])
        ], 400);
      }

      $newRecord = $this->model->find($insertedId);
      $resource = new EmailHistoryResource($newRecord);

      return $this->respondCreated([
        'status' => true,
        'message' => lang('Common.success.model_create', ['name' => $this->controllerName]),
        'data' => $resource->get()
      ]);
    } catch (\Throwable $th) {
      $message = "EmailHistoryController.create: ";
      $message .= $th->getFile() . " ";
      $message .= $th->getLine() . " ";
      $message .= $th->getMessage() . " ";
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.',
        'message' => $message,
      ], 500);
    }
  }

  /**
   * Delete the designated resource object from the model
   *
   * @param int|string|null $id
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function delete($id = null) {
    $data = $this->model->find($id);
    if (!$data) {
      return $this->failNotFound('No email history found with id ' . $id);
    }

    $this->model->delete($id);

    return $this->respondDeleted(['id' => $id], 'Email history deleted successfully');
  }

  /**
   * Update an existing resource object
   *
   * @param int|string|null $id
   *
   * @return \CodeIgniter\HTTP\ResponseInterface
   */
  public function update($id = null) {
    try {
      //Authorization
      $auth = $this->jwtService->authenticateUser();
      if (!$auth['status']) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }
      $userInfo = (array) $auth['user_info'];
      $roleId = $userInfo['role_id'];
      if (!isAdmin($roleId)) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.no_authorize')
        ], 403);
      }

      //Initial request
      $request = $this->request->getJSON(true) ?? [];
      //Validation
      $rules    = EmailHistoryRequest::rules();
      $messages = EmailHistoryRequest::messages();
      if (!$this->validateData($request, $rules, $messages)) {
        return $this->respond([
          'status' => false,
          'errors' => $this->validator->getErrors(),
        ], 422);
      }

      $existingRecord = $this->model->find($id);
      if (!$existingRecord) {
        return $this->respond([
          'status' => false,
          'message' => lang('Common.error.not_found', ['name' => $this->controllerName])
        ], 404);
      }

      $this->model->update($id, $request);

      return $this->respond([
        'status' => true,
        'message' => lang('Common.success.model_update', ['name' => $this->controllerName])
      ]);
    } catch (\Throwable $th) {
      $message = "EmailHistoryController.update: ";
      $message .= $th->getFile() . " " . $th->getLine() . " " . $th->getMessage();
      log_message('error', $message);
      return $this->respond([
        'status' => false,
        'message' => 'An error occurred during processing. Please try again later.'
      ], 500);
    }
  }
}
